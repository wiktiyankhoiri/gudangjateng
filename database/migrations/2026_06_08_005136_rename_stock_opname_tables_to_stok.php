<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('stock_opname', 'stok_opname');
        Schema::rename('stock_opname_detail', 'stok_opname_detail');

        Schema::table('stok_opname_detail', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->renameColumn('stock_opname_id', 'stok_opname_id');
        });
    }

    public function down(): void
    {
        Schema::table('stok_opname_detail', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->renameColumn('stok_opname_id', 'stock_opname_id');
        });

        Schema::rename('stok_opname_detail', 'stock_opname_detail');
        Schema::rename('stok_opname', 'stock_opname');
    }
};
