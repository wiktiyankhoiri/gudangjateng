@extends('layouts.app')

@section('content')

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="flex flex-col gap-4 px-5 py-4 sm:flex-row sm:items-center sm:justify-between border-b border-gray-200 dark:border-gray-800"
         x-data="{ cari: '{{ $cari ?? '' }}', loading: false }"
         x-init="$watch('cari', (value, oldValue) => {
             if (value === oldValue) return;
             clearTimeout(window._searchTimer);
             window._searchTimer = setTimeout(() => {
                 loading = true;
                 fetch('{{ route('masterdata.pabrik.index') }}?cari=' + encodeURIComponent(value), {
                     headers: { 'X-Requested-With': 'XMLHttpRequest' }
                 })
                 .then(r => r.json())
                 .then(data => {
                     document.getElementById('table-wrap').innerHTML = data.html;
                     loading = false;
                     const url = new URL(window.location);
                    if (value) url.searchParams.set('cari', value);
                    else url.searchParams.delete('cari');
                     window.history.replaceState({}, '', url);
                 })
                 .catch(() => { loading = false; });
             }, 400);
         })">

        <div class="relative">
            <span class="absolute top-1/2 left-0 -translate-y-1/2 border-r border-gray-200 px-3.5 py-3 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M3.04175 9.37363C3.04175 5.87693 5.87711 3.04199 9.37508 3.04199C12.8731 3.04199 15.7084 5.87693 15.7084 9.37363C15.7084 12.8703 12.8731 15.7053 9.37508 15.7053C5.87711 15.7053 3.04175 12.8703 3.04175 9.37363ZM9.37508 1.54199C5.04902 1.54199 1.54175 5.04817 1.54175 9.37363C1.54175 13.6991 5.04902 17.2053 9.37508 17.2053C11.2674 17.2053 13.003 16.5344 14.357 15.4176L17.177 18.238C17.4699 18.5309 17.9448 18.5309 18.2377 18.238C18.5306 17.9451 18.5306 17.4703 18.2377 17.1774L15.418 14.3573C16.5365 13.0033 17.2084 11.2669 17.2084 9.37363C17.2084 5.04817 13.7011 1.54199 9.37508 1.54199Z" fill="currentColor"/>
                </svg>
            </span>
            <input type="text" name="cari" x-model="cari"
                   class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full sm:w-[280px] rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pl-[62px] text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                   placeholder="Cari pabrik...">
            <div x-show="loading" class="absolute right-3 top-1/2 -translate-y-1/2">
                <svg class="h-5 w-5 animate-spin text-brand-500" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if(!empty($cari))
            <a href="{{ route('masterdata.pabrik.index') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs ring-1 ring-inset ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.641 4.887C5.934 4.594 6.409 4.594 6.702 4.887L10 8.185L13.298 4.887C13.591 4.594 14.066 4.594 14.359 4.887C14.652 5.18 14.652 5.655 14.359 5.948L11.061 9.246L14.359 12.544C14.652 12.837 14.652 13.312 14.359 13.605C14.066 13.898 13.591 13.898 13.298 13.605L10 10.307L6.702 13.605C6.409 13.898 5.934 13.898 5.641 13.605C5.348 13.312 5.348 12.837 5.641 12.544L8.939 9.246L5.641 5.948C5.348 5.655 5.348 5.18 5.641 4.887Z" fill="currentColor"/></svg>
            </a>
            @endif
            <x-excel-dropdown
                template-route="{{ route('masterdata.pabrik.template') }}"
                export-route="{{ route('masterdata.pabrik.export') }}"
                import-route="#"
            />
            <a href="{{ route('masterdata.pabrik.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 3.75V16.25M16.25 10H3.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                Tambah
            </a>
        </div>
    </div>

    <div id="table-wrap">
        @include('masterdata.pabrik._table')
    </div>
</div>

<!-- Modal Import -->
<div x-data="{ open: false }" @open-import.window="open = true" x-show="open" class="fixed inset-0 z-99999 flex items-center justify-center bg-gray-900/50 p-4" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
    <div @click.outside="open = false" class="w-full max-w-md rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Impor Pabrik Excel</h3>
            <button @click="open = false" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M5.641 4.887C5.934 4.594 6.409 4.594 6.702 4.887L10 8.185L13.298 4.887C13.591 4.594 14.066 4.594 14.359 4.887C14.652 5.18 14.652 5.655 14.359 5.948L11.061 9.246L14.359 12.544C14.652 12.837 14.652 13.312 14.359 13.605C14.066 13.898 13.591 13.898 13.298 13.605L10 10.307L6.702 13.605C6.409 13.898 5.934 13.898 5.641 13.605C5.348 13.312 5.348 12.837 5.641 12.544L8.939 9.246L5.641 5.948C5.348 5.655 5.348 5.18 5.641 4.887Z" fill="currentColor"/>
                </svg>
            </button>
        </div>
        <form action="{{ route('masterdata.pabrik.import') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="mb-4 rounded-lg bg-brand-50 p-4 dark:bg-brand-500/15">
                <p class="text-sm font-medium text-brand-600 dark:text-brand-400 mb-2">Format Excel:</p>
                <ul class="mb-0 text-xs text-gray-500 dark:text-gray-400 ms-4 list-disc">
                    <li>kode_pabrik</li>
                    <li>nama_pabrik</li>
                    <li>alamat</li>
                </ul>
                <p class="text-xs text-brand-600 dark:text-brand-400 mt-2 font-medium">💡 Jika kode_pabrik sudah ada, data akan diupdate.</p>
            </div>
            <div class="mb-4">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">File Excel</label>
                <div class="relative">
                    <input type="file" name="file_excel" id="fileExcelPabrik" accept=".xls,.xlsx" required class="hidden" onchange="document.getElementById('fileLabelPabrik').textContent = this.files[0]?.name || 'Pilih file'">
                    <label for="fileExcelPabrik" class="flex h-11 w-full cursor-pointer items-center gap-3 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-500 shadow-theme-xs transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800">
                        <span class="inline-flex items-center gap-2 rounded-md bg-brand-50 px-3 py-1.5 text-xs font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">Pilih File</span>
                        <span id="fileLabelPabrik" class="truncate">Belum ada file dipilih</span>
                    </label>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3">
                <button type="button" @click="open = false" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-800 dark:text-gray-400 dark:hover:bg-gray-800">Tutup</button>
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">Impor</button>
            </div>
        </form>
    </div>
</div>
@endsection
