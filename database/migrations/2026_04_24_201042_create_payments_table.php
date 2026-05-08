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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('midtrans_transaction_id')->nullable();
            $table->string('payment_type')->nullable(); // bank_transfer, gopay, dll
            $table->string('payment_status')->nullable(); // settlement, pending, deny
            $table->string('bank_name')->nullable();
            $table->decimal('gross_amount', 12, 2);
            $table->timestamps();
            $table->softDeletes(); // Opsional, sesuaikan dengan ERD Anda jika pakai deleted_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
