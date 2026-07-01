@include('dashboard.admin')

<div class="mt-6 md:mt-8 grid grid-cols-12 gap-4 md:gap-6">
    <div class="col-span-12 lg:col-span-6">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-800">
                <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400">Aksi Cepat</h3>
                <a href="{{ route('pengaturan.tentangsistem.index') }}" class="text-theme-sm font-medium text-brand-500 hover:text-brand-600">Tentang Sistem</a>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('pengaturan.backup.index') }}" class="flex items-center gap-3 rounded-xl border border-gray-200 p-4 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-white/5">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-50 dark:bg-green-500/15">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                        </span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Backup DB</span>
                    </a>
                    <a href="{{ route('pengaturan.user.index') }}" class="flex items-center gap-3 rounded-xl border border-gray-200 p-4 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-white/5">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-50 dark:bg-purple-500/15">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0Zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0Z"/></svg>
                        </span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">User</span>
                    </a>
                    <a href="{{ route('pengaturan.auditlog.index') }}" class="flex items-center gap-3 rounded-xl border border-gray-200 p-4 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-white/5">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 dark:bg-amber-500/15">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                        </span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Audit Log</span>
                    </a>
                    <a href="{{ route('pengaturan.tentangsistem.index') }}" class="flex items-center gap-3 rounded-xl border border-gray-200 p-4 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-white/5">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand-50 dark:bg-brand-500/15">
                            <svg class="w-5 h-5 text-brand-600 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Sistem</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-span-12 lg:col-span-6">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-800">
                <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400">Audit Log Terbaru</h3>
                <a href="{{ route('pengaturan.auditlog.index') }}" class="text-theme-sm font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400">Lihat Semua</a>
            </div>
            <div class="p-5">
                <div class="flex flex-col gap-3">
                    @forelse($auditLogTerbaru->take(5) as $log)
                    <div class="flex items-start gap-3 border-b border-gray-100 pb-3 dark:border-gray-800 last:border-b-0 last:pb-0">
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ ['create' => 'bg-green-50 text-green-700', 'update' => 'bg-brand-50 text-brand-700', 'delete' => 'bg-error-50 text-error-700', 'login' => 'bg-blue-50 text-blue-700'][$log->action] ?? 'bg-gray-50 text-gray-600' }}">
                            {{ ucfirst($log->action) }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ esc($log->description) }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ esc($log->user_nama ?? 'System') }} &middot; {{ $log->created_at?->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4"><p class="text-sm text-gray-500 dark:text-gray-400">Belum ada log audit</p></div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
