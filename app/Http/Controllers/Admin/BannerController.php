<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('type')->orderBy('order')->paginate(10);
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'       => 'required|in:slider,promo',
            'title'      => 'nullable|string|max:255',
            'subtitle'   => 'nullable|string|max:255',
            'image'      => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'link_url'   => 'nullable|string|max:255',
            'is_active'  => 'nullable|boolean',
            'order'      => 'nullable|integer',
        ]);

        $imagePath = $request->file('image')->store('banners', 'public');

        Banner::create([
            'type'       => $request->type,
            'title'      => $request->title,
            'subtitle'   => $request->subtitle,
            'image_path' => $imagePath,
            'link_url'   => $request->link_url,
            'is_active'  => $request->has('is_active'),
            'order'      => $request->order ?? 0,
        ]);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $banner = Banner::findOrFail($id);
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $request->validate([
            'type'       => 'required|in:slider,promo',
            'title'      => 'nullable|string|max:255',
            'subtitle'   => 'nullable|string|max:255',
            'image'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'link_url'   => 'nullable|string|max:255',
            'is_active'  => 'nullable|boolean',
            'order'      => 'nullable|integer',
        ]);

        if ($request->hasFile('image')) {
            // Delete old
            if (Storage::disk('public')->exists($banner->image_path)) {
                Storage::disk('public')->delete($banner->image_path);
            }
            $banner->image_path = $request->file('image')->store('banners', 'public');
        }

        $banner->type = $request->type;
        $banner->title = $request->title;
        $banner->subtitle = $request->subtitle;
        $banner->link_url = $request->link_url;
        $banner->is_active = $request->has('is_active');
        $banner->order = $request->order ?? 0;
        $banner->save();

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);
        if (Storage::disk('public')->exists($banner->image_path)) {
            Storage::disk('public')->delete($banner->image_path);
        }
        $banner->delete();

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner berhasil dihapus.');
    }
}
