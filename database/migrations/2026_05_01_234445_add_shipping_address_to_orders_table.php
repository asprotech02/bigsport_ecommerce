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
        Schema::table('orders', function (Blueprint $table) {
            // Kolom untuk menyimpan alamat lengkap saat transaksi terjadi
            $table->text('shipping_address')->nullable()->after('invoice_number');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('shipping_address');
        });
    }
};
