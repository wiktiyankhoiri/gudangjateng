<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barang')->onDelete('cascade');
            $table->integer('stok_baik')->default(0);
            $table->integer('stok_rusak')->default(0);
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            
            $table->unique('barang_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok');
    }
};
