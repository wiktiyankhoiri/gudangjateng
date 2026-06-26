<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stok_opname_detail', function (Blueprint $table) {
            $table->integer('stok_sistem_sales')->default(0)->after('stok_sistem_rusak');
            $table->integer('stok_fisik_sales')->default(0)->after('stok_fisik_rusak');
            $table->integer('selisih_sales')->default(0)->after('selisih_rusak');
        });
    }

    public function down(): void
    {
        Schema::table('stok_opname_detail', function (Blueprint $table) {
            $table->dropColumn(['stok_sistem_sales', 'stok_fisik_sales', 'selisih_sales']);
        });
    }
};