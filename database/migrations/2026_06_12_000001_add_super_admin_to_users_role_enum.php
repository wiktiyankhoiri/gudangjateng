<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * ALTER TYPE ... ADD VALUE tidak bisa dijalankan di dalam transaction block PostgreSQL.
     */
    public $withinTransaction = false;

    /**
     * Tambahkan value 'super_admin' ke enum role pada tabel users.
     *
     * Migration awal (2024_01_01_000001) hanya mendefinisikan:
     * enum('role', ['admin', 'sales', 'audit', 'manager'])
     *
     * Padahal aplikasi menggunakan role 'super_admin' di 25+ referensi
     * (Gate, Middleware, Controller, View).
     */
    public function up(): void
    {
        // PostgreSQL: ALTER TYPE tidak bisa dijalankan di dalam transaction.
        // Cek dulu apakah value sudah ada untuk menghindari error jika di-run ulang.
        $existing = DB::select("
            SELECT e.enumlabel
            FROM pg_type t
            JOIN pg_enum e ON t.oid = e.enumtypid
            WHERE t.typname = 'users_role_enum'
            AND e.enumlabel = 'super_admin'
        ");

        if (empty($existing)) {
            DB::statement("ALTER TYPE users_role_enum ADD VALUE 'super_admin'");
        }
    }

    /**
     * PostgreSQL tidak mendukung penghapusan value dari enum type.
     * Untuk rollback, perlu membuat enum type baru dan swap — terlalu berisiko
     * untuk data production. Oleh karena itu down() dibiarkan kosong.
     *
     * Jika benar-benar perlu rollback, lakukan manual via SQL:
     * 1. Buat enum baru tanpa 'super_admin'
     * 2. Alter column pakai enum baru
     * 3. Drop enum lama, rename enum baru
     */
    public function down(): void
    {
        // Intentionally empty — PostgreSQL enum values cannot be removed.
    }
};
