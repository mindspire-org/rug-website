<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->latest();
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        $products = $query->paginate(20)->withQueryString();
        $categories = Category::all();
        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:200',
            'price'       => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'images.*'    => 'nullable|image|max:5120',
        ]);

        $data = $request->except(['images', 'colors', '_token']);
        $data['slug']   = Str::slug($request->name) . '-' . Str::random(4);
        $data['featured']     = $request->has('featured');
        $data['is_bestseller']= $request->has('is_bestseller');
        $data['is_new_arrival']= $request->has('is_new_arrival');

        $product = Product::create($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $i => $file) {
                $path = $file->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'path'       => $path,
                    'sort_order' => $i,
                    'is_primary' => $i === 0,
                ]);
            }
        }

        if ($request->filled('color_names')) {
            foreach ($request->color_names as $i => $name) {
                if (!empty($name) && !empty($request->color_hexes[$i])) {
                    ProductColor::create([
                        'product_id' => $product->id,
                        'color_name' => $name,
                        'color_hex'  => $request->color_hexes[$i],
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully!');
    }

    public function edit(Product $product)
    {
        $product->load('images', 'colors', 'sizes');
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'        => 'required|string|max:200',
            'price'       => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'images.*'    => 'nullable|image|max:5120',
        ]);

        $data = $request->except(['images', 'colors', '_token', '_method']);
        $data['featured']     = $request->has('featured');
        $data['is_bestseller']= $request->has('is_bestseller');
        $data['is_new_arrival']= $request->has('is_new_arrival');

        $product->update($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $i => $file) {
                $path = $file->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'path'       => $path,
                    'sort_order' => $product->images->count() + $i,
                    'is_primary' => $product->images->isEmpty() && $i === 0,
                ]);
            }
        }

        if ($request->filled('color_names')) {
            $product->colors()->delete();
            foreach ($request->color_names as $i => $name) {
                if (!empty($name) && !empty($request->color_hexes[$i])) {
                    ProductColor::create([
                        'product_id' => $product->id,
                        'color_name' => $name,
                        'color_hex'  => $request->color_hexes[$i],
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated!');
    }

    public function destroy(Product $product)
    {
        foreach ($product->images as $img) {
            Storage::disk('public')->delete($img->path);
        }
        $product->delete();
        return back()->with('success', 'Product deleted.');
    }
}
