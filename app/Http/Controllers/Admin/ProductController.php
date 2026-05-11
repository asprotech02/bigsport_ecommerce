<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with([
            'category',
            'subcategory',
            'brand'
        ])->latest()->get();

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        $subcategories = Subcategory::all();
        $brands = Brand::all();

        return view('admin.products.create', compact(
            'categories',
            'subcategories',
            'brands'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'subcategory_id' => 'required',
            'brand_id' => 'required',
            'gender' => 'required',
            'base_price' => 'required',
            'weight_gram' => 'required',
        ]);

        Product::create([
            'category_id' => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
            'brand_id' => $request->brand_id,
            'gender' => $request->gender,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'base_price' => $request->base_price,
            'discount_price' => $request->discount_price,
            'is_featured' => $request->is_featured ? 1 : 0,
            'weight_gram' => $request->weight_gram,
        ]);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product berhasil ditambahkan');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $subcategories = Subcategory::all();
        $brands = Brand::all();

        return view('admin.products.edit', compact(
            'product',
            'categories',
            'subcategories',
            'brands'
        ));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'subcategory_id' => 'required',
            'brand_id' => 'required',
            'gender' => 'required',
            'base_price' => 'required',
            'weight_gram' => 'required',
        ]);

        $product->update([
            'category_id' => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
            'brand_id' => $request->brand_id,
            'gender' => $request->gender,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'base_price' => $request->base_price,
            'discount_price' => $request->discount_price,
            'is_featured' => $request->is_featured ? 1 : 0,
            'weight_gram' => $request->weight_gram,
        ]);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product berhasil diupdate');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product berhasil dihapus');
    }
}