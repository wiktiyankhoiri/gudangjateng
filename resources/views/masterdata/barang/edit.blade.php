@extends('layouts.app')

@section('content')

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <form method="post" action="{{ route('masterdata.barang.update', $barang->id) }}" autocomplete="off">
        @csrf @method('PUT')
        <div class="p-5">
            @if ($errors->any())
            <div class="mb-4 rounded-lg border-l-4 border-l-error-500 bg-error-50 p-4 dark:bg-error-500/15">
                <ul class="mb-0 list-disc ps-4 text-sm text-red-800 dark:text-red-200">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="flex flex-col gap-6 pt-3 pb-3">
                <div class="flex flex-col gap-6 md:flex-row">
                    <div class="md:w-1/4">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Kode Barang</label>
                        <input type="text" name="kode_barang" value="{{ old('kode_barang', $barang->kode_barang) }}" placeholder="Masukkan kode barang" required
                            class="h-11 w-full rounded-lg border dark:bg-dark-900 border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800 uppercase"
                            oninput="const s=this.selectionStart;this.value=this.value.toUpperCase();this.setSelectionRange(s,s)">
                        @error('kode_barang')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:w-1/4">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Satuan</label>
                        <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                            <select name="satuan" required
                                class="select2 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                x-bind:class="isOptionSelected && 'text-gray-800 dark:text-white/90'"
                                @change="isOptionSelected = true">
                                <option value="">-- Pilih Satuan --</option>
                                <option value="PCS" {{ old('satuan', $barang->satuan) == 'PCS' ? 'selected' : '' }}>PCS</option>
                                <option value="SET" {{ old('satuan', $barang->satuan) == 'SET' ? 'selected' : '' }}>SET</option>
                            </select>
                            <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </div>
                        @error('satuan')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nama Barang</label>
                    <input type="text" name="nama_barang" value="{{ old('nama_barang', $barang->nama_barang) }}" placeholder="Masukkan nama barang" required
                        class="h-11 w-full rounded-lg border dark:bg-dark-900 border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800 uppercase"
                            oninput="const s=this.selectionStart;this.value=this.value.toUpperCase();this.setSelectionRange(s,s)">
                    @error('nama_barang')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="flex flex-col gap-6 md:flex-row">
                <div class="md:w-1/3">
                    <label class="mb-1.5 block text-sm font-medium text-warning-600 dark:text-warning-400">💰 Harga Gold (Rp)</label>
                    <input type="number" name="harga_gold" value="{{ old('harga_gold', $barang->harga_gold) }}" placeholder="0" min="0" step="0.01"
                        class="h-11 w-full rounded-lg border dark:bg-dark-900 border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
                    @error('harga_gold')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
                </div>
                <div class="md:w-1/3">
                    <label class="mb-1.5 block text-sm font-medium text-success-600 dark:text-success-400">💰 Harga Grosir (Rp)</label>
                    <input type="number" name="harga_grosir" value="{{ old('harga_grosir', $barang->harga_grosir) }}" placeholder="0" min="0" step="0.01"
                        class="h-11 w-full rounded-lg border dark:bg-dark-900 border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
                    @error('harga_grosir')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
                </div>
                <div class="md:w-1/3">
                    <label class="mb-1.5 block text-sm font-medium text-purple-600 dark:text-purple-400">💰 Harga Khusus (Rp)</label>
                    <input type="number" name="harga_khusus" value="{{ old('harga_khusus', $barang->harga_khusus) }}" placeholder="0" min="0" step="0.01"
                        class="h-11 w-full rounded-lg border dark:bg-dark-900 border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
                    @error('harga_khusus')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
                </div>
            </div>
        <div class="flex items-center justify-between px-5 py-4">
            <a href="{{ route('masterdata.barang.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs ring-1 ring-inset ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.8334 10H4.16669M4.16669 10L9.16669 15M4.16669 10L9.16669 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Kembali
            </a>
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.8334 8.33333V15.8333C15.8334 16.7538 15.0872 17.5 14.1667 17.5H5.83341C4.91294 17.5 4.16675 16.7538 4.16675 15.8333V4.16667C4.16675 3.24619 4.91294 2.5 5.83341 2.5H11.6667M15.8334 8.33333L11.6667 4.16667M15.8334 8.33333H11.6667V4.16667M7.50008 12.5H10.0001M7.50008 15H12.5001" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Update Barang
            </button>
        </div>    </form>
</div>
@endsection
