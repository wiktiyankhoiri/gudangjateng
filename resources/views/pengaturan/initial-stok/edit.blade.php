@extends('layouts.app')

@section('content')

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <form data-protect-submit="true" data-confirm-message="Pastikan data sudah sesuai dengan nota fisik. Lanjutkan update?" data-confirm-ok="Ya, Update" method="post" action="{{ route('pengaturan.initialstok.update', $data->id) }}">
        @csrf @method('PUT')
        <div class="p-5 sm:p-6">
            @if ($errors->any())
            <div class="mb-4 rounded-lg border-l-4 border-l-error-500 bg-error-50 p-4 dark:bg-error-500/15">
                <ul class="mb-0 list-disc ps-4 text-sm text-red-800 dark:text-red-200">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            @php
                $currentBarang = null;
                foreach ($barang as $b) {
                    if ($b->id == $data->barang_id) {
                        $currentBarang = $b;
                        break;
                    }
                }
            @endphp

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Barang</label>
                    <input type="hidden" name="barang_id" value="{{ $data->barang_id }}">
                    <input type="text" readonly value="{{ esc(($currentBarang->kode_barang ?? '').' - '.($currentBarang->nama_barang ?? '')) }}"
                        class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-gray-50 bg-none px-4 py-2.5 text-sm text-gray-500 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400 dark:placeholder:text-white/30">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Jml Baik</label>
                    <input type="number" name="qty_baik" min="0" step="1" value="{{ old('qty_baik', $data->qty_baik) }}" required
                        class="dark:bg-dark-900 shadow-theme-xs h-11 w-24 rounded-lg border border-gray-300 bg-transparent bg-none px-3 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Jml Rusak</label>
                    <input type="number" name="qty_rusak" min="0" step="1" value="{{ old('qty_rusak', $data->qty_rusak) }}" required
                        class="dark:bg-dark-900 shadow-theme-xs h-11 w-24 rounded-lg border border-gray-300 bg-transparent bg-none px-3 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Keterangan</label>
                    <textarea name="keterangan" rows="3" placeholder="Masukkan keterangan tambahan jika ada"
                        class="h-11 w-full rounded-lg border dark:bg-dark-900 border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800 uppercase"
                        oninput="const s=this.selectionStart;this.value=this.value.toUpperCase();this.setSelectionRange(s,s)">{{ old('keterangan', $data->keterangan) }}</textarea>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-between px-5 py-4">
            <a href="{{ route('pengaturan.initialstok.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs ring-1 ring-inset ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.8334 10H4.16669M4.16669 10L9.16669 15M4.16669 10L9.16669 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Kembali
            </a>
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.8334 8.33333V15.8333C15.8334 16.7538 15.0872 17.5 14.1667 17.5H5.83341C4.91294 17.5 4.16675 16.7538 4.16675 15.8333V4.16667C4.16675 3.24619 4.91294 2.5 5.83341 2.5H11.6667M15.8334 8.33333L11.6667 4.16667M15.8334 8.33333H11.6667V4.16667M7.50008 12.5H10.0001M7.50008 15H12.5001" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Update
            </button>
        </div>
    </form>
</div>

@endsection
