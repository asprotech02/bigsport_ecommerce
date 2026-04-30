<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            
            // Dibuat nullable agar kalau produk dihapus oleh admin, riwayat pesanan (invoice) tidak ikut terhapus/error
            $table->foreignId('product_sku_id')->nullable()->constrained('product_skus')->onDelete('set null');
            
            // Data "Snapshot" Historis (Wajib ada biar data tagihan nggak berubah)
            $table->string('product_name'); // Simpan nama produk saat dibeli
            $table->string('product_size')->nullable(); // Simpan ukuran saat dibeli
            $table->decimal('price_at_purchase', 12, 2); // Harga saat barang dibeli
            $table->integer('quantity');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};