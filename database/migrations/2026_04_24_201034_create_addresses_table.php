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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('receiver_name'); 
            $table->string('receiver_phone');
            $table->integer('province_id');
            $table->string('province_name');
            $table->integer('city_id');
            $table->string('city_name');
            $table->string('district_id'); // 🌟 FIX: Sudah jadi string untuk Biteship
            $table->string('district_name');
            $table->string('village_id')->nullable(); // 🌟 FIX: Sudah masuk sini
            $table->string('village_name')->nullable();
            $table->string('postal_code');
            $table->text('full_address');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->softDeletes(); // Opsional, sesuaikan dengan ERD Anda jika pakai deleted_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
