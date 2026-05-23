<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaticPage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index()
    {
        $pages = StaticPage::orderBy('title')->paginate(10);
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'     => 'required|string|max:255',
            'slug'      => 'nullable|string|max:255|unique:static_pages,slug',
            'content'   => 'required|string',
            'is_active' => 'nullable|boolean',
        ]);

        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->title);
        
        // Ensure slug is unique
        $originalSlug = $slug;
        $counter = 1;
        while (StaticPage::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        StaticPage::create([
            'title'     => $request->title,
            'slug'      => $slug,
            'content'   => $request->content,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Halaman berhasil dibuat.');
    }

    public function edit($id)
    {
        $page = StaticPage::findOrFail($id);
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, $id)
    {
        $page = StaticPage::findOrFail($id);

        $request->validate([
            'title'     => 'required|string|max:255',
            'slug'      => 'nullable|string|max:255|unique:static_pages,slug,' . $page->id,
            'content'   => 'required|string',
            'is_active' => 'nullable|boolean',
        ]);

        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->title);

        $page->update([
            'title'     => $request->title,
            'slug'      => $slug,
            'content'   => $request->content,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Halaman berhasil diperbarui.');
    }

    public function destroy($id)
    {
        StaticPage::findOrFail($id)->delete();
        return redirect()->route('admin.pages.index')
            ->with('success', 'Halaman berhasil dihapus.');
    }
}
