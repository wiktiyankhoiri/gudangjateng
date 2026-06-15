<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penyesuaian_stok', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('barang_id')->constrained('barang')->onDelete('cascade');
            $table->integer('stok_baik_sebelum');
            $table->integer('stok_baik_sesudah');
            $table->integer('stok_rusak_sebelum');
            $table->integer('stok_rusak_sesudah');
            $table->integer('selisih_baik');
            $table->integer('selisih_rusak');
            $table->text('alasan')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            $table->index('barang_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penyesuaian_stok');
    }
};
