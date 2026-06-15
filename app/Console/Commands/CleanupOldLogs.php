<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanupOldLogs extends Command
{
    protected $signature = 'app:cleanup-old-logs {--days=30 : Delete log files older than N days}';
    protected $description = 'Hapus file log Laravel yang lebih dari 30 hari';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $logPath = storage_path('logs');
        $cutoff = strtotime("-{$days} days");
        $deleted = 0;
        $size = 0;

        $files = glob($logPath . '/*.log');

        foreach ($files as $file) {
            if (filemtime($file) < $cutoff) {
                $size += filesize($file);
                unlink($file);
                $deleted++;
            }
        }

        $this->info("{$deleted} file log (>{$days} hari) berhasil dihapus (" . round($size / 1024, 1) . " KB).");
        \Log::info("CleanupOldLogs: {$deleted} log files older than {$days} days deleted (" . round($size / 1024, 1) . " KB).");

        return self::SUCCESS;
    }
}
