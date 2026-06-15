<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutoBackupDatabase extends Command
{
    protected $signature = 'app:auto-backup';
    protected $description = 'Backup database otomatis setiap hari';

    public function handle(): int
    {
        try {
            $path = storage_path('app/backups');
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }

            $filename = 'backup-' . date('Y-m-d-His') . '.sql';
            $filepath = $path . '/' . $filename;

            $sql = $this->generateDump();
            file_put_contents($filepath, $sql);

            // Rotasi: hapus backup > 30 hari
            $cutoff = strtotime('-30 days');
            $deleted = 0;
            foreach (glob($path . '/backup-*.sql') as $f) {
                if (filemtime($f) < $cutoff) {
                    unlink($f);
                    $deleted++;
                }
            }

            $this->info("Auto backup: {$filename}" . ($deleted ? " ({$deleted} lama dihapus)" : ""));
            \Log::info("AutoBackup: {$filename} created, {$deleted} old deleted.");

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Auto backup gagal: ' . $e->getMessage());
            \Log::error('AutoBackup failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    private function generateDump(): string
    {
        $output = "-- GudangJateng Auto Backup\n";
        $output .= "-- Created: " . date('Y-m-d H:i:s') . "\n";
        $output .= "-- Database: " . DB::getDatabaseName() . "\n\n";
        $output .= "SET session_replication_role = 'replica';\n\n";

        $tables = DB::select(
            "SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' AND table_name != 'migrations' ORDER BY table_name"
        );

        $allTables = array_map(fn($t) => $t->table_name, $tables);

        foreach (array_reverse($allTables) as $tableName) {
            $output .= "DROP TABLE IF EXISTS {$tableName} CASCADE;\n";
        }
        $output .= "\n";

        foreach ($allTables as $tableName) {
            $output .= $this->getCreateTableSql($tableName) . ";\n\n";
        }

        foreach ($allTables as $tableName) {
            DB::table($tableName)->orderBy(DB::raw('1'))->chunk(500, function ($rows) use (&$output, $tableName) {
                if ($rows->isEmpty()) return;
                $columns = implode(', ', array_keys((array) $rows->first()));
                foreach ($rows as $row) {
                    $values = [];
                    foreach ((array) $row as $value) {
                        $values[] = is_null($value) ? 'NULL' : DB::getPdo()->quote($value);
                    }
                    $output .= "INSERT INTO {$tableName} ({$columns}) VALUES (" . implode(', ', $values) . ");\n";
                }
            });
            $output .= "\n";
        }

        $output .= "SET session_replication_role = 'origin';\n";
        return $output;
    }

    private function getCreateTableSql(string $tableName): string
    {
        $columns = DB::select(
            "SELECT column_name, data_type, character_maximum_length, ordinal_position
             FROM information_schema.columns WHERE table_schema = 'public' AND table_name = ? ORDER BY ordinal_position",
            [$tableName]
        );

        $pkCols = DB::select(
            "SELECT kcu.column_name FROM information_schema.table_constraints tc
             JOIN information_schema.key_column_usage kcu ON tc.constraint_name = kcu.constraint_name
             WHERE tc.table_schema = 'public' AND tc.table_name = ? AND tc.constraint_type = 'PRIMARY KEY'
             ORDER BY kcu.ordinal_position",
            [$tableName]
        );

        $pkColumns = array_map(fn($c) => $c->column_name, $pkCols);

        $sql = "CREATE TABLE {$tableName} (\n";
        foreach ($columns as $i => $col) {
            $isSerial = in_array($col->column_name, $pkColumns) && str_contains($col->column_default ?? '', 'nextval');
            $type = $isSerial ? 'SERIAL' : strtoupper($col->data_type);
            if ($col->character_maximum_length) $type .= "({$col->character_maximum_length})";
            $sql .= "    {$col->column_name} {$type}";
            if ($i < count($columns) - 1) $sql .= ",";
            $sql .= "\n";
        }
        if (!empty($pkColumns)) {
            $sql .= "    PRIMARY KEY (" . implode(', ', $pkColumns) . ")\n";
        }
        $sql .= ")";
        return $sql;
    }
}
