<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

use function Pest\Laravel\patch;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Product::query();

            return DataTables::of($query)
                ->addColumn('image', function ($product) {
                    return '<img src="' . ($product->image ? asset($product->image) : 'https://placehold.co/400') . '" alt="' . $product->name . '" class="h-20 w-20 rounded object-cover">';
                })
                ->addColumn('price', function ($product) {
                    return 'Rs' . number_format($product->price, 2);
                })
                ->addColumn('action', function ($product) {
                    return '
                        <div class="flex space-x-2">
                            <a href="' . route('admin.product.edit', $product->id) . '" class="px-3 py-1 text-xs text-white bg-indigo-600 rounded-md hover:bg-indigo-700">Edit</a>
                        </div>
                    ';
                })
                ->rawColumns(['image', 'action'])
                ->make(true);
        }

        return view('admin.product.index');
    }
 
    public function create()
    {
        return view('admin.product.create');
    }

    public function store(Request $request)
    {

        try {

            $validated = $request->validate([
                'name' => 'required',
                'sku' => 'required|string|unique:products,sku',
                'price' => 'required|numeric',
                'color' => 'required',
                'size' => 'required',
                'description' => 'nullable',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            ]);

            if ($request->hasFile('image')) {
                $imageName =  Str::random(17) . '.' . $request->file('image')->extension();
                $path = $request->file('image')->storeAs('products', $imageName, 'public');
                $validated['image'] = 'storage/' . $path;
            }

            $product = Product::create($validated);

            return redirect()->back()->with('message', 'Product created successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Error: ' . $th->getMessage());
        }
    }

    public function edit($id)
    {
        $product = Product::find($id);
        return view('admin.product.create', compact('product'));
    }

    public function update(Request $request)
    {
        $product = Product::find($request->id);
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => [
                'required',
                'string',
                'max:100',
                Rule::unique('products', 'sku')->ignore($product->sku, 'sku')
            ],
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $validated = $request->except('image');
        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $imageName =  Str::random(17) . '.' . $request->file('image')->extension();
            $path = $request->file('image')->storeAs('products', $imageName, 'public');
            $validated['image'] = 'storage/' . $path;
        }

        $product->update($validated);

        return redirect()->route('admin.product.index')->with('message', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        // Delete the product image if exists
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('admin.product.index')->with('success', 'Product deleted successfully');
    }
}
