<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang_keluar_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_keluar_id')->constrained('barang_keluar')->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('barang')->onDelete('cascade');
            $table->integer('qty_baik')->default(0);
            $table->integer('qty_rusak')->default(0);
            
            $table->index('barang_keluar_id');
            $table->index('barang_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang_keluar_detail');
    }
};
