<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE UNIQUE INDEX toko_kode_toko_lower_unique ON toko (LOWER(kode_toko))');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS toko_kode_toko_lower_unique');
    }
};
