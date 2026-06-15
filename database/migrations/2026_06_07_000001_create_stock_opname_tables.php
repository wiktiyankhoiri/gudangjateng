<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_opname', function (Blueprint $table) {
            $table->id();
            $table->string('no_opname')->unique();
            $table->date('tanggal_opname');
            $table->enum('status', ['draft', 'selesai', 'diterapkan', 'dibatalkan'])->default('draft');
            $table->text('catatan')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index('status');
            $table->index('tanggal_opname');
        });

        Schema::create('stock_opname_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_opname_id')->constrained('stock_opname')->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('barang')->onDelete('cascade');
            $table->integer('stok_sistem_baik')->default(0);
            $table->integer('stok_sistem_rusak')->default(0);
            $table->integer('stok_fisik_baik')->default(0);
            $table->integer('stok_fisik_rusak')->default(0);
            $table->integer('selisih_baik')->default(0);
            $table->integer('selisih_rusak')->default(0);
            $table->text('keterangan')->nullable();

            $table->unique(['stock_opname_id', 'barang_id']);
            $table->index('barang_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opname_detail');
        Schema::dropIfExists('stock_opname');
    }
};
