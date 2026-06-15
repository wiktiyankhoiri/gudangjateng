<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class TentangSistemController extends Controller
{
    public function index()
    {
        $this->requireAdminOrAudit();

        $phpVersion = PHP_VERSION;
        $laravelVersion = app()->version();
        $environment = config('app.env');
        $dbDriver = DB::getDriverName();
        $dbName = DB::getDatabaseName();
        $cacheDriver = config('cache.default');

        // DB size
        $dbSize = null;
        try {
            $result = DB::select("SELECT pg_database_size(?) as size", [$dbName]);
            $dbSize = $result[0]->size ?? null;
        } catch (\Throwable) {}

        // Table counts
        $tableCounts = [];
        try {
            $tables = DB::select("SELECT tablename FROM pg_catalog.pg_tables WHERE schemaname = 'public' AND tablename != 'migrations'");
            foreach ($tables as $t) {
                $tableCounts[$t->tablename] = DB::table($t->tablename)->count();
            }
        } catch (\Throwable) {}

        // Backup stats
        $backupPath = storage_path('app/backups');
        $backupFiles = glob($backupPath . '/backup-*.sql');
        $totalBackups = count($backupFiles);
        $totalBackupSize = 0;
        $lastBackup = null;
        foreach ($backupFiles as $f) {
            $size = filesize($f);
            $totalBackupSize += $size;
            $mtime = filemtime($f);
            if ($lastBackup === null || $mtime > $lastBackup['time']) {
                $lastBackup = ['file' => basename($f), 'time' => $mtime, 'size' => $size];
            }
        }

        // Log stats
        $logPath = storage_path('logs');
        $logFiles = glob($logPath . '/*.log');
        $totalLogFiles = count($logFiles);
        $totalLogSize = 0;
        $errorCount24h = 0;
        foreach ($logFiles as $f) {
            $totalLogSize += filesize($f);
            if (filemtime($f) > strtotime('-1 day')) {
                $content = @file_get_contents($f);
                if ($content) {
                    $errorCount24h += substr_count(strtolower($content), 'error');
                }
            }
        }

        // Audit log stats
        $totalAuditLogs = \App\Models\AuditLog::count();
        $oldestAuditLog = \App\Models\AuditLog::oldest('created_at')->first();

        // User stats
        $totalUsers = \App\Models\User::count();
        $roleCounts = \App\Models\User::selectRaw('role, COUNT(*) as total')
            ->groupBy('role')->pluck('total', 'role')->toArray();

        return view('pengaturan.tentang-sistem.index', [
            'title' => 'Tentang Sistem',
            'phpVersion' => $phpVersion,
            'laravelVersion' => $laravelVersion,
            'environment' => $environment,
            'cacheDriver' => $cacheDriver,
            'dbDriver' => $dbDriver,
            'dbName' => $dbName,
            'dbSize' => $dbSize,
            'tableCounts' => $tableCounts,
            'totalBackups' => $totalBackups,
            'totalBackupSize' => $totalBackupSize,
            'lastBackup' => $lastBackup,
            'totalLogFiles' => $totalLogFiles,
            'totalLogSize' => $totalLogSize,
            'errorCount24h' => $errorCount24h,
            'totalAuditLogs' => $totalAuditLogs,
            'oldestAuditLog' => $oldestAuditLog,
            'totalUsers' => $totalUsers,
            'roleCounts' => $roleCounts,
        ]);
    }

    public function pruneAuditLogs(Request $request)
    {
        $this->requireAdminOrAudit();

        $days = (int) ($request->input('days', 90));
        $cutoff = now()->subDays($days);
        $deleted = \App\Models\AuditLog::where('created_at', '<', $cutoff)->delete();

        return redirect()->route('pengaturan.tentangsistem.index')
            ->with('success', "{$deleted} audit log (> {$days} hari) berhasil dihapus.");
    }

    public function clearCache()
    {
        $this->requireAdminOrAudit();

        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('config:clear');

        return redirect()->route('pengaturan.tentangsistem.index')
            ->with('success', 'Cache aplikasi berhasil dibersihkan.');
    }

    public function cleanupLogs()
    {
        $this->requireAdminOrAudit();

        $logPath = storage_path('logs');
        $deleted = 0;
        $size = 0;
        $cutoff = strtotime('-30 days');

        $files = glob($logPath . '/*.log');

        foreach ($files as $file) {
            if (filemtime($file) < $cutoff) {
                $size += filesize($file);
                unlink($file);
                $deleted++;
            }
        }

        \Log::info('Cleanup logs: ' . $deleted . ' files, ' . round($size / 1024, 1) . ' KB freed');

        return redirect()->route('pengaturan.tentangsistem.index')
            ->with('success', $deleted . ' file log lama berhasil dihapus (' . round($size / 1024, 1) . ' KB)');
    }

    protected function requireAdminOrAudit(): void
    {
        if (!in_array(auth()->user()->role, ['admin', 'audit', 'super_admin'])) {
            abort(404);
        }
    }
}
