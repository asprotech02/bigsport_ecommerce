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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('subcategory_id')->nullable()->constrained('subcategories')->nullOnDelete();
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
            $table->enum('gender', ['Laki-laki', 'Perempuan', 'Anak-anak', 'Unisex'])->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->integer('weight_gram'); 
            $table->boolean('is_featured')->default(false);
            // 🌟 FIX: base_price dan discount_price dihapus dari sini
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
