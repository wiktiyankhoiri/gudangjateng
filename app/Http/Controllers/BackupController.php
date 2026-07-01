<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Services\DatabaseBackupService;

class BackupController extends Controller
{
    protected $backupPath;

    public function __construct()
    {
        $this->backupPath = storage_path('app/backups');

        if (! is_dir($this->backupPath)) {
            mkdir($this->backupPath, 0755, true);
        }
    }

    protected function requireAdminOrAudit(): void
    {
        $role = auth()->user()->role;
        if (! in_array($role, ['admin', 'audit', 'super_admin'])) {
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

        if (! empty($backups)) {
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
            $filename = 'gudangjateng-backup-'.date('Y-m-d-His').'.sql';
            $filepath = $this->backupPath.'/'.$filename;

            $sql = app(DatabaseBackupService::class)->generateDump();

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
                        \Log::info('Backup rotation: hapus '.$oldBackup['filename']);
                    }
                }
            }

            $this->logBackup($filename, 'success', 'Backup berhasil');

            return response()->json([
                'success' => true,
                'message' => 'Backup berhasil',
                'filename' => $filename,
                'downloadUrl' => route('pengaturan.backup.download', ['filename' => $filename]),
            ]);

        } catch (\Exception $e) {
            \Log::error('Backup failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Backup database gagal diproses.',
            ]);
        }
    }

    public function download($filename)
    {
        $this->requireAdminOrAudit();

        $filepath = $this->backupPath.'/'.basename($filename);

        if (! file_exists($filepath)) {
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

        $filepath = $this->backupPath.'/'.basename($filename);

        if (! file_exists($filepath)) {
            return redirect()->route('pengaturan.backup.index')->with('error', 'File tidak ditemukan');
        }

        unlink($filepath);
        $this->logBackup($filename, 'deleted', 'Backup dihapus');

        return redirect()->route('pengaturan.backup.index')->with('success', 'Backup berhasil dihapus');
    }

    private function getBackupList(): array
    {
        $backups = [];

        if (! is_dir($this->backupPath)) {
            return $backups;
        }

        $files = scandir($this->backupPath);

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $filepath = $this->backupPath.'/'.$file;

            if (is_file($filepath) && preg_match('/\.sql$/', $file)) {
                $backups[] = [
                    'filename' => $file,
                    'size' => filesize($filepath),
                    'created' => $this->getBackupTimestamp($file, $filepath),
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
            \Log::error('Failed to log backup: '.$e->getMessage());
        }
    }

    private function getBackupTimestamp(string $filename, string $filepath): int
    {
        if (preg_match('/^(?:safety-)?gudangjateng-backup-(\d{4}-\d{2}-\d{2})-(\d{6})\.sql$/', $filename, $matches)) {
            $timestamp = strtotime($matches[1].' '.substr($matches[2], 0, 2).':'.substr($matches[2], 2, 2).':'.substr($matches[2], 4, 2));

            if ($timestamp !== false) {
                return $timestamp;
            }
        }

        return filemtime($filepath);
    }
}
