@extends('layouts.app')

@section('content')

<!-- Maintenance Actions -->
<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Maintenance</h3>
    </div>
    <div class="p-5">
        <div class="flex flex-wrap gap-3">
            {{-- Cleanup Logs --}}
            <form method="post" action="{{ route('pengaturan.tentangsistem.cleanup-logs') }}" data-confirm-message="Hapus file log yang berusia >30 hari?" data-confirm-ok="Ya, Hapus">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-warning-500 px-4 py-3 text-sm font-medium text-white transition hover:bg-warning-600">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M7.5 2.5C7.5 2.08579 7.83579 1.75 8.25 1.75H11.75C12.1642 1.75 12.5 2.08579 12.5 2.5V3.5H6.25V2.5H7.5ZM4.75 4.5V3.5C4.75 2.25736 5.75736 1.25 7 1.25H13C14.2426 1.25 15.25 2.25736 15.25 3.5V4.5H16.5C17.1904 4.5 17.75 5.05964 17.75 5.75C17.75 6.44036 17.1904 7 16.5 7H16.1273L15.2865 16.2885C15.1399 17.8235 13.8455 19 12.3043 19H7.6957C6.15448 19 4.86011 17.8235 4.71346 16.2885L3.87273 7H3.5C2.80964 7 2.25 6.44036 2.25 5.75C2.25 5.05964 2.80964 4.5 3.5 4.5H4.75ZM6.21817 7L7.04185 16.1443C7.07708 16.5095 7.39145 16.75 7.6957 16.75H12.3043C12.6086 16.75 12.9229 16.5095 12.9582 16.1443L13.7818 7H6.21817Z" fill="currentColor"/></svg>
                    Cleanup Logs (&gt;30 hari)
                </button>
            </form>

            {{-- Hapus Audit Logs --}}
            <form method="post" action="{{ route('pengaturan.tentangsistem.prune-audit') }}" data-confirm-message="Hapus audit log yang berusia >90 hari?" data-confirm-ok="Ya, Hapus">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-error-500 px-4 py-3 text-sm font-medium text-white transition hover:bg-error-600">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M7.5 2.5C7.5 2.08579 7.83579 1.75 8.25 1.75H11.75C12.1642 1.75 12.5 2.08579 12.5 2.5V3.5H6.25V2.5H7.5ZM4.75 4.5V3.5C4.75 2.25736 5.75736 1.25 7 1.25H13C14.2426 1.25 15.25 2.25736 15.25 3.5V4.5H16.5C17.1904 4.5 17.75 5.05964 17.75 5.75C17.75 6.44036 17.1904 7 16.5 7H16.1273L15.2865 16.2885C15.1399 17.8235 13.8455 19 12.3043 19H7.6957C6.15448 19 4.86011 17.8235 4.71346 16.2885L3.87273 7H3.5C2.80964 7 2.25 6.44036 2.25 5.75C2.25 5.05964 2.80964 4.5 3.5 4.5H4.75ZM6.21817 7L7.04185 16.1443C7.07708 16.5095 7.39145 16.75 7.6957 16.75H12.3043C12.6086 16.75 12.9229 16.5095 12.9582 16.1443L13.7818 7H6.21817Z" fill="currentColor"/></svg>
                    Hapus Audit Log (&gt;90 hari)
                </button>
            </form>

            {{-- Clear Cache --}}
            <form method="post" action="{{ route('pengaturan.tentangsistem.clear-cache') }}" data-confirm-message="Bersihkan cache aplikasi (config, view, data)?" data-confirm-ok="Ya, Bersihkan">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white transition hover:bg-brand-600">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M10 2.5C10 2.22386 10.2239 2 10.5 2H14.5C14.7761 2 15 2.22386 15 2.5V5.5C15 5.77614 14.7761 6 14.5 6C14.2239 6 14 5.77614 14 5.5V3.62207C12.621 3.02563 11.1182 2.75 9.5 2.75C5.35786 2.75 2 6.10786 2 10.25C2 14.3921 5.35786 17.75 9.5 17.75C13.6421 17.75 17 14.3921 17 10.25C17 9.97386 17.2239 9.75 17.5 9.75C17.7761 9.75 18 9.97386 18 10.25C18 14.9444 14.1944 18.75 9.5 18.75C4.80558 18.75 1 14.9444 1 10.25C1 5.55558 4.80558 1.75 9.5 1.75C11.1758 1.75 12.7903 2.03536 14.2654 2.64319L14.0089 2.53736L14.0718 2.98262L14.1289 3.35575L14.4153 4.14104L14.7466 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                    Clear Cache
                </button>
            </form>
        </div>
    </div>
</div>

<!-- System Health -->
<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] mt-6">
    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">System Health</h3>
    </div>
    <div class="p-5">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400">PHP Version</p>
                <p class="mt-1 text-sm font-semibold text-gray-800 dark:text-white/90">{{ $phpVersion }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400">Laravel Version</p>
                <p class="mt-1 text-sm font-semibold text-gray-800 dark:text-white/90">{{ $laravelVersion }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400">Environment</p>
                <p class="mt-1 text-sm font-semibold text-gray-800 dark:text-white/90">{{ $environment }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400">Cache Driver</p>
                <p class="mt-1 text-sm font-semibold text-gray-800 dark:text-white/90">{{ $cacheDriver }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400">DB Driver</p>
                <p class="mt-1 text-sm font-semibold text-gray-800 dark:text-white/90">{{ ucfirst($dbDriver) }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400">Database</p>
                <p class="mt-1 text-sm font-semibold text-gray-800 dark:text-white/90">{{ $dbName }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400">DB Size</p>
                <p class="mt-1 text-sm font-semibold text-gray-800 dark:text-white/90">
                    {{ $dbSize ? round($dbSize / 1024 / 1024, 1) . ' MB' : 'N/A' }}
                </p>
            </div>
            <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400">Error Rate (24j)</p>
                <p class="mt-1 text-sm font-semibold text-gray-800 dark:text-white/90">{{ $errorCount24h }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Penyimpanan -->
<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] mt-6">
    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Penyimpanan</h3>
    </div>
    <div class="p-5">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400">Log Files</p>
                <p class="mt-1 text-sm font-semibold text-gray-800 dark:text-white/90">
                    {{ $totalLogFiles }} file / {{ round($totalLogSize / 1024, 1) }} KB
                </p>
            </div>
            <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400">Backup Files</p>
                <p class="mt-1 text-sm font-semibold text-gray-800 dark:text-white/90">
                    {{ $totalBackups }} file / {{ round($totalBackupSize / 1024, 1) }} KB
                </p>
            </div>
            <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400">Backup Terakhir</p>
                <p class="mt-1 text-sm font-semibold text-gray-800 dark:text-white/90">
                    @if($lastBackup)
                        {{ $lastBackup['file'] ?? '-' }}<br>
                        <span class="text-xs text-gray-500">{{ date('d/m/Y H:i', $lastBackup['time'] ?? 0) }}</span>
                    @else
                        Belum ada
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Statistik Data -->
<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] mt-6">
    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Statistik Data</h3>
    </div>
    <div class="p-5">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="rounded-xl border border-gray-200 p-5 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Total User</p>
                <p class="text-xl font-bold text-gray-800 dark:text-white/90 mb-2">{{ number_format($totalUsers) }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500">
                    @foreach($roleCounts as $role => $cnt)
                    {{ ucfirst($role) }}: {{ $cnt }}@if(!$loop->last) &middot; @endif
                    @endforeach
                </p>
            </div>
            <div class="rounded-xl border border-gray-200 p-5 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Total Audit Log</p>
                <p class="text-xl font-bold text-gray-800 dark:text-white/90 mb-2">{{ number_format($totalAuditLogs) }}</p>
                @if($oldestAuditLog)
                <p class="text-xs text-gray-400 dark:text-gray-500">
                    Log tertua: <span class="font-medium text-gray-600 dark:text-gray-300">{{ $oldestAuditLog->created_at->format('d M Y') }}</span>
                </p>
                @endif
            </div>
            <div class="rounded-xl border border-gray-200 p-5 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Database</p>
                <p class="text-xl font-bold text-gray-800 dark:text-white/90 mb-2">{{ $dbName }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500">
                    {{ ucfirst($dbDriver) }} &middot; {{ $dbSize ? round($dbSize / 1024 / 1024, 1) . ' MB' : 'N/A' }} &middot; {{ count($tableCounts) }} tabel
                </p>
            </div>
        </div>

        <h4 class="mt-6 mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Jumlah Record per Tabel</h4>
        @php
            $tableLabels = [
                'users' => 'User', 'barang' => 'Barang', 'toko' => 'Toko', 'pabrik' => 'Pabrik',
                'stok' => 'Stok', 'initialstok' => 'Initial Stok',
                'barang_masuk' => 'Barang Masuk', 'barang_masuk_detail' => 'Detail Barang Masuk',
                'barang_keluar' => 'Barang Keluar', 'barang_keluar_detail' => 'Detail Barang Keluar',
                'mutasi' => 'Mutasi', 'mutasi_detail' => 'Detail Mutasi',
                'audit_log' => 'Audit Log', 'penyesuaian_stok' => 'Penyesuaian Stok',
                'stok_opname' => 'Stok Opname', 'stok_opname_detail' => 'Detail Opname',
                'notifications' => 'Notifikasi',
                'password_reset_tokens' => 'Reset Token', 'sessions' => 'Session',
                'cache' => 'Cache', 'cache_locks' => 'Cache Lock',
                'jobs' => 'Job Queue', 'job_batches' => 'Job Batch', 'failed_jobs' => 'Failed Job',
            ];
        @endphp
        <div class="overflow-x-auto border border-gray-200 rounded-lg dark:border-gray-700">
            <table class="min-w-full">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-2.5 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">#</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Tabel</th>
                        <th class="px-4 py-2.5 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @php $no = 1; @endphp
                    @foreach($tableCounts as $table => $count)
                    <tr class="{{ $count > 0 ? '' : 'opacity-40' }}">
                        <td class="px-4 py-2 text-center text-xs text-gray-400 dark:text-gray-500">{{ $no++ }}</td>
                        <td class="px-4 py-2 text-sm text-gray-800 dark:text-white/90">{{ $tableLabels[$table] ?? $table }}</td>
                        <td class="px-4 py-2 text-center text-sm font-mono font-semibold {{ $count > 0 ? 'text-gray-800 dark:text-white/90' : 'text-gray-400' }}">{{ number_format($count) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
