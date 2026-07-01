@extends('layouts.app')

@section('content')

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

    <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-gray-800">
        <h5 class="text-lg font-semibold text-gray-800 dark:text-white/90">{{ $title ?? 'Detail Penyesuaian Stok' }}</h5>
        <a href="{{ route('transaksi.penyesuaianstok.index') }}"
           class="inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs ring-1 ring-inset ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
            <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M17 10C17 10.4142 16.6642 10.75 16.25 10.75L6.61213 10.75L10.0645 14.0294C10.3608 14.3135 10.3707 14.7882 10.0866 15.0845C9.80248 15.3808 9.32778 15.3907 9.03147 15.1066L4.33147 10.6316C4.1867 10.4929 4.10663 10.3003 4.10663 10.1C4.10663 9.89972 4.1867 9.70713 4.33147 9.56841L9.03147 5.09341C9.32778 4.80931 9.80248 4.8192 10.0866 5.11551C10.3707 5.41182 10.3608 5.88652 10.0645 6.17062L6.61213 9.45L16.25 9.45C16.6642 9.45 17 9.78579 17 10.2L17 10Z" fill="currentColor"/></svg>
            Kembali
        </a>
    </div>

    <div class="p-5">

        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <div>
                <p class="mb-1 text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Tanggal</p>
                <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $detail->tanggal ? $detail->tanggal->format('d-m-Y') : '-' }}</p>
            </div>
            <div>
                <p class="mb-1 text-xs font-medium text-gray-500 uppercase dark:text-gray-400">User</p>
                <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $detail->nama_user ?? '-' }}</p>
            </div>
            <div>
                <p class="mb-1 text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Barang</p>
                <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $detail->kode_barang ?? '-' }} - {{ $detail->nama_barang ?? '-' }}</p>
            </div>
        </div>

        <hr class="my-6 border-gray-200 dark:border-gray-800">

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

            <div class="rounded-xl border border-success-200 bg-white dark:border-success-800 dark:bg-white/[0.03]">
                <div class="border-b border-success-200 bg-success-50 px-5 py-3 dark:border-success-800 dark:bg-success-500/15">
                    <h6 class="text-sm font-semibold text-success-700 dark:text-success-400">Stok Baik</h6>
                </div>
                <div class="p-5">
                    <table class="min-w-full">
                        <tr>
                            <th width="40%" class="pb-3 pr-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Sebelum</th>
                            <td class="pb-3 text-sm text-gray-800 dark:text-white/90">{{ number_format($detail->stok_baik_sebelum ?? 0) }}</td>
                        </tr>
                        <tr>
                            <th width="40%" class="pb-3 pr-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Sesudah</th>
                            <td class="pb-3 text-sm text-gray-800 dark:text-white/90">{{ number_format($detail->stok_baik_sesudah ?? 0) }}</td>
                        </tr>
                        <tr>
                            <th width="40%" class="pr-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Selisih</th>
                            <td class="text-sm">
                                @if($detail->selisih_baik >= 0)
                                    <span class="font-semibold text-success-600 dark:text-success-400">+{{ number_format($detail->selisih_baik ?? 0) }}</span>
                                @else
                                    <span class="font-semibold text-error-600 dark:text-error-400">{{ number_format($detail->selisih_baik ?? 0) }}</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="rounded-xl border border-error-200 bg-white dark:border-error-800 dark:bg-white/[0.03]">
                <div class="border-b border-error-200 bg-error-50 px-5 py-3 dark:border-error-800 dark:bg-error-500/15">
                    <h6 class="text-sm font-semibold text-error-700 dark:text-error-400">Stok Rusak</h6>
                </div>
                <div class="p-5">
                    <table class="min-w-full">
                        <tr>
                            <th width="40%" class="pb-3 pr-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Sebelum</th>
                            <td class="pb-3 text-sm text-gray-800 dark:text-white/90">{{ number_format($detail->stok_rusak_sebelum ?? 0) }}</td>
                        </tr>
                        <tr>
                            <th width="40%" class="pb-3 pr-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Sesudah</th>
                            <td class="pb-3 text-sm text-gray-800 dark:text-white/90">{{ number_format($detail->stok_rusak_sesudah ?? 0) }}</td>
                        </tr>
                        <tr>
                            <th width="40%" class="pr-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Selisih</th>
                            <td class="text-sm">
                                @if($detail->selisih_rusak >= 0)
                                    <span class="font-semibold text-success-600 dark:text-success-400">+{{ number_format($detail->selisih_rusak ?? 0) }}</span>
                                @else
                                    <span class="font-semibold text-error-600 dark:text-error-400">{{ number_format($detail->selisih_rusak ?? 0) }}</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

        </div>

        <div class="mt-6">
            <label class="text-sm font-medium text-gray-800 dark:text-white/90">Alasan Penyesuaian</label>
            <div class="mt-2 rounded-lg border border-gray-200 bg-gray-50 p-4 text-sm text-gray-700 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-300">
                {!! nl2br(e($detail->alasan ?? '-')) !!}
            </div>
        </div>

    </div>

</div>
@endsection
