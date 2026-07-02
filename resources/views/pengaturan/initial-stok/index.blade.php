@extends('layouts.app')

@section('content')

<?php
$no = 1 + (50 * ((request('page_initialstok') ?? 1) - 1));
?>

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="flex flex-col gap-4 px-5 py-4 sm:flex-row sm:items-center sm:justify-between border-b border-gray-200 dark:border-gray-800">
        <form method="get" class="flex items-center gap-2">
            <div class="relative">
                <span class="absolute top-1/2 left-0 -translate-y-1/2 border-r border-gray-200 px-3.5 py-3 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M3.04175 9.37363C3.04175 5.87693 5.87711 3.04199 9.37508 3.04199C12.8731 3.04199 15.7084 5.87693 15.7084 9.37363C15.7084 12.8703 12.8731 15.7053 9.37508 15.7053C5.87711 15.7053 3.04175 12.8703 3.04175 9.37363ZM9.37508 1.54199C5.04902 1.54199 1.54175 5.04817 1.54175 9.37363C1.54175 13.6991 5.04902 17.2053 9.37508 17.2053C11.2674 17.2053 13.003 16.5344 14.357 15.4176L17.177 18.238C17.4699 18.5309 17.9448 18.5309 18.2377 18.238C18.5306 17.9451 18.5306 17.4703 18.2377 17.1774L15.418 14.3573C16.5365 13.0033 17.2084 11.2669 17.2084 9.37363C17.2084 5.04817 13.7011 1.54199 9.37508 1.54199Z" fill="currentColor"/>
                    </svg>
                </span>
                <input type="text" name="cari" value="{{ $cari ?? '' }}" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full sm:w-[280px] rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pl-[62px] text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" placeholder="Cari barang...">
            </div>
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">Cari</button>
        </form>
        <div class="flex items-center gap-2">
            <div x-data="{ dropdownOpen: false }" class="relative">
                <button
                    @click="dropdownOpen = !dropdownOpen"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs ring-1 ring-inset ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]"
                >
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M2.5 5C2.5 4.58579 2.83579 4.25 3.25 4.25H16.75C17.1642 4.25 17.5 4.58579 17.5 5C17.5 5.41421 17.1642 5.75 16.75 5.75H3.25C2.83579 5.75 2.5 5.41421 2.5 5ZM4.75 10C4.75 9.58579 5.08579 9.25 5.5 9.25H14.5C14.9142 9.25 15.25 9.58579 15.25 10C15.25 10.4142 14.9142 10.75 14.5 10.75H5.5C5.08579 10.75 4.75 10.4142 4.75 10ZM7.5 14.75C7.08579 14.75 6.75 15.0858 6.75 15.5C6.75 15.9142 7.08579 16.25 7.5 16.25H12.5C12.9142 16.25 13.25 15.9142 13.25 15.5C13.25 15.0858 12.9142 14.75 12.5 14.75H7.5Z" fill="currentColor"/>
                    </svg>
                    Excel
                    <svg x-bind:class="dropdownOpen && 'rotate-180'" width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <div
                    x-show="dropdownOpen"
                    @click.outside="dropdownOpen = false"
                    class="absolute right-0 z-50 mt-1 w-56 rounded-lg border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-800 dark:bg-gray-900"
                >
                    <a href="{{ route('pengaturan.initialstok.template') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800">
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 3.75V16.25M16.25 10H3.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                        Template Impor
                    </a>
                    <a href="{{ route('pengaturan.initialstok.export') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800">
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 3.125C10.4142 3.125 10.75 3.46079 10.75 3.875V12.4822L13.6161 9.61612C13.909 9.32322 14.3839 9.32322 14.6768 9.61612C14.9697 9.90901 14.9697 10.3839 14.6768 10.6768L10.6768 14.6768C10.3839 14.9697 9.90901 14.9697 9.61612 14.6768L5.61612 10.6768C5.32322 10.3839 5.32322 9.90901 5.61612 9.61612C5.90901 9.32322 6.38388 9.32322 6.67678 9.61612L9.25 12.1893V3.875C9.25 3.46079 9.58579 3.125 10 3.125ZM4.5 11C4.91421 11 5.25 11.3358 5.25 11.75V15C5.25 15.6904 5.80964 16.25 6.5 16.25H13.5C14.1904 16.25 14.75 15.6904 14.75 15V11.75C14.75 11.3358 15.0858 11 15.5 11C15.9142 11 16.25 11.3358 16.25 11.75V15C16.25 16.5188 15.0188 17.75 13.5 17.75H6.5C4.98122 17.75 3.75 16.5188 3.75 15V11.75C3.75 11.3358 4.08579 11 4.5 11Z" fill="currentColor"/></svg>
                        Ekspor Data
                    </a>
                    <hr class="my-1 border-gray-200 dark:border-gray-800">
                    <a href="#" @click.prevent="$dispatch('open-import')" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800">
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 16.875C9.58579 16.875 9.25 16.5392 9.25 16.125V7.51777L6.38388 10.3839C6.09099 10.6768 5.61612 10.6768 5.32322 10.3839C5.03033 10.091 5.03033 9.61612 5.32322 9.32322L9.32322 5.32322C9.61612 5.03033 10.091 5.03033 10.3839 5.32322L14.3839 9.32322C14.6768 9.61612 14.6768 10.091 14.3839 10.3839C14.091 10.6768 13.6161 10.6768 13.3232 10.3839L10.75 7.81066V16.125C10.75 16.5392 10.4142 16.875 10 16.875ZM4.5 9C4.91421 9 5.25 8.66421 5.25 8.25V5C5.25 4.30964 5.80964 3.75 6.5 3.75H13.5C14.1904 3.75 14.75 4.30964 14.75 5V8.25C14.75 8.66421 15.0858 9 15.5 9C15.9142 9 16.25 8.66421 16.25 8.25V5C16.25 3.48122 15.0188 2.25 13.5 2.25H6.5C4.98122 2.25 3.75 3.48122 3.75 5V8.25C3.75 8.66421 4.08579 9 4.5 9Z" fill="currentColor"/></svg>
                        Impor Data
                    </a>
                </div>
            </div>
            <a href="{{ route('pengaturan.initialstok.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 3.75V16.25M16.25 10H3.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                Tambah
            </a>
        </div>
    </div>

    <div class="overflow-x-auto"><table class="table-sticky min-w-full">
            <thead>
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">NO</th>
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">KODE</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">NAMA BARANG</th>
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">JML BAIK</th>
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">JML RUSAK</th>
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">KETERANGAN</th>
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">AKSI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($data as $i => $d)
                <tr>
                    <td class="px-5 py-4 text-sm text-center text-gray-800 dark:text-white/90">{{ $no++ }}</td>
                    <td class="px-5 py-4 text-center">
                        <span class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-1 text-xs font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">
                            {{ esc($d->kode_barang) }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-sm text-left text-gray-800 dark:text-white/90">{{ esc($d->nama_barang) }}</td>
                    <td class="px-5 py-4 text-sm text-center font-medium text-success-600 dark:text-success-400">{{ number_format($d->qty_baik, 0, ',', '.') }}</td>
                    <td class="px-5 py-4 text-sm text-center font-medium text-error-600 dark:text-error-400">{{ number_format($d->qty_rusak, 0, ',', '.') }}</td>
                    <td class="px-5 py-4 text-sm text-center text-gray-500 dark:text-gray-400">{{ esc($d->keterangan) }}</td>
                    <td class="px-5 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('pengaturan.initialstok.edit', $d->id) }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-warning-50 p-2 text-warning-600 hover:bg-warning-100 dark:bg-warning-500/15 dark:text-warning-400">
                                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M13.75 2.125C14.1642 2.125 14.5 2.46079 14.5 2.875V5.0568L17.0683 5.0568C17.4825 5.0568 17.8183 5.39259 17.8183 5.8068C17.8183 6.22102 17.4825 6.5568 17.0683 6.5568L15.4379 6.5568L14.5 6.5568L14.5 14.375C14.5 16.5841 12.7091 18.375 10.5 18.375H5.5C3.29086 18.375 1.5 16.5841 1.5 14.375V6.25C1.5 4.04086 3.29086 2.25 5.5 2.25H13C13.4142 2.25 13.75 2.58579 13.75 3V2.125ZM13 3.75H5.5C4.11929 3.75 3 4.86929 3 6.25V14.375C3 15.7557 4.11929 16.875 5.5 16.875H10.5C11.8807 16.875 13 15.7557 13 14.375V6.5568V5.8068H13V4.375V3.75ZM8 8.75C8 8.33579 8.33579 8 8.75 8C9.16421 8 9.5 8.33579 9.5 8.75V10.75H11.5C11.9142 10.75 12.25 11.0858 12.25 11.5C12.25 11.9142 11.9142 12.25 11.5 12.25H9.5V14.25C9.5 14.6642 9.16421 15 8.75 15C8.33579 15 8 14.6642 8 14.25V12.25H6C5.58579 12.25 5.25 11.9142 5.25 11.5C5.25 11.0858 5.58579 10.75 6 10.75H8V8.75Z" fill="currentColor"/>
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center">
                        <svg class="mx-auto mb-4 h-16 w-16 text-gray-300 dark:text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada data stok awal</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($data->hasPages())
    <div class="border-t border-gray-200 dark:border-gray-800">
        {{ $data->appends(['cari' => request('cari')])->links('vendor.pagination.tailadmin', ['paginatorName' => 'initialstok']) }}
    </div>
    @endif
</div>

<!-- Modal Import -->
<div x-data="{ open: false }" @open-import.window="open = true" x-show="open" class="fixed inset-0 z-99999 flex items-center justify-center bg-gray-900/50 p-4" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
    <div @click.outside="open = false" class="w-full max-w-md rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Impor Stok Awal</h3>
            <button @click="open = false" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M5.641 4.887C5.934 4.594 6.409 4.594 6.702 4.887L10 8.185L13.298 4.887C13.591 4.594 14.066 4.594 14.359 4.887C14.652 5.18 14.652 5.655 14.359 5.948L11.061 9.246L14.359 12.544C14.652 12.837 14.652 13.312 14.359 13.605C14.066 13.898 13.591 13.898 13.298 13.605L10 10.307L6.702 13.605C6.409 13.898 5.934 13.898 5.641 13.605C5.348 13.312 5.348 12.837 5.641 12.544L8.939 9.246L5.641 5.948C5.348 5.655 5.348 5.18 5.641 4.887Z" fill="currentColor"/>
                </svg>
            </button>
        </div>
        <form action="{{ route('pengaturan.initialstok.import') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="mb-4 rounded-lg bg-brand-50 p-4 dark:bg-brand-500/15">
                <p class="text-sm font-medium text-brand-600 dark:text-brand-400 mb-2">Format Excel:</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">kode_barang, jml_baik, jml_rusak, keterangan</p>
            </div>
            <div class="mb-4">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">File Excel</label>
                <div class="relative">
                    <input type="file" name="file_excel" id="fileExcelInitial" accept=".xls,.xlsx" required class="hidden" onchange="document.getElementById('fileLabelInitial').textContent = this.files[0]?.name || 'Pilih file'">
                    <label for="fileExcelInitial" class="flex h-11 w-full cursor-pointer items-center gap-3 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-500 shadow-theme-xs transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800">
                        <span class="inline-flex items-center gap-2 rounded-md bg-brand-50 px-3 py-1.5 text-xs font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">Pilih File</span>
                        <span id="fileLabelInitial" class="truncate">Belum ada file dipilih</span>
                    </label>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3">
                <button type="button" @click="open = false" class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-800 dark:text-gray-400 dark:hover:bg-gray-800">Tutup</button>
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">Impor</button>
            </div>
        </form>
    </div>
</div>
@endsection
