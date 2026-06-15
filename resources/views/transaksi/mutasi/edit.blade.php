@extends('layouts.app')

@section('content')

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

    <form data-protect-submit="true" data-confirm-message="Pastikan data sudah sesuai dengan nota fisik. Lanjutkan update?" data-confirm-ok="Ya, Update" method="post"
          action="{{ route('transaksi.mutasi.update', $mutasi->id) }}"
          id="formMutasi">

        @csrf @method('PUT')

        <div class="p-5 sm:p-6">
            @php
                // Cek stok per item untuk peringatan
                $adaPeringatan = false;
                $peringatanItems = [];
                $barangNama = $barang->pluck('nama_barang', 'id')->toArray();
                if (isset($stokPerBarang) && isset($detail)):
                    foreach ($detail as $d):
                        $stok = $stokPerBarang[$d->barang_id] ?? null;
                        if ($stok):
                            if ($d->tipe == 'baik_ke_rusak' && (int)$stok['stok_baik'] < (int)$d->qty):
                                $adaPeringatan = true;
                                $selisih = (int)$d->qty - (int)$stok['stok_baik'];
                                $nama = $barangNama[$d->barang_id] ?? ('Barang #' . $d->barang_id);
                                $peringatanItems[] = sprintf('%s (mutasi baik→rusak %d, stok baik saat ini %d, selisih -%d)', $nama, $d->qty, $stok['stok_baik'], $selisih);
                            endif;
                            if ($d->tipe == 'rusak_ke_baik' && (int)$stok['stok_rusak'] < (int)$d->qty):
                                $adaPeringatan = true;
                                $selisih = (int)$d->qty - (int)$stok['stok_rusak'];
                                $nama = $barangNama[$d->barang_id] ?? ('Barang #' . $d->barang_id);
                                $peringatanItems[] = sprintf('%s (mutasi rusak→baik %d, stok rusak saat ini %d, selisih -%d)', $nama, $d->qty, $stok['stok_rusak'], $selisih);
                            endif;
                        endif;
                    endforeach;
                endif;
            @endphp
            @if($adaPeringatan)
            <div class="flex w-full border-l-6 border-warning-500 bg-warning-50 px-7 py-5 mb-6 dark:bg-warning-500/15 dark:border-warning-500/30" role="alert">
                <div class="mr-3 flex h-10 w-10 items-center justify-center rounded-lg bg-warning-500">
                    <svg class="fill-white" width="18" height="18" viewBox="0 0 22 22" xmlns="http://www.w3.org/2000/svg"><path d="M11 0C4.93 0 0 4.93 0 11s4.93 11 11 11 11-4.93 11-11S17.07 0 11 0zm0 16.5a1.1 1.1 0 110-2.2 1.1 1.1 0 010 2.2zm.9-4.95a.9.9 0 01-1.8 0V5.5a.9.9 0 011.8 0v6.05z"/></svg>
                </div>
                <div class="w-full">
                    <h5 class="mb-2 text-lg font-semibold text-black dark:text-white">Perhatian!</h5>
                    <p class="text-sm text-gray-700 dark:text-white/80">Stok barang berikut sudah berkurang karena transaksi lain setelah mutasi ini dibuat:</p>
                    <div class="mt-2 text-sm text-gray-700 dark:text-white/80 space-y-1">
                        @foreach($peringatanItems as $item)
                        <div>{{ $item }}</div>
                        @endforeach
                    </div>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400 border-t border-warning-200 dark:border-warning-700 pt-2">Jumlah barang di atas tidak bisa dikurangi. Jika perlu ubah jumlah, buat Penyesuaian Stok atau hapus + buat transaksi baru.</p>
                </div>
            </div>
            @endif

            <!-- ROW 1: NO MUTASI + TANGGAL + KETERANGAN -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

                <!-- NO MUTASI -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">No Mutasi</label>
                    <input type="text" name="no_mutasi"
                           class="h-11 w-full rounded-lg border dark:bg-dark-900 border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800 uppercase"
                           value="{{ old('no_mutasi', $mutasi->no_mutasi) }}" required
                           oninput="const s=this.selectionStart;this.value=this.value.toUpperCase();this.setSelectionRange(s,s)">
                    @error('no_mutasi')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
                </div>

                <!-- TANGGAL -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tanggal</label>
                    <div class="relative">
                        <input type="text" name="tanggal" value="{{ old('tanggal', $mutasi->tanggal ? $mutasi->tanggal->format('Y-m-d') : date('Y-m-d')) }}"
                               class="datepicker h-11 w-full rounded-lg border dark:bg-dark-900 border-gray-300 bg-transparent py-2.5 pr-11 pl-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                               placeholder="Pilih tanggal" required>
                        <span class="pointer-events-none absolute top-1/2 right-3 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                            <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M6.66659 1.5415C7.0808 1.5415 7.41658 1.87729 7.41658 2.2915V2.99984H12.5833V2.2915C12.5833 1.87729 12.919 1.5415 13.3333 1.5415C13.7475 1.5415 14.0833 1.87729 14.0833 2.2915V2.99984L15.4166 2.99984C16.5212 2.99984 17.4166 3.89527 17.4166 4.99984V7.49984V15.8332C17.4166 16.9377 16.5212 17.8332 15.4166 17.8332H4.58325C3.47868 17.8332 2.58325 16.9377 2.58325 15.8332V7.49984V4.99984C2.58325 3.89527 3.47868 2.99984 4.58325 2.99984L5.91659 2.99984V2.2915C5.91659 1.87729 6.25237 1.5415 6.66659 1.5415ZM6.66659 4.49984H4.58325C4.30711 4.49984 4.08325 4.7237 4.08325 4.99984V6.74984H15.9166V4.99984C15.9166 4.7237 15.6927 4.49984 15.4166 4.49984H13.3333H6.66659ZM15.9166 8.24984H4.08325V15.8332C4.08325 16.1093 4.30711 16.3332 4.58325 16.3332H15.4166C15.6927 16.3332 15.9166 16.1093 15.9166 15.8332V8.24984Z" fill=""/>
                            </svg>
                        </span>
                    </div>
                    @error('tanggal')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
                </div>

                <!-- KETERANGAN -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Keterangan</label>
                    <textarea name="keterangan"
                              class="h-11 w-full rounded-lg border dark:bg-dark-900 border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800 uppercase"
                              rows="2"
                              oninput="const s=this.selectionStart;this.value=this.value.toUpperCase();this.setSelectionRange(s,s)">{{ old('keterangan', $mutasi->keterangan) }}</textarea>
                </div>
            </div>

            <hr class="border-gray-200 dark:border-gray-800 my-6">

            <!-- DETAIL -->
            <div class="flex items-center justify-between mb-4">
                <h5 class="text-lg font-semibold text-gray-800 dark:text-white/90">Detail Barang</h5>
                <button type="button" id="btnAddRow"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 3.75V16.25M16.25 10H3.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                    Tambah Barang
                </button>
            </div>

            <div id="bodyBarang" class="space-y-2">
                @foreach($detail as $d)
                <div class="flex flex-col sm:flex-row gap-2 items-start p-3 border border-gray-200 rounded-lg dark:border-gray-800 row-barang">
                    <div class="w-full" style="flex:1 1 0%; min-width:200px;">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">Barang</label>
                        <select name="barang_id[]" class="h-11 w-full rounded-lg border dark:bg-dark-900 border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800 barangSelect" required>
                            <option value="">-- Pilih Barang --</option>
                            @foreach($barang as $b)
                                <option value="{{ $b->id }}" {{ $b->id == $d->barang_id ? 'selected' : '' }}>{{ $b->kode_barang }} - {{ $b->nama_barang }}</option>
                            @endforeach
                        </select>
                        <div class="stok-info mt-1 text-xs hidden"></div>
                    </div>
                    <div class="w-full sm:w-32">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">Tipe Mutasi</label>
                        <select name="tipe[]" class="h-11 w-full rounded-lg border dark:bg-dark-900 border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" required>
                            <option value="">-- Pilih Tipe --</option>
                            <option value="baik_ke_rusak" {{ $d->tipe == 'baik_ke_rusak' ? 'selected' : '' }}>Baik &rarr; Rusak</option>
                            <option value="rusak_ke_baik" {{ $d->tipe == 'rusak_ke_baik' ? 'selected' : '' }}>Rusak &rarr; Baik</option>
                        </select>
                    </div>
                    <div class="w-full sm:w-16">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">Qty</label>
                        <input type="number" name="qty[]" class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-3 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" min="1" value="{{ $d->qty }}" required>
                    </div>
                    <div class="w-full sm:w-20 flex justify-center" style="padding-top:28px;flex-shrink:0;">
                        <button type="button" class="inline-flex items-center justify-center gap-2 rounded-lg bg-error-50 p-2 text-error-600 hover:bg-error-100 dark:bg-error-500/15 dark:text-error-400 btnRemoveRow">
                            <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M7.5 2.5C7.5 2.08579 7.83579 1.75 8.25 1.75H11.75C12.1642 1.75 12.5 2.08579 12.5 2.5V3.5H6.25V2.5H7.5ZM4.75 4.5V3.5C4.75 2.25736 5.75736 1.25 7 1.25H13C14.2426 1.25 15.25 2.25736 15.25 3.5V4.5H16.5C17.1904 4.5 17.75 5.05964 17.75 5.75C17.75 6.44036 17.1904 7 16.5 7H16.1273L15.2865 16.2885C15.1399 17.8235 13.8455 19 12.3043 19H7.6957C6.15448 19 4.86011 17.8235 4.71346 16.2885L3.87273 7H3.5C2.80964 7 2.25 6.44036 2.25 5.75C2.25 5.05964 2.80964 4.5 3.5 4.5H4.75ZM6.21817 7L7.04185 16.1443C7.07708 16.5095 7.39145 16.75 7.6957 16.75H12.3043C12.6086 16.75 12.9229 16.5095 12.9582 16.1443L13.7818 7H6.21817Z" fill="currentColor"/></svg>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>

        </div>

        <!-- FOOTER -->
        <div class="flex items-center justify-between px-5 py-4 border-t border-gray-200 dark:border-gray-800">
            <a href="{{ route('transaksi.mutasi.index') }}"
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs ring-1 ring-inset ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.8334 10H4.16669M4.16669 10L9.16669 15M4.16669 10L9.16669 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Kembali
            </a>
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600" data-loading-text="Menyimpan...">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.8334 8.33333V15.8333C15.8334 16.7538 15.0872 17.5 14.1667 17.5H5.83341C4.91294 17.5 4.16675 16.7538 4.16675 15.8333V4.16667C4.16675 3.24619 4.91294 2.5 5.83341 2.5H11.6667M15.8334 8.33333L11.6667 4.16667M15.8334 8.33333H11.6667V4.16667M7.50008 12.5H10.0001M7.50008 15H12.5001" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Update
            </button>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // =========================================
    // INIT TOM SELECT (existing rows)
    // =========================================
    document.querySelectorAll('.barangSelect').forEach(function(el) {
        new TomSelect(el, { maxItems:1, searchField:['text','value'], dropdownParent:'body', plugins:{clear_button:{title:'Hapus pilihan'}} });
    });

    // =========================================
    // TAMBAH ROW
    // =========================================
    document.getElementById('btnAddRow').addEventListener('click', function() {

        let row = `
        <div class="flex flex-col sm:flex-row gap-2 items-start p-3 border border-gray-200 rounded-lg dark:border-gray-800 row-barang">
            <div class="w-full" style="flex:1 1 0%; min-width:200px;">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">Barang</label>
                <select name="barang_id[]" class="h-11 w-full rounded-lg border dark:bg-dark-900 border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800 barangSelect" required>
                    <option value="">-- Pilih Barang --</option>
                    @foreach($barang as $b)
                        <option value="{{ $b->id }}">{{ $b->kode_barang }} - {{ $b->nama_barang }}</option>
                    @endforeach
                </select>
                <div class="stok-info mt-1 text-xs hidden"></div>
            </div>
            <div class="w-full sm:w-32">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">Tipe Mutasi</label>
                <select name="tipe[]" class="h-11 w-full rounded-lg border dark:bg-dark-900 border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" required>
                    <option value="">-- Pilih Tipe --</option>
                    <option value="baik_ke_rusak">Baik &rarr; Rusak</option>
                    <option value="rusak_ke_baik">Rusak &rarr; Baik</option>
                </select>
            </div>
            <div class="w-full sm:w-16">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">Qty</label>
                <input type="number" name="qty[]" class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-3 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" min="1" value="1" required>
            </div>
            <div class="w-full sm:w-20 flex justify-center" style="padding-top:28px;flex-shrink:0;">
                <button type="button" class="inline-flex items-center justify-center gap-2 rounded-lg bg-error-50 p-2 text-error-600 hover:bg-error-100 dark:bg-error-500/15 dark:text-error-400 btnRemoveRow">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M7.5 2.5C7.5 2.08579 7.83579 1.75 8.25 1.75H11.75C12.1642 1.75 12.5 2.08579 12.5 2.5V3.5H6.25V2.5H7.5ZM4.75 4.5V3.5C4.75 2.25736 5.75736 1.25 7 1.25H13C14.2426 1.25 15.25 2.25736 15.25 3.5V4.5H16.5C17.1904 4.5 17.75 5.05964 17.75 5.75C17.75 6.44036 17.1904 7 16.5 7H16.1273L15.2865 16.2885C15.1399 17.8235 13.8455 19 12.3043 19H7.6957C6.15448 19 4.86011 17.8235 4.71346 16.2885L3.87273 7H3.5C2.80964 7 2.25 6.44036 2.25 5.75C2.25 5.05964 2.80964 4.5 3.5 4.5H4.75ZM6.21817 7L7.04185 16.1443C7.07708 16.5095 7.39145 16.75 7.6957 16.75H12.3043C12.6086 16.75 12.9229 16.5095 12.9582 16.1443L13.7818 7H6.21817Z" fill="currentColor"/></svg>
                </button>
            </div>
        </div>
        `;

        document.getElementById('bodyBarang').insertAdjacentHTML('beforeend', row);

        document.querySelectorAll('#bodyBarang > div:last-child .barangSelect').forEach(function(el) {
            new TomSelect(el, { maxItems:1, searchField:['text','value'], dropdownParent:'body', plugins:{clear_button:{title:'Hapus pilihan'}} });
        });

    });

    // =========================================
    // HAPUS ROW (delegation)
    // =========================================
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btnRemoveRow');
        if (!btn) return;

        if (document.querySelectorAll('#bodyBarang > div').length > 1) {
            btn.closest('.row-barang').remove();
        } else {
            showSystemMessage('warning', 'Minimal harus ada 1 item mutasi.');
        }

    });

    // =========================================
    // STOK INFO DISPLAY
    // =========================================
    var stokData = @json($stokPerBarang ?? []);
    var stokIndex = {};
    Object.keys(stokData).forEach(function(id) { stokIndex[id] = stokData[id]; });

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('barangSelect')) {
            var row = e.target.closest('.row-barang');
            var info = row ? row.querySelector('.stok-info') : null;
            if (!info) return;
            var stok = stokIndex[e.target.value];
            if (stok) {
                info.innerHTML = 'Stok: baik <strong>' + stok.stok_baik + '</strong> | rusak <strong>' + stok.stok_rusak + '</strong>';
                info.classList.remove('hidden');
            } else {
                info.innerHTML = 'Stok: <span class="text-warning-500">belum ada</span>';
                info.classList.remove('hidden');
            }
        }
    });

});
</script>
@endpush
