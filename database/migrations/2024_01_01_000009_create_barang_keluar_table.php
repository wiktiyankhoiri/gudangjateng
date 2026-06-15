<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang_keluar', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat');
            $table->date('tanggal');
            $table->foreignId('toko_id')->nullable()->constrained('toko')->onDelete('set null');
            $table->foreignId('sales_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            $table->unique('no_surat');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang_keluar');
    }
};
