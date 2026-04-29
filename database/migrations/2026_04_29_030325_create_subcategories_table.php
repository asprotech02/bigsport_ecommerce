<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subcategories', function (Blueprint $table) {
            $table->id();
            // Menghubungkan subkategori ke kategori utama (Pakaian, Sepatu, dll)
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('name'); // Contoh: Kaos, Celana, Sepak Bola
            $table->string('slug')->unique();
            $table->timestamps();
            $table->softDeletes(); // Opsional, sesuaikan dengan ERD Anda jika pakai deleted_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subcategories');
    }
};