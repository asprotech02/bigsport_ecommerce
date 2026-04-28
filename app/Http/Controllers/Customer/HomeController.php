<?php

// PERBAIKAN: Namespace harus sesuai dengan struktur folder (tambah \Customer)
namespace App\Http\Controllers\Customer;

// Karena HomeController sekarang berada di subfolder, 
// kita harus mengimport Controller utama (Base Controller)
use App\Http\Controllers\Controller; 
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Ambil produk diskon + skus (untuk cek stok)
        $discountProducts = Product::with(['brand', 'category', 'skus', 'images' => function($query) {
                                        $query->where('is_primary', true);
                                    }])
                                    ->whereNotNull('discount_price')
                                    ->withAvg('reviews', 'rating')
                                    ->withCount('reviews')
                                    ->latest() 
                                    ->take(8) 
                                    ->get();

        // Ambil produk pilihan + skus (untuk cek stok)
        $featuredProducts = Product::with(['brand', 'category', 'skus', 'images' => function($query) {
                                        $query->where('is_primary', true);
                                    }])
                                    ->where('is_featured', true)
                                    ->withAvg('reviews', 'rating')
                                    ->withCount('reviews') 
                                    ->latest()
                                    ->take(8) 
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