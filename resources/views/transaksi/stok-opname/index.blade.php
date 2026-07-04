@extends('layouts.app')

@section('content')

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="flex flex-col gap-4 px-5 py-4 sm:flex-row sm:items-center sm:justify-between border-b border-gray-200 dark:border-gray-800">
        <form method="get" class="flex flex-wrap items-center gap-2">
            <select name="bulan"
                    class="dark:bg-dark-900 h-11 rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                <option value="">Semua Bulan</option>
                @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $m => $nama)
                <option value="{{ str_pad($m+1, 2, '0', STR_PAD_LEFT) }}" {{ ($bulan ?? '') === str_pad($m+1, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>{{ $nama }}</option>
                @endforeach
            </select>
            <select name="tahun"
                    class="dark:bg-dark-900 h-11 rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                <option value="">Semua Tahun</option>
                @for($t = date('Y'); $t >= date('Y')-2; $t--)
                <option value="{{ $t }}" {{ ($tahun ?? '') == $t ? 'selected' : '' }}>{{ $t }}</option>
                @endfor
            </select>
            <select name="status"
                    class="dark:bg-dark-900 h-11 rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                <option value="">Semua Status</option>
                <option value="draft" {{ ($status ?? '') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="selesai" {{ ($status ?? '') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="diterapkan" {{ ($status ?? '') === 'diterapkan' ? 'selected' : '' }}>Diterapkan</option>
                <option value="dibatalkan" {{ ($status ?? '') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                Filter
            </button>
        </form>
        <div class="flex items-center gap-2">
            <x-excel-dropdown
                template-route="{{ route('transaksi.stokopname.template') }}"
                :export-route="null"
                :import-route="'#'"
            />
            <a href="{{ route('transaksi.stokopname.create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 3.75V16.25M16.25 10H3.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                Buat Opname
            </a>
        </div>
    </div>

    <!-- Modal Import -->
    <div x-data="{ open: false }" @open-import.window="open = true" x-show="open"
         class="fixed inset-0 z-99999 flex items-center justify-center bg-gray-900/50 p-4"
         x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div @click.outside="open = false" class="w-full max-w-md rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Impor Stok Opname Excel</h3>
                <button @click="open = false" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M5.641 4.887C5.934 4.594 6.409 4.594 6.702 4.887L10 8.185L13.298 4.887C13.591 4.594 14.066 4.594 14.359 4.887C14.652 5.18 14.652 5.655 14.359 5.948L11.061 9.246L14.359 12.544C14.652 12.837 14.652 13.312 14.359 13.605C14.066 13.898 13.591 13.898 13.298 13.605L10 10.307L6.702 13.605C6.409 13.898 5.934 13.898 5.641 13.605C5.348 13.312 5.348 12.837 5.641 12.544L8.939 9.246L5.641 5.948C5.348 5.655 5.348 5.18 5.641 4.887Z" fill="currentColor"/>
                    </svg>
                </button>
            </div>
            <form method="post" action="{{ route('transaksi.stokopname.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tanggal Opname</label>
                    <div class="relative">
                        <input type="text" name="tanggal_opname" value="{{ date('Y-m-d') }}"
                               class="datepicker h-11 w-full rounded-lg border dark:bg-dark-900 border-gray-300 bg-transparent py-2.5 pr-11 pl-4 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" required>
                        <span class="pointer-events-none absolute top-1/2 right-3 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                            <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M6.66659 1.5415C7.0808 1.5415 7.41658 1.87729 7.41658 2.2915V2.99984H12.5833V2.2915C12.5833 1.87729 12.919 1.5415 13.3333 1.5415C13.7475 1.5415 14.0833 1.87729 14.0833 2.2915V2.99984L15.4166 2.99984C16.5212 2.99984 17.4166 3.89527 17.4166 4.99984V7.49984V15.8332C17.4166 16.9377 16.5212 17.8332 15.4166 17.8332H4.58325C3.47868 17.8332 2.58325 16.9377 2.58325 15.8332V7.49984V4.99984C2.58325 3.89527 3.47868 2.99984 4.58325 2.99984L5.91659 2.99984V2.2915C5.91659 1.87729 6.25237 1.5415 6.66659 1.5415ZM6.66659 4.49984H4.58325C4.30711 4.49984 4.08325 4.7237 4.08325 4.99984V6.74984H15.9166V4.99984C15.9166 4.7237 15.6927 4.49984 15.4166 4.49984H13.3333H6.66659ZM15.9166 8.24984H4.08325V15.8332C4.08325 16.1093 4.30711 16.3332 4.58325 16.3332H15.4166C15.6927 16.3332 15.9166 16.1093 15.9166 15.8332V8.24984Z" fill=""/>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Catatan (opsional)</label>
                    <input type="text" name="catatan"
                           class="h-11 w-full rounded-lg border dark:bg-dark-900 border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                </div>
                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">File Excel</label>
                    <div class="relative">
                        <input type="file" name="file_excel" id="fileExcelOpname" accept=".xls,.xlsx" required class="hidden" onchange="document.getElementById('fileLabelOpname').textContent = this.files[0]?.name || 'Pilih file'">
                        <label for="fileExcelOpname" class="flex h-11 w-full cursor-pointer items-center gap-3 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-500 shadow-theme-xs transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800">
                            <span class="inline-flex items-center gap-2 rounded-md bg-brand-50 px-3 py-1.5 text-xs font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">Pilih File</span>
                            <span id="fileLabelOpname" class="truncate">Belum ada file dipilih</span>
                        </label>
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Format: .xls atau .xlsx, maksimal 5MB</p>
                </div>
                <div class="flex items-center justify-end gap-3">
                    <button type="button" @click="open = false"
                            class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
                        Batal
                    </button>
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                        Impor
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="overflow-x-auto"><table class="table-sticky min-w-full">
            <thead>
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">NO</th>
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">NO OPNAME</th>
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">TANGGAL</th>
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">STATUS</th>
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">PETUGAS</th>
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">AKSI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($data as $i => $d)
                <tr>
                    <td class="px-5 py-4 text-sm text-center text-gray-800 dark:text-white/90">{{ $data->firstItem() + $i }}</td>
                    <td class="px-5 py-4 text-center">
                        <span class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-1 text-xs font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">
                            {{ esc($d->no_opname) }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-sm text-center text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($d->tanggal_opname)->format('d/m/Y') }}</td>
                    <td class="px-5 py-4 text-center">
                        @if($d->status === 'draft')
                            <span class="inline-flex items-center rounded-full bg-warning-50 px-2.5 py-1 text-xs font-medium text-warning-600 dark:bg-warning-500/15 dark:text-warning-400">Draft</span>
                        @elseif($d->status === 'selesai')
                            <span class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-1 text-xs font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">Selesai</span>
                        @elseif($d->status === 'diterapkan')
                            <span class="inline-flex items-center rounded-full bg-success-50 px-2.5 py-1 text-xs font-medium text-success-600 dark:bg-success-500/15 dark:text-success-400">Diterapkan</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-error-50 px-2.5 py-1 text-xs font-medium text-error-600 dark:bg-error-500/15 dark:text-error-400">Dibatalkan</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-sm text-center text-gray-500 dark:text-gray-400">{{ esc($d->nama_user) }}</td>
                    <td class="px-5 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            @if($d->status === 'draft')
                            <a href="{{ route('transaksi.stokopname.edit', $d->id) }}"
                               class="inline-flex items-center justify-center gap-2 rounded-lg bg-warning-50 p-2 text-warning-600 hover:bg-warning-100 dark:bg-warning-500/15 dark:text-warning-400"
                               title="Edit">
                                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17.1667 3.5C16.7917 3.125 16.2917 2.91667 15.7917 2.91667C15.2917 2.91667 14.7917 3.125 14.4167 3.5L4.5 13.4167L3.83333 16.8333L7.25 16.1667L17.1667 6.25C17.5417 5.875 17.75 5.375 17.75 4.875C17.75 4.375 17.5417 3.875 17.1667 3.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </a>
                            @endif
                            <a href="{{ route('transaksi.stokopname.detail', $d->id) }}"
                               class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-50 p-2 text-brand-600 hover:bg-brand-100 dark:bg-brand-500/15 dark:text-brand-400"
                               title="Lihat Detail">
                                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10 3.75C5.83333 3.75 2.5 7.5 2.5 10C2.5 12.5 5.83333 16.25 10 16.25C14.1667 16.25 17.5 12.5 17.5 10C17.5 7.5 14.1667 3.75 10 3.75ZM10 14.1667C7.75 14.1667 5.83333 12.25 5.83333 10C5.83333 7.75 7.75 5.83333 10 5.83333C12.25 5.83333 14.1667 7.75 14.1667 10C14.1667 12.25 12.25 14.1667 10 14.1667ZM10 7.5C8.61667 7.5 7.5 8.61667 7.5 10C7.5 11.3833 8.61667 12.5 10 12.5C11.3833 12.5 12.5 11.3833 12.5 10C12.5 8.61667 11.3833 7.5 10 7.5Z" fill="currentColor"/>
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center">
                        <svg class="mx-auto mb-4 h-16 w-16 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada data stok opname</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($data->hasPages())
    <div class="border-t border-gray-200 dark:border-gray-800">
        {{ $data->links('vendor.pagination.tailadmin') }}
    </div>
    @endif
</div>

@endsection
