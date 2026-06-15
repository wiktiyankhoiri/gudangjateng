<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\AuditLog;

class BackupController extends Controller
{
    protected $backupPath;

    public function __construct()
    {
        $this->backupPath = storage_path('app/backups');

        if (!is_dir($this->backupPath)) {
            mkdir($this->backupPath, 0755, true);
        }
    }

    protected function requireAdminOrAudit(): void
    {
        $role = auth()->user()->role;
        if (!in_array($role, ['admin', 'audit', 'super_admin'])) {
            abort(404, 'Akses ditolak');
        }
    }

    protected function requireAdminOnly(): void
    {
        $role = auth()->user()->role;
        if ($role !== 'admin' && $role !== 'super_admin') {
            abort(404, 'Akses ditolak');
        }
    }

    public function backup()
    {
        $this->requireAdminOrAudit();

        $backups = $this->getBackupList();

        $lastBackup = null;
        $totalSize = 0;

        if (!empty($backups)) {
            $lastBackup = $backups[0];
            foreach ($backups as $b) {
                $totalSize += $b['size'];
            }
        }

        return view('pengaturan.backup-restore.backup', [
            'title' => 'Backup Database',
            'backups' => $backups,
            'lastBackup' => $lastBackup,
            'totalSize' => $totalSize,
            'totalFiles' => count($backups),
        ]);
    }

    public function doBackup()
    {
        $this->requireAdminOrAudit();

        try {
            $filename = 'backup-' . date('Y-m-d-His') . '.sql';
            $filepath = $this->backupPath . '/' . $filename;

            $sql = $this->generateDatabaseDump();

            if (file_put_contents($filepath, $sql) === false) {
                throw new \Exception('Gagal menyimpan file backup');
            }

            if (filesize($filepath) === 0) {
                unlink($filepath);
                throw new \Exception('File backup kosong');
            }

            $backups = $this->getBackupList();
            if (count($backups) > 15) {
                $toDelete = array_slice($backups, 15);
                foreach ($toDelete as $oldBackup) {
                    if (file_exists($oldBackup['path'])) {
                        unlink($oldBackup['path']);
                        \Log::info('Backup rotation: hapus ' . $oldBackup['filename']);
                    }
                }
            }

            $this->logBackup($filename, 'success', 'Backup berhasil');

            return response()->json([
                'success' => true,
                'message' => 'Backup berhasil',
                'filename' => $filename,
                'downloadUrl' => route('pengaturan.backup.download', ['filename' => $filename])
            ]);

        } catch (\Exception $e) {
            \Log::error('Backup failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Backup database gagal diproses.'
            ]);
        }
    }

    public function download($filename)
    {
        $this->requireAdminOrAudit();

        $filepath = $this->backupPath . '/' . basename($filename);

        if (!file_exists($filepath)) {
            return redirect()->route('pengaturan.backup.index')->with('error', 'File tidak ditemukan');
        }
        if (filesize($filepath) === 0) {
            return redirect()->route('pengaturan.backup.index')->with('error', 'File backup kosong');
        }

        return response()->download($filepath);
    }

    public function deleteBackup($filename)
    {
        $this->requireAdminOnly();

        $filepath = $this->backupPath . '/' . basename($filename);

        if (!file_exists($filepath)) {
            return redirect()->route('pengaturan.backup.index')->with('error', 'File tidak ditemukan');
        }

        unlink($filepath);
        $this->logBackup($filename, 'deleted', 'Backup dihapus');

        return redirect()->route('pengaturan.backup.index')->with('success', 'Backup berhasil dihapus');
    }

    private function generateDatabaseDump(): string
    {
        $output = "-- Database Backup\n";
        $output .= "-- Created: " . date('Y-m-d H:i:s') . "\n";
        $output .= "-- User: " . auth()->user()->nama . " (" . auth()->user()->username . ")\n";
        $output .= "-- Database: " . DB::getDatabaseName() . "\n\n";

        $output .= "SET session_replication_role = 'replica';\n\n";

        $tables = DB::select(
            "SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' AND table_name != 'migrations' ORDER BY table_name"
        );

        $allTables = array_map(function ($t) { return $t->table_name; }, $tables);
        $reverseTables = array_reverse($allTables);

        foreach ($reverseTables as $tableName) {
            $output .= "DROP TABLE IF EXISTS {$tableName} CASCADE;\n";
        }
        $output .= "\n";

        foreach ($allTables as $tableName) {
            $createSql = $this->getCreateTableSql($tableName);
            $output .= $createSql . ";\n\n";
        }

        foreach ($allTables as $tableName) {
            DB::table($tableName)->orderBy(DB::raw('1'))->chunk(500, function ($rows) use (&$output, $tableName) {
                if ($rows->isEmpty()) return;

                $columns = implode(', ', array_keys((array)$rows->first()));

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
            });
            $output .= "\n";
        }

        $output .= "SET session_replication_role = 'origin';\n";

        return $output;
    }

    private function getCreateTableSql(string $tableName): string
    {
        $columns = DB::select(
            "SELECT column_name, data_type, character_maximum_length, is_nullable, column_default, ordinal_position
             FROM information_schema.columns
             WHERE table_schema = 'public' AND table_name = ?
             ORDER BY ordinal_position",
            [$tableName]
        );

        $pkCols = DB::select(
            "SELECT kcu.column_name
             FROM information_schema.table_constraints tc
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

        $sql .= ")";

        return $sql;
    }

    private function getBackupList(): array
    {
        $backups = [];

        if (!is_dir($this->backupPath)) {
            return $backups;
        }

        $files = scandir($this->backupPath);

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;

            $filepath = $this->backupPath . '/' . $file;

            if (is_file($filepath) && preg_match('/\.sql$/', $file)) {
                $backups[] = [
                    'filename' => $file,
                    'size' => filesize($filepath),
                    'created' => filectime($filepath),
                    'path' => $filepath,
                ];
            }
        }

        usort($backups, function ($a, $b) {
            return $b['created'] - $a['created'];
        });

        return $backups;
    }

    private function logBackup(string $filename, string $status, string $message): void
    {
        try {
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'backup',
                'table_name' => 'system',
                'reference_id' => null,
                'description' => "Backup: {$filename} - {$status}",
                'data' => json_encode([
                    'filename' => $filename,
                    'status' => $status,
                    'message' => $message,
                ]),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            \Log::error('Failed to log backup: ' . $e->getMessage());
        }
    }
}
