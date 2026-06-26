<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mutasi', function (Blueprint $table) {
            $table->foreignId('sales_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->after('keterangan');
        });
    }

    public function down(): void
    {
        Schema::table('mutasi', function (Blueprint $table) {
            $table->dropForeign(['sales_id']);
            $table->dropColumn('sales_id');
        });
    }
};