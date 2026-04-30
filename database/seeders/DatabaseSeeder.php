<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

// Import Model
use App\Models\User;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSku;
use App\Models\Review;
use App\Models\Promo; // <--- TAMBAHKAN INI

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // 1. USER ADMIN & CUSTOMER UTAMA
        User::create([
            'name' => 'Admin Big Sport',
            'email' => 'admin@bigsport.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Budi Santoso',
            'email' => 'customer@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);

        // 1.b GENERATE 10 CUSTOMER DUMMY
        $dummyCustomers = [];
        for ($i = 0; $i < 10; $i++) {
            $dummyCustomers[] = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password'),
                'role' => 'customer',
            ]);
        }

        // 2. DATA MASTER (KATEGORI, SUBKATEGORI, MEREK)
        $kategoriData = [
            'Sepatu' => ['Sepak Bola', 'Basket', 'Volly', 'Casual', 'Sepatu Lari'],
            'Pakaian' => ['Kaos', 'Jersey', 'Hoodie', 'Celana'],
            'Aksesoris' => ['Tas', 'Topi', 'Kaos Kaki', 'Alat Olahraga']
        ];

        $merekData = ['Adidas', 'Nike', 'Puma', 'Ortuseight', 'Specs'];
        $genderData = ['Laki-laki', 'Perempuan', 'Anak-anak', 'Unisex'];
        
        $brandsDb = [];
        $catsDb = [];
        $subcatsDb = [];

        foreach ($merekData as $merek) {
            $brandsDb[] = Brand::create(['name' => $merek, 'slug' => Str::slug($merek)]);
        }

        foreach ($kategoriData as $catName => $subCats) {
            $category = Category::create(['name' => $catName, 'slug' => Str::slug($catName)]);
            $catsDb[$catName] = $category;

            foreach ($subCats as $subName) {
                $subcatsDb[$catName][] = Subcategory::create([
                    'category_id' => $category->id, 
                    'name' => $subName, 
                    'slug' => Str::slug($subName)
                ]);
            }
        }

        // 3. GENERATOR 150 PRODUK DUMMY
        for ($i = 1; $i <= 150; $i++) {
            $randomCatName = array_rand($kategoriData);
            $kategori = $catsDb[$randomCatName];
            $subkategori = $faker->randomElement($subcatsDb[$randomCatName]);
            $merek = $faker->randomElement($brandsDb);
            $gender = $faker->randomElement($genderData);

            $namaProduk = $merek->name . ' ' . $subkategori->name . ' ' . ucfirst($faker->word);
            $basePrice = $faker->numberBetween(15, 250) * 10000; 
            
            $isDiscount = $faker->boolean(40);
            $discountPrice = null;
            if ($isDiscount) {
                $persenPotongan = $faker->randomElement([0.1, 0.2, 0.3, 0.5]);
                $discountPrice = $basePrice - ($basePrice * $persenPotongan);
            }

            $product = Product::create([
                'category_id'    => $kategori->id,
                'subcategory_id' => $subkategori->id,
                'brand_id'       => $merek->id,
                'gender'         => $gender,
                'name'           => $namaProduk,
                'slug'           => Str::slug($namaProduk . '-' . $i),
                'description'    => $faker->paragraph(3) . "\n\n" . "Material premium yang nyaman digunakan.",
                'base_price'     => $basePrice,
                'discount_price' => $discountPrice,
                'is_featured'    => $faker->boolean(20),
                'weight_gram'    => $faker->numberBetween(200, 1000),
                'created_at'     => $faker->dateTimeBetween('-3 months', 'now'),
            ]);

            // Galeri Gambar Dummy
            ProductImage::create(['product_id' => $product->id, 'image_path' => 'products/pegasus-40.jpg', 'is_primary' => true]);
            ProductImage::create(['product_id' => $product->id, 'image_path' => 'products/samba-side.jpg', 'is_primary' => false]);

            // Ukuran & Stok
            if ($randomCatName == 'Pakaian') { $availableSizes = ['S', 'M', 'L', 'XL']; } 
            elseif ($randomCatName == 'Sepatu') { $availableSizes = ['40', '41', '42']; } 
            else { $availableSizes = ['All Size']; }

            foreach ($faker->randomElements($availableSizes, 1) as $size) {
                ProductSku::create(['product_id' => $product->id, 'size' => $size, 'stock' => $faker->numberBetween(5, 20)]);
            }

            // Reviews Dummy
            $numReviews = $faker->numberBetween(0, 3);
            for ($r = 0; $r < $numReviews; $r++) {
                Review::create([
                    'product_id' => $product->id,
                    'user_id'    => $faker->randomElement($dummyCustomers)->id,
                    'rating'     => $faker->numberBetween(4, 5),
                    'comment'    => 'Barang bagus sesuai pesanan.',
                ]);
            }
        }

        // ==========================================
        // 6. GENERATE DATA PROMO (BARU)
        // ==========================================
        Promo::create([
            'code' => 'BIGSPORT10',
            'type' => 'fixed',
            'reward' => 10000,
            'max_usage' => 100,
            'used_count' => 0,
            'min_order_amount' => 50000,
            'is_active' => true,
            'expires_at' => now()->addDays(30),
        ]);

        Promo::create([
            'code' => 'PROMOHEMAT',
            'type' => 'percentage',
            'reward' => 10, // 10%
            'max_usage' => 50,
            'used_count' => 0,
            'min_order_amount' => 100000,
            'is_active' => true,
            'expires_at' => now()->addDays(7),
        ]);

        Promo::create([
            'code' => 'LEBARAN2026',
            'type' => 'fixed',
            'reward' => 50000,
            'max_usage' => 10,
            'used_count' => 0,
            'min_order_amount' => 500000,
            'is_active' => true,
            'expires_at' => '2026-05-30 23:59:59',
        ]);
    }
}