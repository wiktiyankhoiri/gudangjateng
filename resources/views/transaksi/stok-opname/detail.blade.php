@extends('layouts.app')

@section('content')

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

    <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-gray-800">
        <h5 class="text-lg font-semibold text-gray-800 dark:text-white/90">
            Detail Stok Opname
            <span class="ml-2 text-sm font-normal text-gray-500">#{{ esc($opname->no_opname) }}</span>
        </h5>
        <a href="{{ route('transaksi.stokopname.index') }}"
           class="inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs ring-1 ring-inset ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
            <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M17 10C17 10.4142 16.6642 10.75 16.25 10.75L6.61213 10.75L10.0645 14.0294C10.3608 14.3135 10.3707 14.7882 10.0866 15.0845C9.80248 15.3808 9.32778 15.3907 9.03147 15.1066L4.33147 10.6316C4.1867 10.4929 4.10663 10.3003 4.10663 10.1C4.10663 9.89972 4.1867 9.70713 4.33147 9.56841L9.03147 5.09341C9.32778 4.80931 9.80248 4.8192 10.0866 5.11551C10.3707 5.41182 10.3608 5.88652 10.0645 6.17062L6.61213 9.45L16.25 9.45C16.6642 9.45 17 9.78579 17 10.2L17 10Z" fill="currentColor"/></svg>
            Kembali
        </a>
    </div>

    <div class="p-5">

        <!-- HEADER INFO -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
            <div>
                <p class="mb-1 text-xs font-medium text-gray-500 uppercase dark:text-gray-400">No Opname</p>
                <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ esc($opname->no_opname) }}</p>
            </div>
            <div>
                <p class="mb-1 text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Tanggal</p>
                <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ date('d/m/Y', strtotime($opname->tanggal_opname)) }}</p>
            </div>
            <div>
                <p class="mb-1 text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Petugas</p>
                <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ esc($opname->nama_user) }}</p>
            </div>
            <div>
                <p class="mb-1 text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Status</p>
                <p class="text-sm font-medium">
                    @if($opname->status === 'draft')
                        <span class="inline-flex items-center rounded-full bg-warning-50 px-2.5 py-1 text-xs font-medium text-warning-600 dark:bg-warning-500/15 dark:text-warning-400">Draft</span>
                    @elseif($opname->status === 'selesai')
                        <span class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-1 text-xs font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">Selesai</span>
                    @elseif($opname->status === 'diterapkan')
                        <span class="inline-flex items-center rounded-full bg-success-50 px-2.5 py-1 text-xs font-medium text-success-600 dark:bg-success-500/15 dark:text-success-400">Diterapkan</span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-error-50 px-2.5 py-1 text-xs font-medium text-error-600 dark:bg-error-500/15 dark:text-error-400">Dibatalkan</span>
                    @endif
                </p>
            </div>
        </div>

        @if($opname->catatan)
        <div class="mt-4">
            <p class="mb-1 text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Catatan</p>
            <p class="text-sm text-gray-800 dark:text-white/90">{{ esc($opname->catatan) }}</p>
        </div>
        @endif

        <!-- ACTION BUTTONS -->
        @if($opname->status === 'draft')
        <div class="mt-6 flex items-center gap-3">
            <form method="post" action="{{ route('transaksi.stokopname.selesaikan', $opname->id) }}" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600" data-confirm-message="Selesaikan opname? Stok tidak akan berubah." data-confirm-ok="Ya, Selesaikan">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16.25 5.75L7.25 14.75L3.75 11.25" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Selesaikan
                </button>
            </form>
            <form method="post" action="{{ route('transaksi.stokopname.batalkan', $opname->id) }}" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-3 text-sm font-medium text-error-600 shadow-theme-xs ring-1 ring-inset ring-gray-300 transition hover:bg-error-50 dark:bg-gray-800 dark:text-error-400 dark:ring-gray-700 dark:hover:bg-error-500/10" data-confirm-message="Batalkan opname?" data-confirm-ok="Ya, Batalkan">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15 5L5 15M5 5L15 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Batalkan
                </button>
            </form>
        </div>
        @endif

        @if($opname->status === 'selesai')
        <div class="mt-6 flex items-center gap-3">
            <form method="post" action="{{ route('transaksi.stokopname.terapkan', $opname->id) }}" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-success-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-success-600" data-confirm-message="Terapkan opname? Stok akan disesuaikan dengan hasil opname." data-confirm-ok="Ya, Terapkan">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16.25 5.75L7.25 14.75L3.75 11.25" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Terapkan ke Stok
                </button>
            </form>
            <form method="post" action="{{ route('transaksi.stokopname.batalkan', $opname->id) }}" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-3 text-sm font-medium text-error-600 shadow-theme-xs ring-1 ring-inset ring-gray-300 transition hover:bg-error-50 dark:bg-gray-800 dark:text-error-400 dark:ring-gray-700 dark:hover:bg-error-500/10" data-confirm-message="Batalkan opname?" data-confirm-ok="Ya, Batalkan">
                    Batalkan
                </button>
            </form>
        </div>
        @endif

        <hr class="my-6 border-gray-200 dark:border-gray-800">

        <!-- TOTAL SELISIH -->
        <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="rounded-xl border border-gray-200 bg-gray-50 px-5 py-4 dark:border-gray-700 dark:bg-gray-800/50">
                <p class="text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Total Selisih Baik</p>
                <p class="mt-1 text-xl font-bold {{ $totalSelisihBaik >= 0 ? 'text-success-600 dark:text-success-400' : 'text-error-600 dark:text-error-400' }}">
                    {{ $totalSelisihBaik >= 0 ? '+' : '' }}{{ number_format($totalSelisihBaik) }}
                </p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-gray-50 px-5 py-4 dark:border-gray-700 dark:bg-gray-800/50">
                <p class="text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Total Selisih Rusak</p>
                <p class="mt-1 text-xl font-bold {{ $totalSelisihRusak >= 0 ? 'text-success-600 dark:text-success-400' : 'text-error-600 dark:text-error-400' }}">
                    {{ $totalSelisihRusak >= 0 ? '+' : '' }}{{ number_format($totalSelisihRusak) }}
                </p>
            </div>
        </div>

        <!-- TABLE DETAIL -->
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">KODE</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">BARANG</th>
                        <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">STOK SISTEM</th>
                        <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">STOK FISIK</th>
                        <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">SELISIH</th>
                        <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">KETERANGAN</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($detail as $d)
                    <tr>
                        <td class="px-3 py-3 text-center">
                            <span class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-1 text-xs font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">
                                {{ esc($d->kode_barang) }}
                            </span>
                        </td>
                        <td class="px-3 py-3 text-sm text-gray-800 dark:text-white/90">{{ esc($d->nama_barang) }}</td>
                        <td class="px-3 py-3 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ number_format($d->stok_sistem_baik) }} baik / {{ number_format($d->stok_sistem_rusak) }} rusak
                        </td>
                        <td class="px-3 py-3 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ number_format($d->stok_fisik_baik) }} baik / {{ number_format($d->stok_fisik_rusak) }} rusak
                        </td>
                        <td class="px-3 py-3 text-center">
                            @php
                                $selBaik = (int) $d->selisih_baik;
                                $selRusak = (int) $d->selisih_rusak;
                            @endphp
                            @if($selBaik === 0 && $selRusak === 0)
                                <span class="text-sm text-gray-400">-</span>
                            @else
                                <span class="text-sm font-medium">
                                    @if($selBaik !== 0)
                                        <span class="{{ $selBaik > 0 ? 'text-success-600 dark:text-success-400' : 'text-error-600 dark:text-error-400' }}">
                                            {{ $selBaik > 0 ? '+' : '' }}{{ $selBaik }} baik
                                        </span>
                                    @endif
                                    @if($selBaik !== 0 && $selRusak !== 0)
                                        <span class="text-gray-400"> / </span>
                                    @endif
                                    @if($selRusak !== 0)
                                        <span class="{{ $selRusak > 0 ? 'text-success-600 dark:text-success-400' : 'text-error-600 dark:text-error-400' }}">
                                            {{ $selRusak > 0 ? '+' : '' }}{{ $selRusak }} rusak
                                        </span>
                                    @endif
                                </span>
                            @endif
                        </td>
                        <td class="px-3 py-3 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ $d->keterangan ?: '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada detail barang</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($detail->hasPages())
        <div class="border-t border-gray-200 dark:border-gray-800 mt-4">
            {{ $detail->links('vendor.pagination.tailadmin') }}
        </div>
        @endif
    </div>
</div>

@endsection
