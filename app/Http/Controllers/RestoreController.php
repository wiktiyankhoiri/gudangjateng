<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\AuditLog;

class RestoreController extends Controller
{
    protected $backupPath;

    public function __construct()
    {
        $this->backupPath = storage_path('app/backups');

        if (!is_dir($this->backupPath)) {
            mkdir($this->backupPath, 0755, true);
        }
    }

    protected function requireAdminOnly(): void
    {
        $role = auth()->user()->role;
        if ($role !== 'admin' && $role !== 'super_admin') {
            abort(404, 'Akses ditolak - Hanya admin');
        }
    }

    public function restore()
    {
        $this->requireAdminOnly();

        $backups = $this->getBackupList();

        return view('pengaturan.backup-restore.restore', [
            'title' => 'Restore Database',
            'backups' => $backups,
        ]);
    }

    public function doRestore(Request $request)
    {
        $this->requireAdminOnly();

        // Cegah timeout dan tetap jalan meskipun browser ditutup
        set_time_limit(0);
        ignore_user_abort(true);

        $confirmation = $request->input('confirmation_text');
        $backupFile = $request->input('backup_file');

        if (strtoupper($confirmation ?? '') !== 'RESTORE') {
            return redirect()->route('pengaturan.backup.restore')->with('error', 'Ketik RESTORE untuk melanjutkan.');
        }

        if (empty($backupFile)) {
            return redirect()->route('pengaturan.backup.restore')->with('error', 'Pilih file backup');
        }

        $filepath = $this->backupPath . '/' . basename($backupFile);

        if (!file_exists($filepath)) {
            return redirect()->route('pengaturan.backup.restore')->with('error', 'File backup tidak ditemukan');
        }

        try {
            $safetyFilename = 'safety-backup-' . date('Y-m-d-His') . '.sql';
            $safetySql = $this->generateDatabaseDump();
            file_put_contents($this->backupPath . '/' . $safetyFilename, $safetySql);

            $sql = file_get_contents($filepath);
            $queries = preg_split('/;(?:\s*[\r\n]|$)/', $sql);

            $allowedPatterns = [
                '/^CREATE\s+/i',
                '/^INSERT\s+/i',
                '/^SET\s+/i',
                '/^SELECT\s+/i',
                '/^BEGIN/i',
                '/^COMMIT/i',
                '/^ALTER\s+TABLE\s+.*\s+OWNER\s+TO/i',
            ];

            DB::statement("SET session_replication_role = 'replica'");

            $successQueries = 0;
            $failedQueries = 0;
            $errors = [];

            foreach ($queries as $query) {
                $query = trim($query);
                if (empty($query)) continue;
                if (str_starts_with($query, '--') || str_starts_with($query, '/*')) continue;

                $isAllowed = false;
                foreach ($allowedPatterns as $pattern) {
                    if (preg_match($pattern, $query)) { $isAllowed = true; break; }
                }

                if (!$isAllowed) { continue; }

                try {
                    DB::statement($query);
                    $successQueries++;
                } catch (\Throwable $e) {
                    $failedQueries++;
                    $errors[] = substr($query, 0, 80) . '... → ' . substr($e->getMessage(), 0, 150);
                    \Log::error('Restore Query Failed: ' . $e->getMessage());
                }
            }

            DB::statement("SET session_replication_role = 'origin'");
            $this->fixSequences();

            $this->logRestore($backupFile, 'success', "Restore: {$successQueries} berhasil, {$failedQueries} gagal");

            $message = 'Restore berhasil. Safety backup: ' . $safetyFilename;
            if ($failedQueries > 0) {
                $message .= " | ⚠️ {$failedQueries} query gagal.";
                return redirect()->route('pengaturan.backup.index')->with('warning', $message)->with('restore_errors', $errors);
            } else {
                $message .= " | ✅ Semua berhasil.";
                return redirect()->route('pengaturan.backup.index')->with('success', $message);
            }

        } catch (\Exception $e) {
            try { DB::statement("SET session_replication_role = 'origin'"); } catch (\Throwable $t) {}
            \Log::error('Restore failed: ' . $e->getMessage());
            return redirect()->route('pengaturan.backup.restore')->with('error', 'Restore gagal.');
        }
    }

    private function generateDatabaseDump(): string
    {
        $output = "-- Database Backup\n-- Created: " . date('Y-m-d H:i:s') . "\n\n";
        $output .= "SET session_replication_role = 'replica';\n\n";

        $tables = DB::select(
            "SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' AND table_name != 'migrations' ORDER BY table_name"
        );

        $allTables = array_map(function ($t) { return $t->table_name; }, $tables);

        foreach (array_reverse($allTables) as $tableName) {
            $output .= "DROP TABLE IF EXISTS {$tableName} CASCADE;\n";
        }
        $output .= "\n";

        foreach ($allTables as $tableName) {
            $output .= $this->getCreateTableSql($tableName) . ";\n\n";
        }

        foreach ($allTables as $tableName) {
            $rows = DB::table($tableName)->get()->toArray();
            if (!empty($rows)) {
                $columns = implode(', ', array_keys((array)$rows[0]));
                foreach ($rows as $row) {
                    $values = [];
                    foreach ((array)$row as $value) {
                        if (is_null($value)) {
                            $values[] = 'NULL';
                        } else {
                            $values[] = DB::getPdo()->quote($value);
                        }
                    }
                    $output .= "INSERT INTO {$tableName} ({$columns}) VALUES (" . implode(', ', $values) . ");\n";
                }
                $output .= "\n";
            }
        }

        $output .= "SET session_replication_role = 'origin';\n";
        return $output;
    }

    private function getCreateTableSql(string $tableName): string
    {
        $columns = DB::select(
            "SELECT column_name, data_type, character_maximum_length, is_nullable, column_default, ordinal_position
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

        $pkColumns = array_map(function ($c) { return $c->column_name; }, $pkCols);
        $sql = "CREATE TABLE {$tableName} (\n";

        foreach ($columns as $i => $col) {
            $colName = $col->column_name;
            $isSerial = in_array($colName, $pkColumns) && str_contains($col->column_default ?? '', 'nextval');

            if ($isSerial) {
                $sql .= "    {$colName} SERIAL";
            } else {
                if ($col->data_type === 'character varying' && $col->character_maximum_length) {
                    $sql .= "    {$colName} varchar({$col->character_maximum_length})";
                } elseif ($col->data_type === 'character varying') {
                    $sql .= "    {$colName} text";
                } else {
                    $sql .= "    {$colName} {$col->data_type}";
                }
                if ($col->column_default !== null && !str_contains($col->column_default, 'nextval')) {
                    $sql .= " DEFAULT {$col->column_default}";
                }
            }

            if ($col->is_nullable !== 'YES' && !$isSerial) $sql .= " NOT NULL";
            if ($i < count($columns) - 1) $sql .= ",";
            $sql .= "\n";
        }

        if (!empty($pkColumns)) {
            $sql .= ",\n    PRIMARY KEY (" . implode(', ', $pkColumns) . ")\n";
        }

        return $sql . ")";
    }

    private function getBackupList(): array
    {
        $backups = [];
        if (!is_dir($this->backupPath)) return $backups;

        foreach (scandir($this->backupPath) as $file) {
            if ($file === '.' || $file === '..') continue;
            $filepath = $this->backupPath . '/' . $file;
            if (is_file($filepath) && preg_match('/\.sql$/', $file)) {
                $backups[] = ['filename' => $file, 'size' => filesize($filepath), 'created' => filectime($filepath), 'path' => $filepath];
            }
        }

        usort($backups, function($a, $b) { return $b['created'] - $a['created']; });
        return $backups;
    }

    private function logRestore(string $filename, string $status, string $message): void
    {
        try {
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'restore',
                'table_name' => 'system',
                'reference_id' => null,
                'description' => "Restore: {$filename} - {$status}",
                'data' => json_encode(['filename' => $filename, 'status' => $status, 'message' => $message]),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            \Log::error('Failed to log restore: ' . $e->getMessage());
        }
    }

    private function fixSequences(): void
    {
        $tables = [
            'barang_keluar', 'barang_keluar_detail', 'barang_masuk', 'barang_masuk_detail',
            'mutasi', 'mutasi_detail', 'stok', 'toko', 'pabrik',
            'notifications', 'audit_log', 'initialstok', 'penyesuaian_stok',
        ];

        foreach ($tables as $table) {
            $seq = $table . '_id_seq';
            try {
                $maxId = DB::select("SELECT COALESCE(MAX(id), 1) as max_id FROM {$table}")[0]->max_id;
                DB::statement("SELECT setval('{$seq}', {$maxId})");
            } catch (\Throwable $e) {
                \Log::warning("Sequence fix failed for {$table}: " . $e->getMessage());
            }
        }
    }
}
