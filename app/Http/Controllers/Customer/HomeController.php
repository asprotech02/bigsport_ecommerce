<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller; 
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // PENTING: Tambahkan ini untuk sub-query Terlaris

class HomeController extends Controller
{
    public function index()
    {
        // 1. PRODUK DISKON (Diskon Terbesar, STOK > 0, dan Terbaru)
        $discountProducts = Product::with(['brand', 'category', 'skus', 'images' => function($query) {
                                        $query->where('is_primary', true);
                                    }])
                                    ->whereNotNull('discount_price')
                                    ->where('base_price', '>', 0) 
                                    ->whereHas('skus', function($query) {
                                        $query->where('stock', '>', 0); // Syarat Stok > 0
                                    })
                                    ->withAvg('reviews', 'rating')
                                    ->withCount('reviews')
                                    ->orderByRaw('((base_price - discount_price) / base_price) * 100 DESC') // Urutan 1: Diskon Terbesar
                                    ->latest() // Urutan 2: Terbaru
                                    ->take(8) 
                                    ->get();

        // 2. PRODUK PILIHAN (Featured, Stok > 0, Terlaris, dan Terbaru)
        $featuredProducts = Product::select('products.*') // Wajib ada jika pakai addSelect
                                    ->with(['brand', 'category', 'skus', 'images' => function($query) {
                                        $query->where('is_primary', true);
                                    }])
                                    ->where('is_featured', true) // Syarat: Produk Pilihan
                                    ->whereHas('skus', function($query) {
                                        $query->where('stock', '>', 0); // Syarat: Stok > 0
                                    })
                                    ->withAvg('reviews', 'rating')
                                    ->withCount('reviews') 
                                    // LOGIKA TERLARIS: Hitung total quantity terjual dari order_items
                                    ->addSelect(['total_sold' => DB::table('order_items')
                                        ->join('product_skus', 'order_items.product_sku_id', '=', 'product_skus.id')
                                        ->whereColumn('product_skus.product_id', 'products.id')
                                        ->selectRaw('IFNULL(SUM(order_items.quantity), 0)')
                                    ])
                                    ->orderBy('total_sold', 'DESC') // Urutkan 1: Paling banyak terjual
                                    ->latest() // Urutkan 2: Paling baru ditambahkan (Secondary sort)
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