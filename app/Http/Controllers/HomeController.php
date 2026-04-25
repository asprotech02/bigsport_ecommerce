<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Ambil produk yang ada harga diskonnya
        $discountProducts = Product::with(['brand', 'images', 'reviews'])
                                ->whereNotNull('discount_price')
                                ->withAvg('reviews', 'rating')
                                ->withCount('reviews')
                                ->get();

        // Ambil produk yang ditandai sebagai 'Featured' (Produk Pilihan)
        $featuredProducts = Product::with(['brand', 'images', 'reviews'])
                                ->where('is_featured', true)
                                ->get();

        return view('pages.customer.home', [
            'discountProducts' => $discountProducts,
            'featuredProducts' => $featuredProducts,
            'gender' => 'Semua',
            'category' => 'Koleksi',
            'subcategory' => 'Terbaru'
        ]);
    }
}