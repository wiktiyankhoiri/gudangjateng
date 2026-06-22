<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * ALTER TABLE ... DROP/ADD CONSTRAINT tidak boleh di dalam transaction block
     * jika dikombinasikan dengan operasi DDL lain.
     */
    public $withinTransaction = false;

    /**
     * Tambahkan value 'staff' ke kolom role pada tabel users.
     *
     * Kolom role bisa berupa:
     * 1. PostgreSQL native enum type  -> ALTER TYPE ... ADD VALUE
     * 2. VARCHAR + CHECK constraint   -> DROP & re-ADD constraint
     *
     * Migration ini auto-detect approach yang benar.
     */
    public function up(): void
    {
        // Cek apakah ada native enum type untuk role
        $enumType = DB::selectOne("
            SELECT t.typname
            FROM pg_type t
            JOIN pg_enum e ON t.oid = e.enumtypid
            WHERE e.enumlabel IN ('admin', 'sales')
            LIMIT 1
        ");

        if ($enumType) {
            // Approach 1: Native PostgreSQL enum
            $exists = DB::selectOne("
                SELECT 1 FROM pg_enum e
                JOIN pg_type t ON t.oid = e.enumtypid
                WHERE t.typname = ? AND e.enumlabel = 'staff'
            ", [$enumType->typname]);

            if (!$exists) {
                DB::statement("ALTER TYPE {$enumType->typname} ADD VALUE 'staff'");
            }
        } else {
            // Approach 2: VARCHAR + CHECK constraint
            // Cari nama CHECK constraint untuk kolom role
            $constraint = DB::selectOne("
                SELECT con.conname
                FROM pg_constraint con
                JOIN pg_class rel ON rel.oid = con.conrelid
                JOIN pg_attribute att ON att.attrelid = rel.oid
                WHERE rel.relname = 'users'
                AND att.attname = 'role'
                AND con.contype = 'c'
            ");

            if ($constraint) {
                // Ambil semua value yang ada dari constraint saat ini
                $definition = DB::selectOne("
                    SELECT pg_get_constraintexpr(con.oid) as definition
                    FROM pg_constraint con
                    WHERE con.conname = ?
                    AND con.conrelid = 'users'::regclass
                ", [$constraint->conname]);

                $currentDef = $definition->definition ?? '';

                // Cek apakah 'staff' sudah ada
                if (!str_contains($currentDef, 'staff')) {
                    // Rebuild constraint dengan semua role termasuk staff
                    $constraintName = $constraint->conname;
                    DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS \"{$constraintName}\"");
                    DB::statement("ALTER TABLE users ADD CONSTRAINT \"{$constraintName}\" CHECK (\"role\" IN ('admin', 'sales', 'audit', 'manager', 'super_admin', 'staff'))");
                }
            } else {
                // Fallback: kolom role tanpa constraint, langsung aman
                // Tidak perlu dilakukan apa-apa
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak bisa remove value dari enum/constraint secara aman di production
    }
};
