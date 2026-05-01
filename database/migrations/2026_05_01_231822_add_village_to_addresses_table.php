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
        Schema::table('addresses', function (Blueprint $table) {
            // Menambahkan kolom kelurahan setelah kolom district_name
            $table->string('village_id')->nullable()->after('district_name');
            $table->string('village_name')->nullable()->after('village_id');
        });
    }

    public function down()
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn(['village_id', 'village_name']);
        });
    }
};
