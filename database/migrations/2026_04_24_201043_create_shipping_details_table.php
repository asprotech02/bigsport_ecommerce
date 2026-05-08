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
        Schema::create('shipping_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('biteship_order_id')->nullable(); // Pindah ke sini
            $table->string('courier_company'); // Misal: jne
            $table->string('courier_type'); // Misal: reg
            $table->string('tracking_number')->nullable(); // Nomor Resi (waybill_id)
            $table->decimal('cost', 12, 2);
            $table->timestamps();
            $table->softDeletes(); // Opsional, sesuaikan dengan ERD Anda jika pakai deleted_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_details');
    }
};
