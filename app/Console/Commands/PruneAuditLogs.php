<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PruneAuditLogs extends Command
{
    protected $signature = 'app:prune-audit-logs {--days=90 : Hapus audit log lebih dari N hari}';
    protected $description = 'Hapus audit log yang lebih dari 90 hari';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);

        $deleted = DB::table('audit_log')
            ->where('created_at', '<', $cutoff)
            ->delete();

        $this->info("{$deleted} audit log (>{$days} hari) berhasil dihapus.");
        \Log::info("PruneAuditLogs: {$deleted} logs older than {$days} days deleted.");

        return self::SUCCESS;
    }
}
