<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlistItems = \App\Models\Wishlist::with(['product.brand', 'product.images' => function($q) {
            $q->where('is_primary', true);
        }])->where('user_id', auth()->id())->get();

        return view('pages.customer.wishlist', compact('wishlistItems'));
    }

    public function destroy($id)
    {
        \App\Models\Wishlist::where('user_id', auth()->id())->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Produk dihapus dari wishlist.');
    }


    public function toggle(Request $request)
{
    $productId = $request->product_id;
    $userId = auth()->id();

    // Cek apakah sudah ada di wishlist
    $wishlist = \App\Models\Wishlist::where('user_id', $userId)
                                    ->where('product_id', $productId)
                                    ->first();

    if ($wishlist) {
        $wishlist->delete();
        return response()->json(['status' => 'removed', 'message' => 'Dihapus dari wishlist']);
    } else {
        \App\Models\Wishlist::create([
            'user_id' => $userId,
            'product_id' => $productId
        ]);
        return response()->json(['status' => 'added', 'message' => 'Ditambahkan ke wishlist']);
    }
}
}
