<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSku;

class NewProductsSeeder extends Seeder
{
    public function run(): void
    {
        $productsData = [
            [
                'category_name' => 'Sepatu',
                'subcategory_name' => 'Sepak Bola',
                'brand_name' => 'Nike',
                'gender' => 'Laki-laki',
                'name' => 'Nike Mercurial Vapor 15 Elite FG',
                'description' => "Kuasai lapangan dengan Nike Mercurial Vapor 15 Elite FG. Didesain khusus untuk performa tinggi, sepatu sepak bola ini dilengkapi dengan unit Zoom Air tiga perempat panjang yang diletakkan langsung ke pelat luar sol untuk memberikan sensasi dorongan ekstra responsif.\n\nMaterial Vaporposite+ premium membungkus kaki dengan pas, memberikan sentuhan bola yang natural dan presisi bahkan saat berlari dengan kecepatan tinggi. Direkomendasikan untuk lapangan rumput alami kering.",
                'weight_gram' => 180,
                'is_featured' => true,
                'image' => 'products/nike_mercurial_vapor.png',
                'skus' => [
                    ['size' => '40', 'color' => 'Merah/Hitam', 'base_price' => 2499000, 'discount_price' => 2249000, 'stock' => 15],
                    ['size' => '41', 'color' => 'Merah/Hitam', 'base_price' => 2499000, 'discount_price' => 2249000, 'stock' => 20],
                    ['size' => '42', 'color' => 'Merah/Hitam', 'base_price' => 2499000, 'discount_price' => 2249000, 'stock' => 12]
                ]
            ],
            [
                'category_name' => 'Sepatu',
                'subcategory_name' => 'Sepatu Lari',
                'brand_name' => 'Adidas',
                'gender' => 'Unisex',
                'name' => 'Adidas Ultraboost Light Running Shoes',
                'description' => "Rasakan kenyamanan berlari tanpa batas dengan Adidas Ultraboost Light. Memperkenalkan Ultraboost teringan yang pernah ada, sepatu lari ini didukung oleh generasi baru teknologi bantalan Boost (Light Boost) yang mengembalikan energi balik 10% lebih banyak di setiap langkah dibanding versi sebelumnya.\n\nUpper Primeknit+ yang lentur menyesuaikan dengan anatomi kaki, sementara Continental™ Rubber outsole memberikan cengkeraman maksimal di segala jenis permukaan jalan kering maupun basah.",
                'weight_gram' => 290,
                'is_featured' => true,
                'image' => 'products/adidas_ultraboost_light.png',
                'skus' => [
                    ['size' => '40', 'color' => 'Putih/Biru', 'base_price' => 3100000, 'discount_price' => 2790000, 'stock' => 10],
                    ['size' => '41', 'color' => 'Putih/Biru', 'base_price' => 3100000, 'discount_price' => 2790000, 'stock' => 15],
                    ['size' => '42', 'color' => 'Putih/Biru', 'base_price' => 3100000, 'discount_price' => 2790000, 'stock' => 8]
                ]
            ],
            [
                'category_name' => 'Sepatu',
                'subcategory_name' => 'Basket',
                'brand_name' => 'Puma',
                'gender' => 'Laki-laki',
                'name' => 'Puma Clyde All-Pro Court Shoes',
                'description' => "Dominasi area court dengan Puma Clyde All-Pro. Sepatu basket premium ini didesain khusus untuk mendukung kelincahan pemain dengan bantalan midsole ProFoam+ yang sangat responsif, meminimalkan getaran pendaratan sekaligus meningkatkan energi pantulan saat melompat.\n\nKonstruksi anyaman jala (mesh) yang bersirkulasi udara dipadukan dengan overlay sintetis untuk memberikan stabilitas lateral yang sangat tangguh.",
                'weight_gram' => 380,
                'is_featured' => false,
                'image' => 'products/puma_clyde_all_pro.png',
                'skus' => [
                    ['size' => '40', 'color' => 'Putih/Hitam/Merah', 'base_price' => 1899000, 'discount_price' => null, 'stock' => 12],
                    ['size' => '41', 'color' => 'Putih/Hitam/Merah', 'base_price' => 1899000, 'discount_price' => null, 'stock' => 18],
                    ['size' => '42', 'color' => 'Putih/Hitam/Merah', 'base_price' => 1899000, 'discount_price' => null, 'stock' => 10]
                ]
            ],
            [
                'category_name' => 'Sepatu',
                'subcategory_name' => 'Volly',
                'brand_name' => 'Ortuseight',
                'gender' => 'Unisex',
                'name' => 'Ortuseight Catalyst Weave Court Edition',
                'description' => "Sepatu voli premium Ortuseight Catalyst Weave dirancang khusus untuk mendukung performa lompatan dan pendaratan maksimal di lapangan voli indoor.\n\nDilengkapi dengan teknologi Ort-Curve yang meningkatkan kelincahan bermanuver, serta sol karet non-marking yang anti-slip sehingga Anda dapat bergerak bebas dan stabil sepanjang permainan.",
                'weight_gram' => 320,
                'is_featured' => false,
                'image' => 'products/ortuseight_catalyst_weave.png',
                'skus' => [
                    ['size' => '40', 'color' => 'Biru/Putih', 'base_price' => 549000, 'discount_price' => 499000, 'stock' => 20],
                    ['size' => '41', 'color' => 'Biru/Putih', 'base_price' => 549000, 'discount_price' => 499000, 'stock' => 25],
                    ['size' => '42', 'color' => 'Biru/Putih', 'base_price' => 549000, 'discount_price' => 499000, 'stock' => 15]
                ]
            ],
            [
                'category_name' => 'Sepatu',
                'subcategory_name' => 'Casual',
                'brand_name' => 'Specs',
                'gender' => 'Unisex',
                'name' => 'Specs Lightspeed Reborn Everyday Sneaker',
                'description' => "Tampil kasual namun tetap sporty dengan Specs Lightspeed Reborn. Terinspirasi dari seri sepatu bola Specs yang ikonik, versi kasual ini menawarkan kenyamanan luar biasa dengan sol EVA empuk dan upper kain bernapas.\n\nCocok digunakan untuk hangout, berjalan santai, bekerja ringan, maupun aktivitas sehari-hari.",
                'weight_gram' => 250,
                'is_featured' => false,
                'image' => 'products/specs_lightspeed_reborn.png',
                'skus' => [
                    ['size' => '40', 'color' => 'Abu-abu/Hijau', 'base_price' => 629000, 'discount_price' => 569000, 'stock' => 15],
                    ['size' => '41', 'color' => 'Abu-abu/Hijau', 'base_price' => 629000, 'discount_price' => 569000, 'stock' => 20],
                    ['size' => '42', 'color' => 'Abu-abu/Hijau', 'base_price' => 629000, 'discount_price' => 569000, 'stock' => 12]
                ]
            ],
            [
                'category_name' => 'Pakaian',
                'subcategory_name' => 'Jersey',
                'brand_name' => 'Nike',
                'gender' => 'Laki-laki',
                'name' => 'Nike Dri-FIT Squad Academy Jersey',
                'description' => "Jersey latihan sepak bola profesional Nike Dri-FIT Squad Academy. Dirancang menggunakan material 100% poliester daur ulang berteknologi Dri-FIT yang menyerap keringat dengan cepat dari kulit ke permukaan kain agar cepat menguap.\n\nDilengkapi dengan panel jala (mesh) di bagian belakang untuk meningkatkan ventilasi udara selama Anda berlatih keras di lapangan.",
                'weight_gram' => 150,
                'is_featured' => false,
                'image' => 'products/nike_drifit_jersey.png',
                'skus' => [
                    ['size' => 'S', 'color' => 'Merah', 'base_price' => 399000, 'discount_price' => null, 'stock' => 20],
                    ['size' => 'M', 'color' => 'Merah', 'base_price' => 399000, 'discount_price' => null, 'stock' => 35],
                    ['size' => 'L', 'color' => 'Merah', 'base_price' => 399000, 'discount_price' => null, 'stock' => 30],
                    ['size' => 'XL', 'color' => 'Merah', 'base_price' => 399000, 'discount_price' => null, 'stock' => 15]
                ]
            ],
            [
                'category_name' => 'Pakaian',
                'subcategory_name' => 'Hoodie',
                'brand_name' => 'Adidas',
                'gender' => 'Unisex',
                'name' => 'Adidas Essentials Fleece Hoodie',
                'description' => "Bungkus tubuh Anda dalam kenyamanan maksimal setelah berolahraga dengan Adidas Essentials Fleece Hoodie. Terbuat dari katun fleece kualitas terbaik dan serat poliester daur ulang yang memberikan rasa lembut luar biasa di kulit.\n\nDilengkapi dengan saku kangguru di depan, tali serut pada penutup kepala, dan detail logo bordir Adidas klasik.",
                'weight_gram' => 500,
                'is_featured' => true,
                'image' => 'products/adidas_essentials_hoodie.png',
                'skus' => [
                    ['size' => 'S', 'color' => 'Hitam', 'base_price' => 999000, 'discount_price' => 849000, 'stock' => 15],
                    ['size' => 'M', 'color' => 'Hitam', 'base_price' => 999000, 'discount_price' => 849000, 'stock' => 20],
                    ['size' => 'L', 'color' => 'Hitam', 'base_price' => 999000, 'discount_price' => 849000, 'stock' => 20],
                    ['size' => 'XL', 'color' => 'Hitam', 'base_price' => 999000, 'discount_price' => 849000, 'stock' => 10]
                ]
            ],
            [
                'category_name' => 'Pakaian',
                'subcategory_name' => 'Celana',
                'brand_name' => 'Puma',
                'gender' => 'Laki-laki',
                'name' => 'Puma Active Woven Running Shorts',
                'description' => "Celana pendek olahraga Puma Active Woven Shorts dirancang khusus untuk kenyamanan dan kebebasan bergerak saat berlari atau latihan di gym.\n\nBerbahan kain anyaman microfiber premium yang ringan, elastis, dan memiliki kemampuan sirkulasi udara optimal. Dilengkapi dengan pinggang elastis berperekat serut di dalam dan saku samping.",
                'weight_gram' => 120,
                'is_featured' => false,
                'image' => 'products/puma_woven_shorts.png',
                'skus' => [
                    ['size' => 'S', 'color' => 'Navy', 'base_price' => 299000, 'discount_price' => 269000, 'stock' => 25],
                    ['size' => 'M', 'color' => 'Navy', 'base_price' => 299000, 'discount_price' => 269000, 'stock' => 30],
                    ['size' => 'L', 'color' => 'Navy', 'base_price' => 299000, 'discount_price' => 269000, 'stock' => 25],
                    ['size' => 'XL', 'color' => 'Navy', 'base_price' => 299000, 'discount_price' => 269000, 'stock' => 15]
                ]
            ],
            [
                'category_name' => 'Aksesoris',
                'subcategory_name' => 'Tas',
                'brand_name' => 'Adidas',
                'gender' => 'Unisex',
                'name' => 'Adidas Linear Duffel Bag Medium',
                'description' => "Bawa seluruh perlengkapan olahraga Anda dengan mudah menggunakan Adidas Linear Duffel Bag. Tas duffel berukuran sedang ini terbuat dari bahan kanvas anyaman poliester daur ulang yang sangat kuat dan tahan gesekan.\n\nMemiliki kompartemen utama yang lapang, saku ritsleting di bagian samping untuk aksesoris kecil, serta kompartemen khusus sepatu berventilasi udara untuk memisahkan sepatu kotor Anda.",
                'weight_gram' => 450,
                'is_featured' => false,
                'image' => 'products/adidas_duffel_bag.png',
                'skus' => [
                    ['size' => 'All Size', 'color' => 'Hitam', 'base_price' => 450000, 'discount_price' => null, 'stock' => 15]
                ]
            ],
            [
                'category_name' => 'Aksesoris',
                'subcategory_name' => 'Topi',
                'brand_name' => 'Specs',
                'gender' => 'Unisex',
                'name' => 'Specs Lightweight Running Performance Cap',
                'description' => "Specs Running Cap didesain khusus bagi para pelari jarak jauh. Menggunakan material mikro poliester super ringan yang anti-air dan cepat menguapkan kelembapan.\n\nDilengkapi pengatur ukuran strap velcro yang elastis dan reflektif di bagian belakang untuk keamanan berlari di malam hari.",
                'weight_gram' => 80,
                'is_featured' => false,
                'image' => 'products/specs_running_cap.png',
                'skus' => [
                    ['size' => 'All Size', 'color' => 'Putih', 'base_price' => 129000, 'discount_price' => 119000, 'stock' => 40]
                ]
            ]
        ];

        foreach ($productsData as $index => $pData) {
            // Temukan kategori
            $category = Category::where('name', $pData['category_name'])->first();
            if (!$category) {
                $category = Category::create([
                    'name' => $pData['category_name'],
                    'slug' => Str::slug($pData['category_name'])
                ]);
            }

            // Temukan subkategori
            $subcategory = Subcategory::where('name', $pData['subcategory_name'])
                ->where('category_id', $category->id)
                ->first();
            if (!$subcategory) {
                $subcategory = Subcategory::create([
                    'category_id' => $category->id,
                    'name' => $pData['subcategory_name'],
                    'slug' => Str::slug($pData['subcategory_name'])
                ]);
            }

            // Temukan brand
            $brand = Brand::where('name', $pData['brand_name'])->first();
            if (!$brand) {
                $brand = Brand::create([
                    'name' => $pData['brand_name'],
                    'slug' => Str::slug($pData['brand_name'])
                ]);
            }

            // Buat produk baru
            $slugBase = Str::slug($pData['name']);
            $slug = $slugBase;
            $counter = 1;
            while (Product::where('slug', $slug)->exists()) {
                $slug = $slugBase . '-' . $counter;
                $counter++;
            }

            $product = Product::create([
                'category_id' => $category->id,
                'subcategory_id' => $subcategory->id,
                'brand_id' => $brand->id,
                'gender' => $pData['gender'],
                'name' => $pData['name'],
                'slug' => $slug,
                'description' => $pData['description'],
                'weight_gram' => $pData['weight_gram'],
                'is_featured' => $pData['is_featured']
            ]);

            // Tambahkan gambar
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $pData['image'],
                'is_primary' => true
            ]);

            // Tambahkan SKU
            foreach ($pData['skus'] as $sku) {
                ProductSku::create([
                    'product_id' => $product->id,
                    'size' => $sku['size'],
                    'color' => $sku['color'],
                    'base_price' => $sku['base_price'],
                    'discount_price' => $sku['discount_price'],
                    'stock' => $sku['stock'],
                    'reserved_stock' => 0
                ]);
            }
        }
    }
}
