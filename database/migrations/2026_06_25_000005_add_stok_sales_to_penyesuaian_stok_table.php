<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penyesuaian_stok', function (Blueprint $table) {
            $table->integer('stok_sales_sebelum')->default(0)->after('stok_rusak_sesudah');
            $table->integer('stok_sales_sesudah')->default(0)->after('stok_sales_sebelum');
            $table->integer('selisih_sales')->default(0)->after('stok_sales_sesudah');
        });
    }

    public function down(): void
    {
        Schema::table('penyesuaian_stok', function (Blueprint $table) {
            $table->dropColumn(['stok_sales_sebelum', 'stok_sales_sesudah', 'selisih_sales']);
        });
    }
};