<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Migration awal (2024_01_01_000001) hanya mendefinisikan:
     * enum('role', ['admin', 'sales', 'audit', 'manager'])
     *
     * Migration 2026_06_12_000001 menambahkan: super_admin
     * Migration ini menambahkan: staff
     *
     * Karena PostgreSQL tidak mendukung penambahan enum value
     * secara langsung via Schema Builder, kita gunakan raw SQL.
     */
    public function up(): void
    {
        // Tambahkan 'staff' ke enum role jika belum ada
        $exists = DB::selectOne("
            SELECT 1 FROM pg_enum
            JOIN pg_type ON pg_enum.enumtypid = pg_type.oid
            WHERE pg_type.typname = 'users_role_enum'
            AND pg_enum.enumlabel = 'staff'
        ");

        if (!$exists) {
            DB::statement("ALTER TYPE users_role_enum ADD VALUE 'staff'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // PostgreSQL tidak mendukung penghapusan enum value secara langsung.
        // Jika perlu rollback, harus recreate type (berisiko pada data existing).
    }
};
