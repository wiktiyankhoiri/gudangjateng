<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->decimal('harga_gold', 15, 2)->nullable()->after('satuan');
            $table->decimal('harga_grosir', 15, 2)->nullable()->after('harga_gold');
            $table->decimal('harga_khusus', 15, 2)->nullable()->after('harga_grosir');
        });
    }

    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->dropColumn(['harga_gold', 'harga_grosir', 'harga_khusus']);
        });
    }
};
