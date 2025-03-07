<?php

namespace App\Http\Controllers\Admin;

use Session;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
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
                ->addColumn('customer_name', function ($order) {
                    return $order->customer_name;
                })
                ->addColumn('customer_email', function ($order) {
                    return $order->customer_email;
                })
                ->addColumn('customer_phone', function ($order) {
                    return $order->customer_phone;
                })
                ->addColumn('shipping_address', function ($order) {
                    return $order->shipping_address;
                })
                ->addColumn('subtotal', function ($order) {
                    return $order->subtotal;
                })
                ->addColumn('total', function ($order) {
                    return 'Rs ' . $order->total;
                })
                ->addColumn('status', function ($order) {
                    return '
                    <form action="' . route('admin.order.status.update', ['id'=>$order->id]) . '" 
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
                            <a href="' . route('admin.order.delete', $order->id) . '" class="px-3 py-1 text-xs" style="color: #EF4444; font-size: 12px;"><i class="fa-solid fa-trash-can"></i></a>
                        </div>
                    ';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('admin.order.index');
    }
    public function getStatusColor($status){
        $colors = [
            "pending" => "#3B82F6",
            "processing" => "#6B7280",
            "completed" => "#F59E0B",
            "delivered" => "#10B981",
            "cancelled" => "#EF4444"
        ];
        return $colors[$status] ?? "#000";
    }
    public function status_update(Request $request, $id){
        // dd($id);
        $order = Order::find($id);
        $order->status = $request->status;
        $order->save();
        return redirect()->route('admin.order.index');
    }

    public function create()
    {
        $products = Product::get();
        return view('admin.order.create', compact("products"));
    }
    public function store(Request $request){
        // dd($request);
        $request->validate([
            'customer_name' => 'required',
            'customer_email' => 'required',
            'customer_phone' => 'required',
            'description' => 'required',
            'status' => 'required|in:pending,processing,completed,delivered,cancelled',
            'shipping_address' => 'required',
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
            $order_item->save();
        }
        return redirect()->route('admin.order.create')->with('message', 'Order Created Successfully!');
    }
    public function delete($id){
        Order::where('id', $id)->delete();
        OrderItem::where('order_id', $id)->delete();
        return redirect()->route('admin.order.index');
    }

}
