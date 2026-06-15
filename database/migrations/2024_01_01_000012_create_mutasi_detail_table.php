<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mutasi_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mutasi_id')->constrained('mutasi')->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('barang')->onDelete('cascade');
            $table->enum('tipe', ['baik_ke_rusak', 'rusak_ke_baik']);
            $table->integer('qty');
            $table->timestamps();
            
            $table->index('mutasi_id');
            $table->index('barang_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mutasi_detail');
    }
};
