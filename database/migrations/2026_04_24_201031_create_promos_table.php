<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Contoh: BAGINDOJAYA10
            $table->enum('type', ['fixed', 'percentage']); // Potongan harga flat atau persen
            $table->decimal('reward', 15, 2); // Nilai potongan (misal: 10000 atau 10)
            $table->integer('max_usage')->default(100); // Maksimal berapa kali kode bisa dipakai
            $table->integer('used_count')->default(0); // Sudah dipakai berapa kali
            $table->timestamp('expires_at')->nullable(); // Masa berlaku
            $table->decimal('min_order_amount', 15, 2)->default(0); // Syarat minimal belanja
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes(); // Opsional, sesuaikan dengan ERD Anda jika pakai deleted_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};
