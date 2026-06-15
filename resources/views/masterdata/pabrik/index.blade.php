@extends('layouts.app')

@section('content')

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="flex flex-col gap-4 px-5 py-4 sm:flex-row sm:items-center sm:justify-between border-b border-gray-200 dark:border-gray-800">
        <x-search-bar route="{{ route('masterdata.pabrik.index') }}" query="{{ $q ?? '' }}" placeholder="Cari pabrik..." />
        <div class="flex items-center gap-2">
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

    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead>
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">ID</th>
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">KODE</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">NAMA PABRIK</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">ALAMAT</th>
                    @can('admin')
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">AKSI</th>
                    @endcan
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($pabrik as $p)
                <tr>
                    <td class="px-5 py-4 text-sm text-center text-gray-800 dark:text-white/90">{{ $p->id }}</td>
                    <td class="px-5 py-4 text-center">
                        <span class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-1 text-xs font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">
                            {{ esc($p->kode_pabrik) }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-sm text-left text-gray-800 dark:text-white/90">{{ esc($p->nama_pabrik) }}</td>
                    <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">{{ esc($p->alamat) }}</td>
                    @can('admin')
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('masterdata.pabrik.edit', $p->id) }}" class="inline-flex items-center justify-center rounded-lg bg-warning-50 p-2 text-warning-600 hover:bg-warning-100 dark:bg-warning-500/15 dark:text-warning-400">
                                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M13.75 2.125C14.1642 2.125 14.5 2.46079 14.5 2.875V5.0568L17.0683 5.0568C17.4825 5.0568 17.8183 5.39259 17.8183 5.8068C17.8183 6.22102 17.4825 6.5568 17.0683 6.5568L15.4379 6.5568L14.5 6.5568L14.5 14.375C14.5 16.5841 12.7091 18.375 10.5 18.375H5.5C3.29086 18.375 1.5 16.5841 1.5 14.375V6.25C1.5 4.04086 3.29086 2.25 5.5 2.25H13C13.4142 2.25 13.75 2.58579 13.75 3V2.125ZM13 3.75H5.5C4.11929 3.75 3 4.86929 3 6.25V14.375C3 15.7557 4.11929 16.875 5.5 16.875H10.5C11.8807 16.875 13 15.7557 13 14.375V6.5568V5.8068H13V4.375V3.75ZM8 8.75C8 8.33579 8.33579 8 8.75 8C9.16421 8 9.5 8.33579 9.5 8.75V10.75H11.5C11.9142 10.75 12.25 11.0858 12.25 11.5C12.25 11.9142 11.9142 12.25 11.5 12.25H9.5V14.25C9.5 14.6642 9.16421 15 8.75 15C8.33579 15 8 14.6642 8 14.25V12.25H6C5.58579 12.25 5.25 11.9142 5.25 11.5C5.25 11.0858 5.58579 10.75 6 10.75H8V8.75Z" fill="currentColor"/>
                                </svg>
                            </a>
                            <form method="post" action="{{ route('masterdata.pabrik.delete', $p->id) }}" class="inline" data-confirm-message="Yakin ingin menghapus data pabrik ini?" data-confirm-ok="Ya, Hapus">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-error-50 p-2 text-error-600 hover:bg-error-100 dark:bg-error-500/15 dark:text-error-400">
                                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M7.5 2.5C7.5 2.08579 7.83579 1.75 8.25 1.75H11.75C12.1642 1.75 12.5 2.08579 12.5 2.5V3.5H6.25V2.5H7.5ZM4.75 4.5V3.5C4.75 2.25736 5.75736 1.25 7 1.25H13C14.2426 1.25 15.25 2.25736 15.25 3.5V4.5H16.5C17.1904 4.5 17.75 5.05964 17.75 5.75C17.75 6.44036 17.1904 7 16.5 7H16.1273L15.2865 16.2885C15.1399 17.8235 13.8455 19 12.3043 19H7.6957C6.15448 19 4.86011 17.8235 4.71346 16.2885L3.87273 7H3.5C2.80964 7 2.25 6.44036 2.25 5.75C2.25 5.05964 2.80964 4.5 3.5 4.5H4.75ZM6.21817 7L7.04185 16.1443C7.07708 16.5095 7.39145 16.75 7.6957 16.75H12.3043C12.6086 16.75 12.9229 16.5095 12.9582 16.1443L13.7818 7H6.21817Z" fill="currentColor"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                    @endcan
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-12 text-center">
                        <svg class="mx-auto mb-4 h-16 w-16 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada data pabrik</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pabrik->hasPages())
    <div class="border-t border-gray-200 dark:border-gray-800">
        {{ $pabrik->links('vendor.pagination.tailadmin') }}
    </div>
    @endif
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
