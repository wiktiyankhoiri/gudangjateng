@extends('layouts.app')

@section('content')

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="p-5">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <div>
                <p class="mb-1 text-xs font-medium text-gray-500 uppercase dark:text-gray-400">No Surat</p>
                <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $barangKeluar->no_surat ?? '-' }}</p>
            </div>
            <div>
                <p class="mb-1 text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Tanggal</p>
                <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $barangKeluar->tanggal ? $barangKeluar->tanggal->format('d-m-Y') : '-' }}</p>
            </div>
            <div>
                <p class="mb-1 text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Toko</p>
                <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $barangKeluar->toko->nama_toko ?? '-' }}</p>
            </div>
            <div>
                <p class="mb-1 text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Sales</p>
                <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $barangKeluar->sales->nama ?? '-' }}</p>
            </div>
            <div class="md:col-span-2">
                <p class="mb-1 text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Keterangan</p>
                <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $barangKeluar->keterangan ?: '-' }}</p>
            </div>
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
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Qty</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($detail as $i => $d)
                <tr>
                    <td class="px-5 py-4 text-sm text-gray-800 dark:text-white/90">{{ $i + 1 }}</td>
                    <td class="px-5 py-4 text-sm text-gray-800 dark:text-white/90">{{ $d->barang->kode_barang ?? '-' }}</td>
                    <td class="px-5 py-4 text-sm text-gray-800 dark:text-white/90">{{ $d->barang->nama_barang ?? '-' }}</td>
                    <td class="px-5 py-4 text-center text-sm font-semibold text-brand-600 dark:text-brand-400">{{ number_format($d->qty_baik ?? 0) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Tidak ada detail item</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="flex items-center justify-start px-5 py-4 border-t border-gray-200 dark:border-gray-800">
        <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs ring-1 ring-inset ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
            <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.8334 10H4.16669M4.16669 10L9.16669 15M4.16669 10L9.16669 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Kembali
        </a>
    </div>
</div>

@endsection
