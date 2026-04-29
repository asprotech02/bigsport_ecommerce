<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Sesuai dengan menu navbar Anda: Laki-laki, Perempuan, Anak-anak
            $table->enum('gender', ['Laki-laki', 'Perempuan', 'Anak-anak', 'Unisex'])->nullable()->after('brand_id');
            
            // Relasi ke tabel subcategories
            $table->foreignId('subcategory_id')->nullable()->constrained('subcategories')->nullOnDelete()->after('category_id');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['subcategory_id']);
            $table->dropColumn('subcategory_id');
            $table->dropColumn('gender');
        });
    }
};