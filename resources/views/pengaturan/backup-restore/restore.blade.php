@extends('layouts.app')

@section('content')

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Form Restore</h3>
    </div>
    <div class="p-5">
        <div class="mb-6 rounded-lg border-l-4 border-l-error-500 bg-error-50 p-5 dark:bg-error-900/20">
            <div class="flex gap-4">
                <svg class="flex-shrink-0 mt-1 text-error-600" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2.25C6.61522 2.25 2.25 6.61522 2.25 12C2.25 17.3848 6.61522 21.75 12 21.75C17.3848 21.75 21.75 17.3848 21.75 12C21.75 6.61522 17.3848 2.25 12 2.25ZM3.75 12C3.75 7.44365 7.44365 3.75 12 3.75C16.5563 3.75 20.25 7.44365 20.25 12C20.25 16.5563 16.5563 20.25 12 20.25C7.44365 20.25 3.75 16.5563 3.75 12ZM12 7.25C12.4142 7.25 12.75 7.58579 12.75 8V12C12.75 12.4142 12.4142 12.75 12 12.75C11.5858 12.75 11.25 12.4142 11.25 12V8C11.25 7.58579 11.5858 7.25 12 7.25ZM12 15.75C12.4142 15.75 12.75 15.4142 12.75 15C12.75 14.5858 12.4142 14.25 12 14.25C11.5858 14.25 11.25 14.5858 11.25 15C11.25 15.4142 11.5858 15.75 12 15.75Z" fill="currentColor"/>
                </svg>
                <div>
                    <h5 class="font-semibold text-error-600 dark:text-error-400 mb-2">PERHATIAN!</h5>
                    <p class="text-sm text-error-600/80 dark:text-error-400/80 mb-2">Restore database akan menimpa seluruh data saat ini. Sangat disarankan melakukan backup database terbaru sebelum restore.</p>
                    <hr class="border-error-300 dark:border-error-700 mb-2">
                    <p class="text-xs font-medium text-error-600 dark:text-error-400 mb-1">Pastikan:</p>
                    <ul class="text-xs text-error-600/80 dark:text-error-400/80 ps-4 mb-3 list-disc">
                        <li>semua user sudah logout</li>
                        <li>tidak ada transaksi berjalan</li>
                        <li>backup terbaru sudah tersedia</li>
                    </ul>
                    <a href="{{ route('pengaturan.backup.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 3.125C10.4142 3.125 10.75 3.46079 10.75 3.875V12.4822L13.6161 9.61612C13.909 9.32322 14.3839 9.32322 14.6768 9.61612C14.9697 9.90901 14.9697 10.3839 14.6768 10.6768L10.6768 14.6768C10.3839 14.9697 9.90901 14.9697 9.61612 14.6768L5.61612 10.6768C5.32322 10.3839 5.32322 9.90901 5.61612 9.61612C5.90901 9.32322 6.38388 9.32322 6.67678 9.61612L9.25 12.1893V3.875C9.25 3.46079 9.58579 3.125 10 3.125ZM4.5 11C4.91421 11 5.25 11.3358 5.25 11.75V15C5.25 15.6904 5.80964 16.25 6.5 16.25H13.5C14.1904 16.25 14.75 15.6904 14.75 15V11.75C14.75 11.3358 15.0858 11 15.5 11C15.9142 11 16.25 11.3358 16.25 11.75V15C16.25 16.5188 15.0188 17.75 13.5 17.75H6.5C4.98122 17.75 3.75 16.5188 3.75 15V11.75C3.75 11.3358 4.08579 11 4.5 11Z" fill="currentColor"/></svg>
                        Backup Database Terbaru
                    </a>
                </div>
            </div>
        </div>

        <form method="post" action="{{ route('pengaturan.backup.doRestore') }}" onsubmit="return validateRestore(event)">
            @csrf

            <div class="mb-4">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Pilih File Backup</label>
                <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                    <select name="backup_file" id="backupFile" required
                        class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                        x-bind:class="isOptionSelected && 'text-gray-800 dark:text-white/90'"
                        @change="isOptionSelected = true">
                        <option value="">-- Pilih file --</option>
                        @if(!empty($backups))
                            @foreach($backups as $b)
                                <option value="{{ $b['filename'] }}">
                                    {{ $b['filename'] }} ({{ number_format($b['size'] / 1024, 1) }} KB - {{ date('d/m/Y H:i', $b['created']) }})
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                        <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                </div>
            </div>

            <div class="mb-4">
                <label class="flex items-center text-sm text-gray-700 dark:text-gray-400 cursor-pointer select-none">
                    <div x-data="{ checkboxToggle: false }" class="relative">
                        <input type="checkbox" id="confirmBackup" class="sr-only" @change="checkboxToggle = !checkboxToggle; toggleRestore()">
                        <div x-bind:class="checkboxToggle ? 'border-brand-500 bg-brand-500' : 'bg-transparent border-gray-300 dark:border-gray-700'" class="mr-3 flex h-5 w-5 items-center justify-center rounded-md border-[1.25px]">
                            <span x-bind:class="checkboxToggle ? '' : 'opacity-0'">
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11.6666 3.5L5.24992 9.91667L2.33325 7" stroke="white" stroke-width="1.94437" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </div>
                    </div>
                    <span class="font-medium text-warning-500">Saya sudah melakukan backup terbaru</span>
                </label>
            </div>

            <div class="mb-4">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Konfirmasi</label>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Ketik <strong>RESTORE</strong> untuk melanjutkan</p>
                <input type="text" id="confirmText" name="confirmation_text" placeholder="RESTORE" oninput="toggleRestore()" autocomplete="off"
                    class="h-11 w-full rounded-lg border dark:bg-dark-900 border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
            </div>

            <button type="submit" id="btnRestore" disabled
                class="inline-flex items-center gap-2 rounded-lg bg-error-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-error-600 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8.75 3.75L5 7.5M5 7.5L8.75 11.25M5 7.5H15V5.75M11.25 16.25L15 12.5M15 12.5L11.25 8.75M15 12.5H5V14.25" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Restore Database
            </button>
        </form>
    </div>
</div>

<script>
function toggleRestore() {
    const checkboxEl = document.getElementById('confirmBackup');
    const checked = checkboxEl.checked;
    const text = document.getElementById('confirmText').value;
    document.getElementById('btnRestore').disabled = !(checked && text.toUpperCase() === 'RESTORE');
}

function validateRestore(e) {
    const file = document.getElementById('backupFile').value;
    if (!file) {
        showSystemMessage('warning', 'Pilih file backup terlebih dahulu.');
        e.preventDefault();
        return false;
    }
    e.preventDefault();

    const modal = document.getElementById('globalConfirmModal');
    const title = document.getElementById('globalConfirmTitle');
    const message = document.getElementById('globalConfirmMessage');
    const ok = document.getElementById('globalConfirmOk');
    const cancel = document.getElementById('globalConfirmCancel');

    title.textContent = 'Konfirmasi Restore Database';
    message.textContent = 'Restore database akan menimpa seluruh data saat ini. Yakin ingin melanjutkan?';
    ok.textContent = 'Ya, Restore';
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    const form = e.target;
    const handler = function () {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        ok.removeEventListener('click', handler);
        const btn = document.getElementById('btnRestore');
        setButtonLoading(btn, 'Restore...');
        showLoadingOverlay('Restore database sedang diproses...');
        form.submit();
    };
    ok.addEventListener('click', handler);
    cancel.addEventListener('click', function cancelHandler() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        cancel.removeEventListener('click', cancelHandler);
        ok.removeEventListener('click', handler);
    });
    return false;
}
</script>
@endsection
