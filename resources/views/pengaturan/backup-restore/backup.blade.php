@extends('layouts.app')

@section('content')

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 mb-6">
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Backup</p>
                <h4 class="mt-1 text-sm font-semibold text-gray-800 dark:text-white/90">{{ $totalFiles ?? 0 }}</h4>
            </div>
        </div>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Ukuran</p>
                <h4 class="mt-1 text-sm font-semibold text-gray-800 dark:text-white/90">{{ number_format(($totalSize ?? 0) / 1024, 1) }} KB</h4>
            </div>
        </div>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Backup Terakhir</p>
                <h4 class="mt-1 text-sm font-semibold text-gray-800 dark:text-white/90">
                    {{ $lastBackup ? date('d/m/Y H:i', $lastBackup['created']) : '-' }}
                </h4>
            </div>
        </div>
    </div>
</div>

<!-- Restore Error Notification -->
@if(session('restore_errors'))
<div class="mb-6 rounded-2xl border border-warning-200 bg-warning-50 p-5 dark:border-warning-800 dark:bg-warning-500/10">
    <div class="flex items-start gap-3">
        <div class="flex-shrink-0 flex h-10 w-10 items-center justify-center rounded-lg bg-warning-500">
            <svg class="fill-white" width="18" height="18" viewBox="0 0 22 22" xmlns="http://www.w3.org/2000/svg">
                <path d="M11 0C4.93 0 0 4.93 0 11s4.93 11 11 11 11-4.93 11-11S17.07 0 11 0zm0 16.5a1.1 1.1 0 110-2.2 1.1 1.1 0 010 2.2zm.9-4.95a.9.9 0 01-1.8 0V5.5a.9.9 0 011.8 0v6.05z"/>
            </svg>
        </div>
        <div class="flex-1">
            <h5 class="mb-2 text-sm font-semibold text-warning-800 dark:text-warning-400">Restore Selesai dengan Peringatan</h5>
            <p class="mb-3 text-xs text-warning-700 dark:text-warning-300">Beberapa query gagal dieksekusi. Database mungkin tidak sepenuhnya konsisten. Periksa log untuk detail.</p>
            <details class="text-xs">
                <summary class="cursor-pointer text-warning-700 dark:text-warning-300 hover:text-warning-800 dark:hover:text-warning-200">Lihat {{ count(session('restore_errors')) }} error</summary>
                <ul class="mt-2 space-y-1 max-h-40 overflow-y-auto">
                    @foreach(session('restore_errors') as $err)
                    <li class="text-warning-600 dark:text-warning-400 bg-warning-100/50 dark:bg-warning-500/5 px-2 py-1 rounded">• {{ esc($err) }}</li>
                    @endforeach
                </ul>
            </details>
        </div>
    </div>
</div>
@endif

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Riwayat Backup</h3>
                <span class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-1 text-xs font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">{{ $totalFiles ?? 0 }} file</span>
            </div>
            <button type="button" id="btnBackup"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600 w-full sm:w-auto">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10 3.125C10.4142 3.125 10.75 3.46079 10.75 3.875V12.4822L13.6161 9.61612C13.909 9.32322 14.3839 9.32322 14.6768 9.61612C14.9697 9.90901 14.9697 10.3839 14.6768 10.6768L10.6768 14.6768C10.3839 14.9697 9.90901 14.9697 9.61612 14.6768L5.61612 10.6768C5.32322 10.3839 5.32322 9.90901 5.61612 9.61612C5.90901 9.32322 6.38388 9.32322 6.67678 9.61612L9.25 12.1893V3.875C9.25 3.46079 9.58579 3.125 10 3.125ZM4.5 11C4.91421 11 5.25 11.3358 5.25 11.75V15C5.25 15.6904 5.80964 16.25 6.5 16.25H13.5C14.1904 16.25 14.75 15.6904 14.75 15V11.75C14.75 11.3358 15.0858 11 15.5 11C15.9142 11 16.25 11.3358 16.25 11.75V15C16.25 16.5188 15.0188 17.75 13.5 17.75H6.5C4.98122 17.75 3.75 16.5188 3.75 15V11.75C3.75 11.3358 4.08579 11 4.5 11Z" fill="currentColor"/>
                </svg>
                Backup Sekarang
            </button>
        </div>
    </div>
    <div id="backupStatus"></div>
    <div class="overflow-x-auto p-5">
        <table class="table-sticky min-w-full">
            <thead>
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400 w-[60px]">NO</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">NAMA FILE</th>
                    <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 uppercase dark:text-gray-400">UKURAN</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">TANGGAL</th>
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400 w-[120px]">AKSI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($backups ?? [] as $i => $b)
                <tr>
                    <td class="px-5 py-4 text-sm text-center text-gray-800 dark:text-white/90">{{ $i + 1 }}</td>
                    <td class="px-5 py-4">
                        <span class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-1 text-xs font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">{{ esc($b['filename']) }}</span>
                    </td>
                    <td class="px-5 py-4 text-sm text-right text-gray-800 dark:text-white/90">{{ number_format($b['size'] / 1024, 1) }} KB</td>
                    <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ date('d/m/Y H:i', $b['created']) }}</td>
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('pengaturan.backup.download', ['filename' => $b['filename']]) }}" class="inline-flex items-center justify-center rounded-lg bg-success-50 p-2 text-success-600 hover:bg-success-100 dark:bg-success-500/15 dark:text-success-400">
                                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10 3.125C10.4142 3.125 10.75 3.46079 10.75 3.875V12.4822L13.6161 9.61612C13.909 9.32322 14.3839 9.32322 14.6768 9.61612C14.9697 9.90901 14.9697 10.3839 14.6768 10.6768L10.6768 14.6768C10.3839 14.9697 9.90901 14.9697 9.61612 14.6768L5.61612 10.6768C5.32322 10.3839 5.32322 9.90901 5.61612 9.61612C5.90901 9.32322 6.38388 9.32322 6.67678 9.61612L9.25 12.1893V3.875C9.25 3.46079 9.58579 3.125 10 3.125ZM4.5 11C4.91421 11 5.25 11.3358 5.25 11.75V15C5.25 15.6904 5.80964 16.25 6.5 16.25H13.5C14.1904 16.25 14.75 15.6904 14.75 15V11.75C14.75 11.3358 15.0858 11 15.5 11C15.9142 11 16.25 11.3358 16.25 11.75V15C16.25 16.5188 15.0188 17.75 13.5 17.75H6.5C4.98122 17.75 3.75 16.5188 3.75 15V11.75C3.75 11.3358 4.08579 11 4.5 11Z" fill="currentColor"/>
                                </svg>
                            </a>
                            <form method="post" action="{{ route('pengaturan.backup.delete', ['filename' => $b['filename']]) }}" class="inline" data-confirm-message="Yakin ingin menghapus file backup ini?" data-confirm-ok="Ya, Hapus">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-error-50 p-2 text-error-600 hover:bg-error-100 dark:bg-error-500/15 dark:text-error-400">
                                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M7.5 2.5C7.5 2.08579 7.83579 1.75 8.25 1.75H11.75C12.1642 1.75 12.5 2.08579 12.5 2.5V3.5H6.25V2.5H7.5ZM4.75 4.5V3.5C4.75 2.25736 5.75736 1.25 7 1.25H13C14.2426 1.25 15.25 2.25736 15.25 3.5V4.5H16.5C17.1904 4.5 17.75 5.05964 17.75 5.75C17.75 6.44036 17.1904 7 16.5 7H16.1273L15.2865 16.2885C15.1399 17.8235 13.8455 19 12.3043 19H7.6957C6.15448 19 4.86011 17.8235 4.71346 16.2885L3.87273 7H3.5C2.80964 7 2.25 6.44036 2.25 5.75C2.25 5.05964 2.80964 4.5 3.5 4.5H4.75ZM6.21817 7L7.04185 16.1443C7.07708 16.5095 7.39145 16.75 7.6957 16.75H12.3043C12.6086 16.75 12.9229 16.5095 12.9582 16.1443L13.7818 7H6.21817Z" fill="currentColor"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada backup</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
document.getElementById('btnBackup')?.addEventListener('click', async function() {
    const status = document.getElementById('backupStatus');
    status.innerHTML = '<div class="mx-5 mt-4 flex items-center gap-2 rounded-lg border border-brand-200 bg-brand-50 px-4 py-3 dark:border-brand-500/20 dark:bg-brand-500/15"><svg class="animate-spin h-4 w-4 text-brand-600 dark:text-brand-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg><span class="text-theme-sm font-medium text-brand-700 dark:text-brand-400">Memproses backup...</span></div>';
    this.disabled = true;

    try {
        const response = await fetch("{{ route('pengaturan.backup.do') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });
        const result = await response.json();

        if (result.success) {
            status.innerHTML = '<div class="mx-5 mt-4 rounded-lg border border-success-200 bg-success-50 px-4 py-3 dark:border-success-500/20 dark:bg-success-500/15"><p class="text-theme-sm font-medium text-success-700 dark:text-success-400">' + result.message + '</p></div>';
            setTimeout(() => location.reload(), 1500);
        } else {
            status.innerHTML = '<div class="mx-5 mt-4 rounded-lg border border-error-200 bg-error-50 px-4 py-3 dark:border-error-500/20 dark:bg-error-500/15"><p class="text-theme-sm font-medium text-error-700 dark:text-error-400">' + result.message + '</p></div>';
        }
    } catch (e) {
        status.innerHTML = '<div class="mx-5 mt-4 rounded-lg border border-error-200 bg-error-50 px-4 py-3 dark:border-error-500/20 dark:bg-error-500/15"><p class="text-theme-sm font-medium text-error-700 dark:text-error-400">Backup gagal.</p></div>';
    }

    this.disabled = false;
});
</script>
@endsection
