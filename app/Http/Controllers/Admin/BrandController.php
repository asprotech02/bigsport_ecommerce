<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::latest()->paginate(10);

        return view('admin.pages.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.pages.brands.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:brands,name'
        ]);

        Brand::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()
            ->route('admin.brands.index')
            ->with('success', 'Brand berhasil ditambahkan');
    }

    public function edit(Brand $brand)
    {
        return view('admin.pages.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|unique:brands,name,' . $brand->id
        ]);

        $brand->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()
            ->route('admin.brands.index')
            ->with('success', 'Brand berhasil diupdate');
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();

        return redirect()
            ->route('admin.brands.index')
            ->with('success', 'Brand berhasil dihapus');
    }
}