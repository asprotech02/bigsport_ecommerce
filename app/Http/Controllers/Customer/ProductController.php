<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // 1. Tangkap semua kemungkinan parameter dari URL
        $type = $request->query('type');               // Untuk Sale/Featured
        $gender = $request->query('gender');           // Untuk Pria/Wanita/Anak
        $category = $request->query('category');       // Untuk Sepatu/Pakaian/Aksesoris
        $subcategory = $request->query('subcategory'); // Untuk Kaos/Sepak Bola/dll

        // 2. Query dasar (dengan relasi untuk mencegah N+1 Query)
        $query = Product::with(['brand', 'category', 'skus', 'images' => function($q) {
                        $q->where('is_primary', true);
                    }])
                    ->withAvg('reviews', 'rating')
                    ->withCount('reviews')
                    ->latest();

        $title = 'SEMUA PRODUK'; // Judul Default

        // 3. FILTER KASUS 1: Menu SALE atau FEATURED
        if ($type == 'sale') {
            $query->whereNotNull('discount_price');
            $title = 'PRODUK DISKON 🔥';
        } elseif ($type == 'featured') {
            $query->where('is_featured', true);
            $title = 'PRODUK PILIHAN ⚡';
        }

        // 4. FILTER KASUS 2: Berdasarkan Gender (Laki-laki / Perempuan / Anak)
        // Asumsi: Anda memiliki kolom 'gender' di tabel products
        if ($gender) {
            $query->where('gender', $gender);
            $title = 'KOLEKSI ' . strtoupper($gender);
        }

        // 5. FILTER KASUS 3: Berdasarkan Kategori Utama (Pakaian / Sepatu)
        // Asumsi: Anda memiliki tabel categories yang berelasi
        if ($category) {
            $query->whereHas('category', function($q) use ($category) {
                $q->where('name', $category);
            });
            $title .= ' - ' . strtoupper($category);
        }

        // 6. FILTER KASUS 4: Berdasarkan Sub-Kategori (Kaos / Jersey / Basket)
        // Asumsi: Anda memiliki kolom 'subcategory' di tabel products
        if ($subcategory) {
            $query->where('subcategory', $subcategory);
            $title .= ' - ' . strtoupper($subcategory);
        }

        // Tampilkan 12 produk per halaman
        $products = $query->paginate(12);

        return view('pages.customer.product', compact('products', 'title'));
    }
}