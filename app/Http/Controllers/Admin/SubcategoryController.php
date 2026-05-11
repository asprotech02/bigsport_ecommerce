<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubcategoryController extends Controller
{
    public function index()
    {
        $subcategories = Subcategory::with('category')
            ->latest()
            ->get();

        return view('admin.subcategories.index', compact('subcategories'));
    }

    public function create()
    {
        $categories = Category::all();

        return view('admin.subcategories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required',
            'name' => 'required|unique:subcategories,name'
        ]);

        Subcategory::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return redirect()
            ->route('admin.subcategories.index')
            ->with('success', 'Subcategory berhasil ditambahkan');
    }

    public function edit(Subcategory $subcategory)
    {
        $categories = Category::all();

        return view('admin.subcategories.edit', compact('subcategory', 'categories'));
    }

    public function update(Request $request, Subcategory $subcategory)
    {
        $request->validate([
            'category_id' => 'required',
            'name' => 'required|unique:subcategories,name,' . $subcategory->id
        ]);

        $subcategory->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return redirect()
            ->route('admin.subcategories.index')
            ->with('success', 'Subcategory berhasil diupdate');
    }

    public function destroy(Subcategory $subcategory)
    {
        $subcategory->delete();

        return redirect()
            ->route('admin.subcategories.index')
            ->with('success', 'Subcategory berhasil dihapus');
    }
}