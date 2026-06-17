<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DatabaseBackupService;

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

            $sql = app(DatabaseBackupService::class)->generateDump();
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
}
