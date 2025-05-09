<?php

namespace App\Http\Controllers\Admin;

use Session;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Order::query();

       

            return DataTables::of($query)
                ->addColumn('order_no', function ($order) {
                    return $order->order_no;
                })
                ->addColumn('customer', function ($order) {
                    return "
                        <div> $order->customer_name </div>
                        <div> $order->customer_email </div>
                        <div> $order->customer_phone </div>
                    ";
                })
                ->addColumn('shipping_address', function ($order) {
                    return $order->shipping_address;
                })
                ->addColumn('description', function ($order) {
                    return $order->description;
                })
                ->addColumn('subtotal', function ($order) {
                    return $order->subtotal;
                })
                ->addColumn('total', function ($order) {
                    return 'Rs ' . $order->total;
                })
                ->addColumn('status', function ($order) {
                    return '
                    <form action="' . route('admin.order.status.update', ['id' => $order->id]) . '" 
                          method="POST" 
                          class="status-form inline-block relative">
                        ' . csrf_field() . '
                        ' . method_field('PUT') . '
                        
                        <!-- Status Badge -->
                        <span class="status_badge inline-block text-white text-xs font-semibold px-2 py-1 rounded cursor-pointer"
                              style="background-color: ' . $this->getStatusColor($order->status) . ';" 
                              data-id="' . $order->id . '">
                            ' . ucfirst($order->status) . '
                        </span>
                
                        <!-- Status Select -->
                        <select name="status" class="status_select hidden text-xs p-1 border rounded">
                            <option value="pending" ' . ($order->status == "pending" ? "selected" : "") . '>Pending</option>
                            <option value="processing" ' . ($order->status == "processing" ? "selected" : "") . '>Processing</option>
                            <option value="completed" ' . ($order->status == "completed" ? "selected" : "") . '>Completed</option>
                            <option value="delivered" ' . ($order->status == "delivered" ? "selected" : "") . '>Delivered</option>
                            <option value="cancelled" ' . ($order->status == "cancelled" ? "selected" : "") . '>Cancelled</option>
                        </select>
                    </form>
                <script>
                $(document).ready(function () {
                    $(".status_badge").hover(function () {
                        $(this).hide();
                        $(this).siblings(".status_select").removeClass("hidden").focus();
                    });

                    $(".status_select").on("mouseleave", function () {
                        $(this).addClass("hidden");
                        $(this).siblings(".status_badge").show();
                    });

                    $(".status_select").change(function () {
                        $(this).closest("form").submit();
                    });
                });
            </script>
                    
                    ';
                })


                ->addColumn('action', function ($order) {
                    return '
                        <div class="flex space-x-2">
                            <a href="' . route('admin.order.delete', $order->id) . '" 
                            onclick="return confirm(\'Are you sure you want to delete this order?\')"
                            class=" py-1 text-xs" style="color: #EF4444; font-size: 13px;">
                            <i class="fa-regular fa-trash-can"></i>
                            </a>
                            <a href="'. route('admin.order.edit', $order->id) .'" data-id="' . $order->id . '" class=" px-0 py-1 text-xs" style="color: green; font-size: 13px;">
                                <i class="fa-regular fa-edit"></i>
                            </a>
                            <a href="#" data-id="' . $order->id . '" class="view-order px-3 py-1 text-xs" style="color: green; font-size: 13px;">
                                <i class="fa-regular fa-eye"></i>
                            </a>
                        </div>
                    ';
                })
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && !empty($request->input('search.value'))) {
                        $search = $request->input('search.value');
                        $query->where(function ($q) use ($search) {
                            $q->where('order_no', 'like', "%{$search}%")
                            ->orWhere('customer_name', 'like', "%{$search}%")
                            ->orWhere('customer_email', 'like', "%{$search}%")
                            ->orWhere('customer_phone', 'like', "%{$search}%")
                            ->orWhere('shipping_address', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%")
                            ->orWhere('status', 'like', "%{$search}%")
                            ->orWhereHas('orderItems.product', function ($q) use ($search) {
                                $q->where('name', 'like', "%{$search}%");
                            });
                        });
                    }
                })
                ->rawColumns(['customer', 'status', 'action'])
                ->make(true);
        }

        return view('admin.order.index');
    }
    
    public function show_details($id)
    {
        $order = Order::findOrFail($id);

        $order_items = OrderItem::with('product')->where('order_id', $id)->get();
        
        return response()->json([
            'order_no' => $order->order_no,
            'customer_name' => $order->customer_name,
            'customer_email' => $order->customer_email,
            'customer_phone' => $order->customer_phone,
            'shipping_address' => $order->shipping_address,
            'description' => $order->description,
            'subtotal' => $order->subtotal,
            'total' => $order->total,
            'status' => $order->status,
            'order_items' => $order_items,
            'created_at' => $order->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $order->updated_at->format('Y-m-d H:i:s'),
            // Add more fields as needed
        ]);
    }
    public function getOrderItems($id)
    {
        $order = Order::with('orderItems')->find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        return response()->json(['order_items' => $order->orderItems]);
    }

    public function getStatusColor($status)
    {
        $colors = [
            "pending" => "#3B82F6",
            "processing" => "#6B7280",
            "completed" => "#F59E0B",
            "delivered" => "#10B981",
            "cancelled" => "#EF4444"
        ];
        return $colors[$status] ?? "#000";
    }
    public function status_update(Request $request, $id)
    {
        // dd($id);
        $order = Order::find($id);
        $order->status = $request->status;
        $order->save();
        return redirect()->route('admin.order.index');
    }

    public function create()
    {
        $order_no = Order::next_order_no();
        $products = Product::get();
        return view('admin.order.create', compact("products", "order_no"));
    }
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'customer_name' => 'required',
            'customer_email' => 'nullable',
            'customer_phone' => 'required',
            'description' => 'nullable',
            'status' => 'required|in:pending,processing,completed,delivered,cancelled',
            'shipping_address' => 'required',
            'items' => 'required|array',
            'total' => 'required|numeric'
        ]);
        $order = new Order();
        $order->customer_name = $request->customer_name;
        $order->customer_email = $request->customer_email;
        $order->customer_phone = $request->customer_phone;
        $order->description = $request->description;
        $order->shipping_address = $request->shipping_address;
        $order->status = $request->status;
        $order->total = $request->total;
        $order->subtotal = $request->total;
        $order->save();

        $insertedOrderId = $order->id;


        foreach ($request->items as $item) {
            $order_item = new OrderItem();
            $order_item->order_id = $insertedOrderId;
            $order_item->product_id = $item['id'];
            $order_item->quantity = $item['quantity'];
            $order_item->price = $item['price'];
            $order_item->color = $item['color'];
            $order_item->size = $item['size'];
            $order_item->description = $item['description'];
            $order_item->save();
        }
        return redirect()->route('admin.order.create')->with('message', 'Order Created Successfully!');
    }
    public function edit($id)
    {
        $order = Order::with('orderItems.product')->findOrFail($id);
        $products = Product::get();
        return view('admin.order.edit', compact('order', 'products'));
    }
    public function update(Request $request, $id)
    {
        // dd($request->all());
        $request->validate([
            'customer_name' => 'required',
            'customer_email' => 'nullable',
            'customer_phone' => 'required',
            'description' => 'nullable',
            'status' => 'required|in:pending,processing,completed,delivered,cancelled',
            'shipping_address' => 'required',
            'total' => 'required|numeric',
            'items' => 'required|array',
        ]);
        $order = Order::find($id);
        $order->customer_name = $request->customer_name;
        $order->customer_email = $request->customer_email;
        $order->customer_phone = $request->customer_phone;
        $order->description = $request->description;
        $order->shipping_address = $request->shipping_address;
        $order->status = $request->status;
        $order->total = $request->total;
        $order->subtotal = $request->total;
        $order->save();

        // Delete existing order items
        OrderItem::where('order_id', $id)->delete();
        // Insert new order items
        $insertedOrderId = $order->id;

        foreach ($request->items as $item) {
            $order_item = new OrderItem();
            $order_item->order_id = $insertedOrderId;
            $order_item->product_id = $item['id'];
            $order_item->quantity = $item['quantity'];
            $order_item->price = $item['price'];
            $order_item->color = $item['color'];
            $order_item->size = $item['size'];
            $order_item->description = $item['description'];
            $order_item->save();
        }

        return redirect()->route('admin.order.index')->with('message', 'Order Updated Successfully!');
    }
    public function delete($id)
    {
        Order::where('id', $id)->delete();
        OrderItem::where('order_id', $id)->delete();
        return redirect()->route('admin.order.index');
    }

    public function ordered_items(Request $request)
    {
        if ($request->ajax()) {
            $query = OrderItem::with(['order', 'product'])
                ->whereHas('order', function ($q) {
                    $q->where('status', '!=', Order::STATUS_DELIVERED)
                    ->where('status', '!=', Order::STATUS_CANCELLED);
                })
                ->when($request->filled('status'), function ($query) use ($request) {
                    $query->whereHas('order', function ($q) use ($request) {
                        $q->where('status', $request->status);
                    });
                })
                ->when($request->filled('product_ids'), function ($query) use ($request) {
                    $query->whereIn('product_id', $request->product_ids);
                })
                ->when($request->filled('color'), function ($query) use ($request) {
                    $query->where('color', $request->color);
                })
                ->when($request->filled('date_from') && $request->filled('date_to'), function ($query) use ($request) {
                    $query->whereHas('order', function ($q) use ($request) {
                        $q->whereBetween('created_at', [$request->date_from, $request->date_to]);
                    });
                });
            

            return DataTables::of($query)
                ->addColumn('checkbox', function ($row) {
                    return '<input type="checkbox" class="checkbox" data-order-item-id="' . $row->id . '">';
                })
                ->addColumn('order_no', function ($row) {
                    return $row->order->order_no ?? 'N/A';
                })
                ->addColumn('customer_name', function ($row) {
                    return $row->order->customer_name ?? 'N/A';
                })
                ->addColumn('status', function ($row) {
                    return $row->order->status ?? 'N/A';
                })
                ->addColumn('assigned', function ($row) {
                    return empty($row->tags) ? 'N/A' : $row->tags;
                })
                ->addColumn('product_name', function ($row) {
                    return $row->product->name ?? 'N/A';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->order->created_at->format('Y-m-d');
                })
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && !empty($request->input('search.value'))) {
                        $search = $request->input('search.value');
                        $query->where(function ($q) use ($search) {
                            $q->where('quantity', 'like', "%{$search}%")
                              ->orWhere('price', 'like', "%{$search}%")
                              ->orWhere('color', 'like', "%{$search}%")
                              ->orWhere('tags', 'like', "%{$search}%")
                              ->orWhereHas('order', function ($q) use ($search) {
                                  $q->where('order_no', 'like', "%{$search}%")
                                    ->orWhere('customer_name', 'like', "%{$search}%")
                                    ->orWhere('status', 'like', "%{$search}%");
                              })
                              ->orWhereHas('product', function ($q) use ($search) {
                                  $q->where('name', 'like', "%{$search}%");
                              });
                        });
                    }
                })
                ->orderColumn('checkbox', false)
                ->rawColumns(['checkbox'])
                ->with([
                    'ordered_product_ids' => $query->pluck('id')->unique(),
                ])
                ->make(true);
        }
    
        $products = Product::get();
        return view('admin.ordered_items.index', compact('products'));
    }

    public function generateWorkerPdf(Request $request)
    {
        $request->validate([
            'worker_name' => 'required|string',
            'selected_items' => 'required|string'
        ]);

        $selectedIds = json_decode($request->selected_items);
        $workerName = $request->worker_name;

        $orderItems = OrderItem::with(['product', 'order'])
            ->whereIn('id', $selectedIds)
            ->get();
        foreach ($orderItems as  $order_item) {
            $order_item->tags = $workerName;
            $order_item->save();
        }
        // dd($orderItems);
        $pdf = Pdf::loadView('admin.pdf.worker_items', [
            'worker_name' => $workerName,
            'date' => now()->format('Y-m-d'),
            'items' => $orderItems,
        ]);
        $pdf->setPaper([0, 0, 1500, 1350 ], 'landscape');;

        return $pdf->stream('worker-items-' . now()->format('Y-m-d') . '.pdf');
    }

    public function ordered_items_pdf(Request $request)
    {
        $request->validate([
            'selected_items' => 'required|string'
        ]);

        $selectedIds = json_decode($request->selected_items);

        $orderItems = OrderItem::with(['product', 'order'])
            ->whereIn('id', $selectedIds)
            ->get();

        $pdf = Pdf::loadView('admin.pdf.ordered_items', [
            'items' => $orderItems,
        ]);
        $pdf->setPaper([0, 0, 1500, 1350], 'landscape');;

        return $pdf->stream('ordered-items-' . now()->format('Y-m-d') . '.pdf');
    }
}
