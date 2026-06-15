<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('initialstok', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barang')->onDelete('cascade');
            $table->integer('qty_baik')->nullable();
            $table->integer('qty_rusak')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique('barang_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('initialstok');
    }
};
