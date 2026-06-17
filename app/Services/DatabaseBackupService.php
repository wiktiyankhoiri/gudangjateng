<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseBackupService
{
    /**
     * Generate a complete SQL dump of all public tables.
     * Output includes: DROP TABLE, CREATE TABLE, ALTER TABLE (FK + UNIQUE),
     * CREATE INDEX, and INSERT statements.
     * Does NOT use session_replication_role (works for non-superuser).
     */
    public function generateDump(): string
    {
        $output = "-- Database Backup\n";
        $output .= "-- Created: " . date('Y-m-d H:i:s') . "\n";
        $output .= "-- Database: " . DB::getDatabaseName() . "\n\n";

        $allTables = $this->getTables();

        // Phase 1: DROP all tables (reverse order for dependency safety)
        foreach (array_reverse($allTables) as $tableName) {
            $output .= "DROP TABLE IF EXISTS {$tableName} CASCADE;\n";
        }
        $output .= "\n";

        // Phase 2: CREATE all tables (columns + primary key only)
        foreach ($allTables as $tableName) {
            $output .= $this->getCreateTableSql($tableName) . ";\n\n";
        }

        // Phase 3: Foreign key constraints
        foreach ($allTables as $tableName) {
            $constraints = $this->getConstraintStatements($tableName);
            foreach ($constraints as $stmt) {
                $output .= $stmt . ";\n";
            }
        }
        $output .= "\n";

        // Phase 4: Custom indexes (non-PK, non-unique-constraint)
        foreach ($allTables as $tableName) {
            $indexes = $this->getIndexStatements($tableName);
            foreach ($indexes as $stmt) {
                $output .= $stmt . ";\n";
            }
        }
        $output .= "\n";

        // Phase 5: INSERT data (chunked for memory efficiency)
        foreach ($allTables as $tableName) {
            $hasRows = false;
            DB::table($tableName)->orderBy(DB::raw('1'))->chunk(500, function ($rows) use (&$output, $tableName, &$hasRows) {
                if ($rows->isEmpty()) return;
                $hasRows = true;
                $columns = implode(', ', array_map(fn($c) => "\"{$c}\"", array_keys((array) $rows->first())));
                foreach ($rows as $row) {
                    $values = [];
                    foreach ((array) $row as $value) {
                        $values[] = is_null($value) ? 'NULL' : DB::getPdo()->quote($value);
                    }
                    $output .= "INSERT INTO {$tableName} ({$columns}) VALUES (" . implode(', ', $values) . ");\n";
                }
            });
            if ($hasRows) {
                $output .= "\n";
            }
        }

        return $output;
    }

    /**
     * Restore database from a SQL backup file.
     * Executes in a single transaction for atomicity.
     *
     * @return array{successQueries: int, failedQueries: int, errors: string[]}
     * @throws \Throwable on critical failure (transaction rolled back)
     */
    public function restore(string $filepath): array
    {
        $sql = file_get_contents($filepath);
        if ($sql === false || trim($sql) === '') {
            throw new \RuntimeException('File backup kosong atau tidak bisa dibaca');
        }

        // Strip SQL comments BEFORE splitting to prevent header comments
        // from swallowing the first query (e.g., DROP TABLE merged with "-- Database Backup")
        $sql = preg_replace('/^--.*$/m', '', $sql);
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);

        $rawQueries = preg_split('/;(?:\s*[\r\n]|$)/', $sql);

        // Separate queries into phases
        $phases = [
            'drop' => [],
            'create' => [],
            'alter' => [],
            'index' => [],
            'insert' => [],
        ];

        foreach ($rawQueries as $query) {
            $query = trim($query);
            if (empty($query)) continue;

            // Skip session_replication_role (old backup compatibility)
            if (preg_match('/^SET\s+session_replication_role/i', $query)) continue;

            // Skip other SET/BEGIN/COMMIT that aren't needed
            if (preg_match('/^(SET|BEGIN|COMMIT)/i', $query)) continue;

            // Categorize
            if (preg_match('/^DROP\s+TABLE/i', $query)) {
                $phases['drop'][] = $query;
            } elseif (preg_match('/^CREATE\s+TABLE/i', $query)) {
                $phases['create'][] = $query;
            } elseif (preg_match('/^ALTER\s+TABLE/i', $query)) {
                $phases['alter'][] = $query;
            } elseif (preg_match('/^CREATE\s+(UNIQUE\s+)?INDEX/i', $query)) {
                $phases['index'][] = $query;
            } elseif (preg_match('/^INSERT\s+INTO/i', $query)) {
                $phases['insert'][] = $query;
            }
            // Other statements (SELECT, OWNER TO, etc.) are silently skipped
        }

        Log::info("Restore phases: DROP=" . count($phases['drop'])
            . " CREATE=" . count($phases['create'])
            . " ALTER=" . count($phases['alter'])
            . " INDEX=" . count($phases['index'])
            . " INSERT=" . count($phases['insert']));

        $successQueries = 0;
        $failedQueries = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            // Phase 1: DROP all tables
            foreach ($phases['drop'] as $query) {
                try {
                    DB::statement($query);
                    $successQueries++;
                } catch (\Throwable $e) {
                    $failedQueries++;
                    $errors[] = $this->formatError($query, $e);
                    Log::error("Restore DROP failed: " . $e->getMessage());
                }
            }

            // Phase 2: CREATE all tables (columns + PK only, no FK yet)
            foreach ($phases['create'] as $query) {
                try {
                    DB::statement($query);
                    $successQueries++;
                } catch (\Throwable $e) {
                    $failedQueries++;
                    $errors[] = $this->formatError($query, $e);
                    Log::error("Restore CREATE failed: " . $e->getMessage());
                }
            }

            // Phase 3: INSERT data (no FK constraints exist yet, so no trigger disabling needed)
            foreach ($phases['insert'] as $query) {
                try {
                    DB::statement($query);
                    $successQueries++;
                } catch (\Throwable $e) {
                    $failedQueries++;
                    $errors[] = $this->formatError($query, $e);
                    Log::error("Restore INSERT failed: " . $e->getMessage());
                }
            }

            // Phase 4: ALTER TABLE — add FK + UNIQUE constraints (after data is in place)
            foreach ($phases['alter'] as $query) {
                try {
                    DB::statement($query);
                    $successQueries++;
                } catch (\Throwable $e) {
                    $failedQueries++;
                    $errors[] = $this->formatError($query, $e);
                    Log::error("Restore ALTER failed: " . $e->getMessage());
                }
            }

            // Phase 5: CREATE INDEX
            foreach ($phases['index'] as $query) {
                try {
                    DB::statement($query);
                    $successQueries++;
                } catch (\Throwable $e) {
                    $failedQueries++;
                    $errors[] = $this->formatError($query, $e);
                    Log::error("Restore INDEX failed: " . $e->getMessage());
                }
            }

            // Phase 6: Sync sequences (inside transaction for atomicity)
            $syncSql = <<<'SQL'
DO $$
DECLARE
    r RECORD;
BEGIN
    FOR r IN
        SELECT c.relname AS table_name, s.relname AS sequence_name
        FROM pg_class s
        JOIN pg_depend d ON d.objid = s.oid
        JOIN pg_class c ON d.refobjid = c.oid
        WHERE s.relkind = 'S'
    LOOP
        EXECUTE format(
            'SELECT setval(''%I'', COALESCE((SELECT MAX(id) FROM %I), 1))',
            r.sequence_name, r.table_name
        );
    END LOOP;
END $$;
SQL;
            DB::statement($syncSql);

            // If too many failures, consider it a failure and rollback
            if ($failedQueries > 0) {
                DB::rollBack();
                Log::error("Restore rolled back: {$failedQueries} queries failed");
                return [
                    'successQueries' => $successQueries,
                    'failedQueries' => $failedQueries,
                    'errors' => $errors,
                    'rolledBack' => true,
                ];
            }

            DB::commit();

            Log::info("Restore berhasil: {$successQueries} queries executed");

            return [
                'successQueries' => $successQueries,
                'failedQueries' => $failedQueries,
                'errors' => $errors,
                'rolledBack' => false,
            ];

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Restore failed with exception: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get all public table names (excluding migrations).
     */
    private function getTables(): array
    {
        $tables = DB::select(
            "SELECT table_name FROM information_schema.tables
             WHERE table_schema = 'public' AND table_name != 'migrations'
             ORDER BY table_name"
        );

        return array_map(fn($t) => $t->table_name, $tables);
    }

    /**
     * Generate CREATE TABLE SQL with columns and primary key.
     * Handles ENUM types, SERIAL, VARCHAR(n), TEXT, etc.
     */
    private function getCreateTableSql(string $tableName): string
    {
        $columns = DB::select(
            "SELECT column_name, data_type, udt_name, character_maximum_length,
                    is_nullable, column_default, ordinal_position, numeric_precision, numeric_scale
             FROM information_schema.columns
             WHERE table_schema = 'public' AND table_name = ?
             ORDER BY ordinal_position",
            [$tableName]
        );

        $pkCols = DB::select(
            "SELECT kcu.column_name
             FROM information_schema.table_constraints tc
             JOIN information_schema.key_column_usage kcu
               ON tc.constraint_name = kcu.constraint_name
               AND tc.table_schema = kcu.table_schema
             WHERE tc.table_schema = 'public'
               AND tc.table_name = ?
               AND tc.constraint_type = 'PRIMARY KEY'
             ORDER BY kcu.ordinal_position",
            [$tableName]
        );

        $pkColumns = array_map(fn($c) => $c->column_name, $pkCols);
        $sql = "CREATE TABLE {$tableName} (\n";

        foreach ($columns as $i => $col) {
            $colName = $col->column_name;
            $isSerial = str_contains($col->column_default ?? '', 'nextval');

            if ($isSerial) {
                // Detect BIGSERIAL vs SERIAL
                $sql .= "    {$colName} " . ($col->udt_name === 'int8' ? 'BIGSERIAL' : 'SERIAL');
            } else {
                $sql .= "    {$colName} " . $this->getColumnType($col);

                // Default value (skip nextval since SERIAL handles it)
                if ($col->column_default !== null && !str_contains($col->column_default, 'nextval')) {
                    $sql .= " DEFAULT {$col->column_default}";
                }
            }

            if ($col->is_nullable !== 'YES' && !$isSerial) {
                $sql .= " NOT NULL";
            }

            if ($i < count($columns) - 1) {
                $sql .= ",";
            }
            $sql .= "\n";
        }

        if (!empty($pkColumns)) {
            $sql .= ",\n    PRIMARY KEY (" . implode(', ', $pkColumns) . ")\n";
        }

        return $sql . ")";
    }

    /**
     * Map information_schema column metadata to PostgreSQL type string.
     */
    private function getColumnType(object $col): string
    {
        // Handle ENUM / custom types
        if ($col->data_type === 'USER-DEFINED') {
            return $col->udt_name;
        }

        // Handle VARCHAR with length
        if ($col->data_type === 'character varying' && $col->character_maximum_length) {
            return "varchar({$col->character_maximum_length})";
        }

        // Handle CHARACTER with length
        if ($col->data_type === 'character' && $col->character_maximum_length) {
            return "char({$col->character_maximum_length})";
        }

        // Handle NUMERIC/DECIMAL with precision and scale
        if ($col->data_type === 'numeric' && $col->numeric_precision !== null) {
            $scale = $col->numeric_scale ?? 0;
            return "numeric({$col->numeric_precision}, {$scale})";
        }

        // Handle ARRAY types
        if ($col->data_type === 'ARRAY') {
            return $col->udt_name; // e.g., _text for text[]
        }

        // Default: use data_type as-is (text, integer, boolean, json, jsonb, timestamp, etc.)
        return $col->data_type;
    }

    /**
     * Get ALTER TABLE statements for FK and UNIQUE constraints.
     */
    private function getConstraintStatements(string $tableName): array
    {
        $constraints = DB::select(
            "SELECT conname, pg_get_constraintdef(oid) AS definition
             FROM pg_constraint
             WHERE conrelid = ?::regclass AND contype IN ('f', 'u')
             ORDER BY contype, conname",
            [$tableName]
        );

        $statements = [];
        foreach ($constraints as $c) {
            $statements[] = "ALTER TABLE {$tableName} ADD CONSTRAINT {$c->conname} {$c->definition}";
        }

        return $statements;
    }

    /**
     * Get CREATE INDEX statements for indexes not backing PK/UNIQUE constraints.
     */
    private function getIndexStatements(string $tableName): array
    {
        $indexes = DB::select(
            "SELECT i.indexname, i.indexdef
             FROM pg_indexes i
             WHERE i.schemaname = 'public' AND i.tablename = ?
             AND i.indexname NOT IN (
                 SELECT conname FROM pg_constraint
                 WHERE conrelid = ?::regclass
             )
             ORDER BY i.indexname",
            [$tableName, $tableName]
        );

        return array_map(fn($idx) => $idx->indexdef, $indexes);
    }

    /**
     * Format a query error for logging/reporting.
     */
    private function formatError(string $query, \Throwable $e): string
    {
        return substr($query, 0, 80) . '... → ' . substr($e->getMessage(), 0, 150);
    }
}
