<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mutasi', function (Blueprint $table) {
            $table->id();
            $table->string('no_mutasi');
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            $table->unique('no_mutasi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mutasi');
    }
};
