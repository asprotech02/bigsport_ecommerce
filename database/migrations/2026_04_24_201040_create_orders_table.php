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
            $table->foreignId('address_id')->nullable()->constrained('addresses')->onDelete('set null');
            $table->foreignId('promo_id')->nullable()->constrained('promos')->onDelete('set null');
            
            $table->string('invoice_number')->unique();
            $table->text('shipping_address')->nullable();
            
            $table->decimal('total_product_price', 12, 2);
            $table->decimal('total_shipping_cost', 12, 2);
            $table->decimal('discount_amount', 12, 2)->default(0); 
            $table->decimal('grand_total', 12, 2);
            $table->string('snap_token')->nullable(); 
            
            // 🌟 FIX: Ubah string menjadi ENUM untuk payment_status
            $table->enum('payment_status', ['unpaid', 'pending', 'paid', 'failed', 'expired', 'refunded'])->default('unpaid'); 
            
            $table->string('payment_type')->nullable(); 
            
            // 🌟 FIX: Ubah string menjadi ENUM untuk status pesanan
            $table->enum('status', ['pending', 'confirmed', 'processing', 'completed', 'cancelled'])->default('pending'); 
            
            $table->timestamps();
            $table->softDeletes();
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