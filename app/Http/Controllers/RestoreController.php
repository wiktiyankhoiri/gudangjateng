<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;
use App\Services\DatabaseBackupService;

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

        $service = app(DatabaseBackupService::class);

        try {
            // Step 1: Create safety backup before restore
            $safetyFilename = 'safety-backup-' . date('Y-m-d-His') . '.sql';
            $safetySql = $service->generateDump();
            file_put_contents($this->backupPath . '/' . $safetyFilename, $safetySql);

            // Step 2: Execute restore (atomic — all in one transaction)
            $result = $service->restore($filepath);

            // Step 3: Log the result
            $this->logRestore($backupFile, 'success', "Restore: {$result['successQueries']} berhasil, {$result['failedQueries']} gagal");

            $message = 'Restore berhasil. Safety backup: ' . $safetyFilename;

            if ($result['rolledBack']) {
                // Restore was rolled back due to query failures
                $message .= " | ⚠️ Restore di-rollback karena {$result['failedQueries']} query gagal.";
                return redirect()->route('pengaturan.backup.index')
                    ->with('warning', $message)
                    ->with('restore_errors', $result['errors']);
            }

            if ($result['failedQueries'] > 0) {
                $message .= " | ⚠️ {$result['failedQueries']} query gagal.";
                return redirect()->route('pengaturan.backup.index')
                    ->with('warning', $message)
                    ->with('restore_errors', $result['errors']);
            }

            $message .= " | ✅ Semua berhasil.";
            return redirect()->route('pengaturan.backup.index')->with('success', $message);

        } catch (\Throwable $e) {
            \Log::error('Restore failed: ' . $e->getMessage());
            return redirect()->route('pengaturan.backup.restore')->with('error', 'Restore gagal: ' . $e->getMessage());
        }
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
}
