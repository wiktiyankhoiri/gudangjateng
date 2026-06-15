@extends('layouts.app')

@section('content')

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

    <!-- FILTER -->
    <div class="border-b border-gray-200 p-5 dark:border-gray-800">
        <form method="get" action="{{ route('laporan.salesstok.index') }}">
            <div class="flex flex-col gap-4 sm:flex-row">

                <div class="filter-item" style="flex:1 1 160px; min-width:120px;">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Dari Tanggal</label>
                    <div class="relative">
                        <input type="text" name="tanggal_awal"
                            class="datepicker h-11 w-full appearance-none rounded-lg border dark:bg-dark-900 border-gray-300 bg-transparent bg-none py-2.5 pr-11 pl-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                            value="{{ $tanggalAwal ?? '' }}" placeholder="Pilih tanggal">
                        <span class="pointer-events-none absolute top-1/2 right-3 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                            <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M6.66659 1.5415C7.0808 1.5415 7.41658 1.87729 7.41658 2.2915V2.99984H12.5833V2.2915C12.5833 1.87729 12.919 1.5415 13.3333 1.5415C13.7475 1.5415 14.0833 1.87729 14.0833 2.2915V2.99984L15.4166 2.99984C16.5212 2.99984 17.4166 3.89527 17.4166 4.99984V7.49984V15.8332C17.4166 16.9377 16.5212 17.8332 15.4166 17.8332H4.58325C3.47868 17.8332 2.58325 16.9377 2.58325 15.8332V7.49984V4.99984C2.58325 3.89527 3.47868 2.99984 4.58325 2.99984L5.91659 2.99984V2.2915C5.91659 1.87729 6.25237 1.5415 6.66659 1.5415ZM6.66659 4.49984H4.58325C4.30711 4.49984 4.08325 4.7237 4.08325 4.99984V6.74984H15.9166V4.99984C15.9166 4.7237 15.6927 4.49984 15.4166 4.49984H13.3333H6.66659ZM15.9166 8.24984H4.08325V15.8332C4.08325 16.1093 4.30711 16.3332 4.58325 16.3332H15.4166C15.6927 16.3332 15.9166 16.1093 15.9166 15.8332V8.24984Z" fill=""/>
                            </svg>
                        </span>
                    </div>
                </div>

                <div class="filter-item" style="flex:1 1 160px; min-width:120px;">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Sampai Tanggal</label>
                    <div class="relative">
                        <input type="text" name="tanggal_akhir"
                            class="datepicker h-11 w-full appearance-none rounded-lg border dark:bg-dark-900 border-gray-300 bg-transparent bg-none py-2.5 pr-11 pl-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                            value="{{ $tanggalAkhir ?? '' }}" placeholder="Pilih tanggal">
                        <span class="pointer-events-none absolute top-1/2 right-3 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                            <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M6.66659 1.5415C7.0808 1.5415 7.41658 1.87729 7.41658 2.2915V2.99984H12.5833V2.2915C12.5833 1.87729 12.919 1.5415 13.3333 1.5415C13.7475 1.5415 14.0833 1.87729 14.0833 2.2915V2.99984L15.4166 2.99984C16.5212 2.99984 17.4166 3.89527 17.4166 4.99984V7.49984V15.8332C17.4166 16.9377 16.5212 17.8332 15.4166 17.8332H4.58325C3.47868 17.8332 2.58325 16.9377 2.58325 15.8332V7.49984V4.99984C2.58325 3.89527 3.47868 2.99984 4.58325 2.99984L5.91659 2.99984V2.2915C5.91659 1.87729 6.25237 1.5415 6.66659 1.5415ZM6.66659 4.49984H4.58325C4.30711 4.49984 4.08325 4.7237 4.08325 4.99984V6.74984H15.9166V4.99984C15.9166 4.7237 15.6927 4.49984 15.4166 4.49984H13.3333H6.66659ZM15.9166 8.24984H4.08325V15.8332C4.08325 16.1093 4.30711 16.3332 4.58325 16.3332H15.4166C15.6927 16.3332 15.9166 16.1093 15.9166 15.8332V8.24984Z" fill=""/>
                            </svg>
                        </span>
                    </div>
                </div>

                <div class="filter-btn-wrap flex flex-wrap gap-2" style="flex-shrink:0; padding-top:28px;">
                    <button type="submit"
                        class="h-11 inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 10H15M2.5 5H17.5M7.5 15H12.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                        Filter
                    </button>
                    <a href="{{ route('laporan.salesstok.index') }}"
                        class="h-11 inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs ring-1 ring-inset ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 2.5C5.85786 2.5 2.5 5.85786 2.5 10C2.5 10.4142 2.83579 10.75 3.25 10.75C3.66421 10.75 4 10.4142 4 10C4 6.68629 6.68629 4 10 4C13.3137 4 16 6.68629 16 10C16 13.3137 13.3137 16 10 16C9.58579 16 9.25 16.3358 9.25 16.75C9.25 17.1642 9.58579 17.5 10 17.5C14.1421 17.5 17.5 14.1421 17.5 10C17.5 5.85786 14.1421 2.5 10 2.5ZM10.5303 6.96967C10.2374 6.67678 9.76256 6.67678 9.46967 6.96967L6.96967 9.46967C6.67678 9.76256 6.67678 10.2374 6.96967 10.5303C7.26256 10.8232 7.73744 10.8232 8.03033 10.5303L9.25 9.31066V13.25C9.25 13.6642 9.58579 14 10 14C10.4142 14 10.75 13.6642 10.75 13.25V9.31066L11.9697 10.5303C12.2626 10.8232 12.7374 10.8232 13.0303 10.5303C13.3232 10.2374 13.3232 9.76256 13.0303 9.46967L10.5303 6.96967Z" fill="currentColor"/></svg>
                        Atur Ulang
                    </a>
                    <a href="{{ route('laporan.salesstok.export', request()->query()) }}"
                        class="h-11 inline-flex items-center justify-center gap-2 rounded-lg bg-success-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-success-600">
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 3.125C10.4142 3.125 10.75 3.46079 10.75 3.875V12.4822L13.6161 9.61612C13.909 9.32322 14.3839 9.32322 14.6768 9.61612C14.9697 9.90901 14.9697 10.3839 14.6768 10.6768L10.6768 14.6768C10.3839 14.9697 9.90901 14.9697 9.61612 14.6768L5.61612 10.6768C5.32322 10.3839 5.32322 9.90901 5.61612 9.61612C5.90901 9.32322 6.38388 9.32322 6.67678 9.61612L9.25 12.1893V3.875C9.25 3.46079 9.58579 3.125 10 3.125ZM4.5 11C4.91421 11 5.25 11.3358 5.25 11.75V15C5.25 15.6904 5.80964 16.25 6.5 16.25H13.5C14.1904 16.25 14.75 15.6904 14.75 15V11.75C14.75 11.3358 15.0858 11 15.5 11C15.9142 11 16.25 11.3358 16.25 11.75V15C16.25 16.5188 15.0188 17.75 13.5 17.75H6.5C4.98122 17.75 3.75 16.5188 3.75 15V11.75C3.75 11.3358 4.08579 11 4.5 11Z" fill="currentColor"/></svg>
                        Ekspor Excel
                    </a>
                </div>

            </div>
        </form>
    </div>

    <!-- TABLE -->
    <div class="p-5">
        <div class="overflow-x-auto"><table class="table-sticky min-w-full">

                <thead>
                    <tr>
                        <th rowspan="2" width="60" class="border-r border-gray-200 dark:border-gray-700 px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400 ">NO</th>
                        <th rowspan="2" width="140" class="border-r border-gray-200 dark:border-gray-700 px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400 ">KODE</th>
                        <th rowspan="2" width="300" class="border-r border-gray-200 dark:border-gray-700 px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400 ">NAMA BARANG</th>
                        <th rowspan="2" width="100" class="border-r border-gray-200 dark:border-gray-700 px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400 ">AWAL</th>
                        <th colspan="3" class="border-r border-gray-200 dark:border-gray-700 px-5 py-3 text-center text-xs font-semibold text-brand-500 dark:text-brand-400">BARANG MASUK</th>
                        <th colspan="1" class="border-r border-gray-200 dark:border-gray-700 px-5 py-3 text-center text-xs font-semibold text-error-700 dark:text-error-500">BARANG KELUAR</th>
                        <th colspan="2" class="border-r border-gray-200 dark:border-gray-700 px-5 py-3 text-center text-xs font-semibold text-success-700 dark:text-success-500">SISA STOK</th>
                        <th rowspan="2" width="100" class="border-l border-gray-200 dark:border-gray-700 px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400 ">TOTAL</th>
                    </tr>
                    <tr>
                        <th width="100" class="border-r border-gray-200 dark:border-gray-700 px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400 ">PABRIK</th>
                        <th width="100" class="border-r border-gray-200 dark:border-gray-700 px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400 ">RETUR</th>
                        <th width="100" class="border-r border-gray-200 dark:border-gray-700 px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400 ">TOTAL</th>
                        <th width="100" class="border-r border-gray-200 dark:border-gray-700 px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400 ">SALES</th>
                        <th width="100" class="border-r border-gray-200 dark:border-gray-700 px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400 ">BAIK</th>
                        <th width="100" class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400 ">RUSAK</th>
                    </tr>
                </thead>

                <tbody>
                    @if($data->isNotEmpty())
                        @php
                            $grandAwal = $grandTotals['awal'] ?? 0;
                            $grandPabrik = $grandTotals['pabrik'] ?? 0;
                            $grandRetur = $grandTotals['retur'] ?? 0;
                            $grandMasuk = $grandTotals['total_masuk'] ?? 0;
                            $grandSales = $grandTotals['sales'] ?? 0;
                            $grandBaik = $grandTotals['sisa_baik'] ?? 0;
                            $grandRusak = $grandTotals['sisa_rusak'] ?? 0;
                            $grandTotal = $grandTotals['total'] ?? 0;
                        @endphp

                        @foreach($data as $i => $d)
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="px-5 py-4 text-center text-sm text-gray-700 dark:text-gray-400">{{ ($data->currentPage() - 1) * $data->perPage() + $i + 1 }}</td>
                            <td class="px-5 py-4 text-center text-sm text-gray-700 dark:text-gray-400">
                                <span class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-1 text-xs font-medium text-brand-500 dark:bg-brand-500/15 dark:text-brand-400">{{ esc($d['kode_barang']) }}</span>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-400">
                                <span class="font-semibold">{{ esc($d['nama_barang']) }}</span>
                            </td>
                            <td class="px-5 py-4 text-center text-sm font-bold text-gray-700 dark:text-gray-400">{{ number_format($d['awal']) }}</td>
                            <td class="px-5 py-4 text-center text-sm">
                                <span class="text-brand-500 dark:text-brand-400 font-bold">{{ number_format($d['pabrik']) }}</span>
                            </td>
                            <td class="px-5 py-4 text-center text-sm">
                                <span class="text-warning-600 dark:text-warning-400 font-bold">{{ number_format($d['retur']) }}</span>
                            </td>
                            <td class="px-5 py-4 text-center text-sm">
                                <span class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-1 text-xs font-medium text-brand-500 dark:bg-brand-500/15 dark:text-brand-400 font-bold">{{ number_format($d['total_masuk']) }}</span>
                            </td>
                            <td class="px-5 py-4 text-center text-sm">
                                <span class="text-error-600 dark:text-error-500 font-bold">{{ number_format($d['sales']) }}</span>
                            </td>
                            <td class="px-5 py-4 text-center text-sm">
                                <span class="text-success-600 dark:text-success-500 font-bold">{{ number_format($d['sisa_baik']) }}</span>
                            </td>
                            <td class="px-5 py-4 text-center text-sm">
                                <span class="text-error-600 dark:text-error-500 font-bold">{{ number_format($d['sisa_rusak']) }}</span>
                            </td>
                            <td class="px-5 py-4 text-center text-sm">
                                <span class="inline-flex items-center rounded-full bg-success-50 px-2.5 py-1 text-xs font-medium text-success-600 dark:bg-success-500/15 dark:text-success-500 font-bold">{{ number_format($d['total']) }}</span>
                            </td>
                        </tr>
                        @endforeach

                    @else

                        <tr>
                            <td colspan="11" class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                Tidak ada data
                            </td>
                        </tr>

                    @endif
                </tbody>

                @if($data->isNotEmpty())

                <tfoot>
                    <tr class="bg-gray-50 dark:bg-gray-800 font-semibold">
                        <td colspan="3" class="px-5 py-3 text-end text-sm text-gray-700 dark:text-gray-400">TOTAL KESELURUHAN</td>
                        <td class="px-5 py-3 text-center text-sm text-gray-700 dark:text-gray-400">{{ number_format($grandAwal) }}</td>
                        <td class="px-5 py-3 text-center text-sm text-brand-500 dark:text-brand-400">{{ number_format($grandPabrik) }}</td>
                        <td class="px-5 py-3 text-center text-sm text-warning-600 dark:text-warning-400">{{ number_format($grandRetur) }}</td>
                        <td class="px-5 py-3 text-center text-sm text-blue-light-500">{{ number_format($grandMasuk) }}</td>
                        <td class="px-5 py-3 text-center text-sm text-error-600 dark:text-error-500">{{ number_format($grandSales) }}</td>
                        <td class="px-5 py-3 text-center text-sm text-success-600 dark:text-success-500">{{ number_format($grandBaik) }}</td>
                        <td class="px-5 py-3 text-center text-sm text-error-600 dark:text-error-500">{{ number_format($grandRusak) }}</td>
                        <td class="px-5 py-3 text-center text-sm text-success-600 dark:text-success-500">{{ number_format($grandTotal) }}</td>
                    </tr>
                </tfoot>

                @endif

            </table>
        </div>
    </div>

    @if($data->hasPages())
    <div class="border-t border-gray-200 dark:border-gray-800">
        {{ $data->appends(request()->only(['tanggal_awal', 'tanggal_akhir']))->links('vendor.pagination.tailadmin') }}
    </div>
    @endif

</div>
@endsection
