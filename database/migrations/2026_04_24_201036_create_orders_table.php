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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // PENTING: address_id dibuat nullable(). Kenapa? 
            // Karena kalau customer pilih "Ambil di Toko", dia gak butuh alamat pengiriman.
            $table->foreignId('address_id')->nullable()->constrained('addresses')->onDelete('set null');
            
            $table->string('invoice_number')->unique();
            
            // --- Kalkulasi Harga ---
            $table->decimal('total_product_price', 12, 2);
            $table->decimal('total_shipping_cost', 12, 2);
            $table->decimal('discount_amount', 12, 2)->default(0); // Potongan harga promo
            $table->decimal('grand_total', 12, 2);
            
            // --- MIDTRANS / PAYMENT INFO ---
            $table->string('snap_token')->nullable(); // Nyimpen token dari Midtrans
            $table->string('payment_status')->default('unpaid'); // Status bayar: unpaid, paid, failed, expired
            $table->string('payment_type')->nullable(); // Metode bayar dari midtrans (qris, bank_transfer, dll)
            
            // --- STATUS ORDER & PENGIRIMAN ---
            $table->string('status')->default('pending'); // Status pesanan: pending, processing, shipped, completed, cancelled
            $table->string('courier_company')->nullable(); // biteship: jne, sicepat, ATAU 'pickup'
            $table->string('courier_type')->nullable(); // reg, yes, store_pickup
            
            // --- PROMO (Jika pakai diskon) ---
            $table->foreignId('promo_id')->nullable()->constrained('promos')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};