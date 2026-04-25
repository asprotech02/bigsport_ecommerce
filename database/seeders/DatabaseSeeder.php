<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

// Import semua model satu per satu
use App\Models\User;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSku;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Wishlist;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ShippingDetail;
use App\Models\Review;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. USERS
        $admin = User::create([
            'name' => 'Admin Big Sport',
            'email' => 'admin@bigsport.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $customer = User::create([
            'name' => 'Budi Santoso',
            'email' => 'customer@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);

        // 2. CATEGORIES
        $categories = ['Running', 'Basketball', 'Lifestyle', 'Football'];
        foreach ($categories as $cat) {
            Category::create(['name' => $cat, 'slug' => Str::slug($cat)]);
        }

        // 3. BRANDS
        $brands = ['Adidas', 'Nike', 'New Balance', 'Puma'];
        foreach ($brands as $br) {
            Brand::create(['name' => $br, 'slug' => Str::slug($br)]);
        }

        // 4. PRODUCTS
        $product = Product::create([
            'category_id' => 3, // Lifestyle
            'brand_id' => 1,    // Adidas
            'name' => 'Adidas Samba OG Black',
            'slug' => Str::slug('Adidas Samba OG Black'),
            'description' => 'Sepatu klasik dengan gaya abadi.',
            'base_price' => 1850000,
            'weight_gram' => 800,
        ]);

        // 5. PRODUCT_IMAGES
        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => 'products/samba-black.jpg',
            'is_primary' => true
        ]);


        $name = 'Nike Air Zoom Pegasus 40';

        $product = Product::create([
            'category_id'    => 2,
            'brand_id'       => 2,
            'name'           => $name,
            'slug'           => Str::slug($name),
            'description'    => 'Sepatu lari performa tinggi dengan teknologi Air Zoom.',
            'base_price'     => 2100000,
            'discount_price' => 1850000, 
            'is_featured'    => true, // KUNCINYA DI SINI
            'weight_gram'    => 850,
        ]);

        // Jangan lupa tambahkan fotonya
        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => 'products/pegasus-40.jpg',
            'is_primary' => true
        ]);

        // 6. PRODUCT_SKUS (Varian Ukuran)
        $sku42 = ProductSku::create([
            'product_id' => $product->id,
            'size' => '42',
            'stock' => 10
        ]);
        ProductSku::create(['product_id' => $product->id, 'size' => '43', 'stock' => 5]);

        // 7. ADDRESSES
        $address = Address::create([
            'user_id' => $customer->id,
            'province_id' => 6, // DKI Jakarta
            'city_id' => 151,   // Jakarta Barat
            'district_id' => 2101,
            'full_address' => 'Jl. Merdeka No. 123, Kembangan',
            'receiver_name' => 'Budi Santoso',
            'receiver_phone' => '08123456789',
            'is_default' => true
        ]);

        // 8. CARTS
        Cart::create([
            'user_id' => $customer->id,
            'product_sku_id' => $sku42->id,
            'quantity' => 1
        ]);

        // 9. WISHLISTS
        Wishlist::create([
            'user_id' => $customer->id,
            'product_id' => $product->id
        ]);

        // 10. ORDERS
        $order = Order::create([
            'user_id' => $customer->id,
            'address_id' => $address->id,
            'invoice_number' => 'INV-' . date('Ymd') . '-001',
            'total_product_price' => 1850000,
            'total_shipping_cost' => 20000,
            'grand_total' => 1870000,
            'status' => 'completed'
        ]);

        // 11. ORDER_ITEMS
        OrderItem::create([
            'order_id' => $order->id,
            'product_sku_id' => $sku42->id,
            'price_at_purchase' => 1850000,
            'quantity' => 1
        ]);

        // 12. PAYMENTS (Log Midtrans)
        Payment::create([
            'order_id' => $order->id,
            'midtrans_transaction_id' => 'MID-TRX-998877',
            'payment_type' => 'bank_transfer',
            'payment_status' => 'settlement',
            'bank_name' => 'BCA',
            'gross_amount' => 1870000
        ]);

        // 13. SHIPPING_DETAILS (Log Biteship)
        ShippingDetail::create([
            'order_id' => $order->id,
            'biteship_order_id' => 'BITESHIP-12345',
            'courier_name' => 'JNE',
            'courier_service' => 'REG',
            'tracking_number' => 'JN123456789ID',
            'cost' => 20000
        ]);

        // 14. REVIEWS
        Review::create([
            'user_id' => $customer->id,
            'product_id' => $product->id,
            'rating' => 5,
            'comment' => 'Barang original, pengiriman cepat sampai Tangerang!'
        ]);
    }
}