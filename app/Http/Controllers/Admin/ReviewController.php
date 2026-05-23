<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['user', 'product', 'images'])->orderByDesc('created_at');

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        $reviews = $query->paginate(20);
        return view('admin.reviews.index', compact('reviews'));
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        
        // Remove images physically
        foreach ($review->images as $image) {
            $path = storage_path('app/public/' . $image->image_path);
            if (file_exists($path)) {
                unlink($path);
            }
            $image->delete();
        }

        $review->delete();

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Ulasan beserta fotonya berhasil dihapus.');
    }
}
