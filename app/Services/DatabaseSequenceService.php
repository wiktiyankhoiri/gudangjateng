<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseSequenceService
{
    public function syncSequences(): int
    {
        $sql = <<<'SQL'
DO $$
DECLARE
    r RECORD;
BEGIN
    FOR r IN
        SELECT
            c.relname AS table_name,
            s.relname AS sequence_name
        FROM pg_class s
        JOIN pg_depend d ON d.objid = s.oid
        JOIN pg_class c ON d.refobjid = c.oid
        WHERE s.relkind = 'S'
    LOOP
        EXECUTE format(
            'SELECT setval(''%I'', COALESCE((SELECT MAX(id) FROM %I), 1))',
            r.sequence_name,
            r.table_name
        );
    END LOOP;
END $$;
SQL;

        try {
            DB::statement($sql);
        } catch (\Throwable $e) {
            Log::error('Sinkronisasi sequence gagal: ' . $e->getMessage());
            throw $e;
        }

        $count = DB::select("SELECT count(*) AS total FROM pg_class WHERE relkind = 'S'")[0]->total;
        Log::info("Sinkronisasi sequence selesai. {$count} sequence diperbarui.");

        return $count;
    }
}
