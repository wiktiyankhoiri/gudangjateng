@extends('layouts.app')

@section('content')

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="p-5">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <div>
                <p class="mb-1 text-xs font-medium text-gray-500 uppercase dark:text-gray-400">No Mutasi</p>
                <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $mutasi->no_mutasi ?? '-' }}</p>
            </div>
            <div>
                <p class="mb-1 text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Tanggal</p>
                <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $mutasi->tanggal ? $mutasi->tanggal->format('d-m-Y') : '-' }}</p>
            </div>
            <div>
                <p class="mb-1 text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Keterangan</p>
                <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $mutasi->keterangan ?: '-' }}</p>
            </div>
            @if($mutasi->sales)
            <div>
                <p class="mb-1 text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Sales</p>
                <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $mutasi->sales->nama }}</p>
            </div>
            @endif
        </div>
    </div>
</div>

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] mt-4">
    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
        <h5 class="text-lg font-semibold text-gray-800 dark:text-white/90">Detail Item Barang</h5>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full table-sticky">
            <thead>
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400 w-[60px]">No</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Kode Barang</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Nama Barang</th>
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Tipe</th>
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Qty</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($detail as $i => $d)
                <tr>
                    <td class="px-5 py-4 text-sm text-gray-800 dark:text-white/90">{{ $i + 1 }}</td>
                    <td class="px-5 py-4 text-sm text-gray-800 dark:text-white/90">{{ $d->barang->kode_barang ?? '-' }}</td>
                    <td class="px-5 py-4 text-sm text-gray-800 dark:text-white/90">{{ $d->barang->nama_barang ?? '-' }}</td>
                    <td class="px-5 py-4 text-center">
                        @if($d->tipe == 'baik_ke_rusak')
                            <span class="inline-flex items-center rounded-full bg-warning-50 px-2.5 py-1 text-xs font-medium text-warning-600 dark:bg-warning-500/15 dark:text-warning-400">Baik &rarr; Rusak</span>
                        @elseif($d->tipe == 'rusak_ke_baik')
                            <span class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-1 text-xs font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">Rusak &rarr; Baik</span>
                        @elseif($d->tipe == 'baik_ke_sales')
                            <span class="inline-flex items-center rounded-full bg-purple-50 px-2.5 py-1 text-xs font-medium text-purple-600 dark:bg-purple-500/15 dark:text-purple-400">Baik &rarr; Sales</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-cyan-50 px-2.5 py-1 text-xs font-medium text-cyan-600 dark:bg-cyan-500/15 dark:text-cyan-400">Sales &rarr; Baik</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-center text-sm font-semibold text-gray-800 dark:text-white/90">{{ number_format($d->qty) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Tidak ada detail item</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="flex items-center justify-start px-5 py-4 border-t border-gray-200 dark:border-gray-800">
        <a href="{{ route('transaksi.mutasi.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs ring-1 ring-inset ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
            <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.8334 10H4.16669M4.16669 10L9.16669 15M4.16669 10L9.16669 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Kembali
        </a>
    </div>
</div>

@endsection
