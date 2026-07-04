@extends('layouts.app')

@section('content')

<x-error-alert :errors="$errors" />

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

    <form data-protect-submit="true" data-confirm-message="Pastikan data sudah benar. Lanjutkan update?" data-confirm-ok="Ya, Update" method="post"
          action="{{ route('transaksi.stokopname.update', $opname->id) }}">

        @csrf

        <div class="p-5 sm:p-6">

            <!-- ROW HEADER -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-6">

                <!-- CARI BARANG -->
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Cari Barang</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500">
                            <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M9 2.5C5.41015 2.5 2.5 5.41015 2.5 9C2.5 12.5899 5.41015 15.5 9 15.5C10.5459 15.5 11.9637 14.9374 13.0629 14.0075L16.9697 17.9142C17.2626 18.2071 17.7374 18.2071 18.0303 17.9142C18.3232 17.6213 18.3232 17.1464 18.0303 16.8536L14.1236 12.9469C15.0535 11.8477 15.5 10.3292 15.5 9C15.5 5.41015 12.5899 2.5 9 2.5ZM4 9C4 6.23858 6.23858 4 9 4C11.7614 4 14 6.23858 14 9C14 11.7614 11.7614 14 9 14C6.23858 14 4 11.7614 4 9Z" fill="currentColor"/></svg>
                        </span>
                        <input type="text" id="cariBarang" placeholder="Cari kode/nama barang..."
                               class="h-11 w-full rounded-lg border dark:bg-dark-900 border-gray-300 bg-transparent py-2.5 pr-4 pl-9 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
                    </div>
                </div>

                <!-- TANGGAL -->
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tanggal Opname</label>
                    <div class="relative">
                        <input type="text" name="tanggal_opname" value="{{ old('tanggal_opname', $opname->tanggal_opname) }}"
                               class="datepicker h-11 w-full rounded-lg border dark:bg-dark-900 border-gray-300 bg-transparent py-2.5 pr-11 pl-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                               placeholder="Pilih tanggal" required>
                        <span class="pointer-events-none absolute top-1/2 right-3 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                            <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M6.66659 1.5415C7.0808 1.5415 7.41658 1.87729 7.41658 2.2915V2.99984H12.5833V2.2915C12.5833 1.87729 12.919 1.5415 13.3333 1.5415C13.7475 1.5415 14.0833 1.87729 14.0833 2.2915V2.99984L15.4166 2.99984C16.5212 2.99984 17.4166 3.89527 17.4166 4.99984V7.49984V15.8332C17.4166 16.9377 16.5212 17.8332 15.4166 17.8332H4.58325C3.47868 17.8332 2.58325 16.9377 2.58325 15.8332V7.49984V4.99984C2.58325 3.89527 3.47868 2.99984 4.58325 2.99984L5.91659 2.99984V2.2915C5.91659 1.87729 6.25237 1.5415 6.66659 1.5415ZM6.66659 4.49984H4.58325C4.30711 4.49984 4.08325 4.7237 4.08325 4.99984V6.74984H15.9166V4.99984C15.9166 4.7237 15.6927 4.49984 15.4166 4.49984H13.3333H6.66659ZM15.9166 8.24984H4.08325V15.8332C4.08325 16.1093 4.30711 16.3332 4.58325 16.3332H15.4166C15.6927 16.3332 15.9166 16.1093 15.9166 15.8332V8.24984Z" fill=""/>
                            </svg>
                        </span>
                    </div>
                </div>

                <!-- CATATAN -->
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Catatan</label>
                    <input type="text" name="catatan" id="catatan" value="{{ old('catatan', $opname->catatan) }}"
                           class="h-11 w-full rounded-lg border dark:bg-dark-900 border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800 uppercase"
                           placeholder=""
                           oninput="const s=this.selectionStart;this.value=this.value.toUpperCase();this.setSelectionRange(s,s)">
                </div>

            </div>

            <hr class="border-gray-200 dark:border-gray-800 my-6">

            <!-- DETAIL BARANG -->
            <div class="flex items-center justify-between mb-4">
                <h5 class="text-lg font-semibold text-gray-800 dark:text-white/90">Data Barang</h5>
                <span class="text-xs text-gray-400 dark:text-gray-500">Kosongkan stok fisik jika barang tidak diopname</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full table-fixed">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="w-[90px] px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">KODE</th>
                            <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">NAMA BARANG</th>
                            <th class="w-[200px] px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400 whitespace-nowrap">STOK SISTEM</th>
                            <th class="w-[210px] px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400 whitespace-nowrap">STOK FISIK</th>
                            <th class="w-[120px] px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400 whitespace-nowrap">SELISIH</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800" id="detailContainer">
                        @forelse($barang as $i => $b)
                        @php
                            $s = $stokAll[$b->id] ?? null;
                            $stokBaik = $s['stok_baik'] ?? 0;
                            $stokRusak = $s['stok_rusak'] ?? 0;
                            $stokSales = $s['stok_sales'] ?? 0;
                            $det = $detailIndexed[$b->id] ?? null;
                            $oldFisikBaik = old('stok_fisik_baik.' . $i, $det ? $det->stok_fisik_baik : '');
                            $oldFisikRusak = old('stok_fisik_rusak.' . $i, $det ? $det->stok_fisik_rusak : '');
                            $oldFisikSales = old('stok_fisik_sales.' . $i, $det ? ($det->stok_fisik_sales ?? '') : '');
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50" data-kode="{{ $b->kode_barang }}" data-nama="{{ $b->nama_barang }}">
                            <td class="px-2 py-3 text-center">
                                <span class="inline-flex items-center rounded-full bg-brand-50 px-2 py-0.5 text-sm font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">
                                    {{ $b->kode_barang }}
                                </span>
                                <input type="hidden" name="barang_id[]" value="{{ $b->id }}">
                            </td>
                            <td class="px-2 py-3 text-sm text-gray-800 dark:text-white/90 whitespace-nowrap">{{ $b->nama_barang }}</td>
                            <td class="px-1 py-3 text-center text-sm text-gray-500 dark:text-gray-400">
                                <div class="stok-sistem-label flex justify-center gap-0.5 text-sm" data-baik="{{ $stokBaik }}" data-rusak="{{ $stokRusak }}" data-sales="{{ $stokSales }}">
                                    <span class="whitespace-nowrap text-brand-600 dark:text-brand-400">Baik : <strong class="ml-0.5">{{ $stokBaik }}</strong></span>
                                    <span class="text-gray-300 dark:text-gray-600">|</span>
                                    <span class="whitespace-nowrap text-error-600 dark:text-error-400">Rusak : <strong class="ml-0.5">{{ $stokRusak }}</strong></span>
                                    <span class="text-gray-300 dark:text-gray-600">|</span>
                                    <span class="whitespace-nowrap text-purple-600 dark:text-purple-400">Sales : <strong class="ml-0.5">{{ $stokSales }}</strong></span>
                                </div>
                                <input type="hidden" name="stok_sistem_baik[]" value="{{ $stokBaik }}">
                                <input type="hidden" name="stok_sistem_rusak[]" value="{{ $stokRusak }}">
                                <input type="hidden" name="stok_sistem_sales[]" value="{{ $stokSales }}">
                            </td>
                            <td class="px-1 py-3 text-center">
                                <div class="flex justify-center gap-px">
                                    <div>
                                        <input type="number" name="stok_fisik_baik[]" min="0" value="{{ $oldFisikBaik }}"
                                                placeholder="Baik"
                                                class="stok-fisik-input h-7 w-16 rounded border dark:bg-dark-900 border-gray-300 bg-transparent px-0.5 py-0.5 text-sm text-gray-800 text-center focus:border-brand-300 focus:outline-hidden focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                               data-index="{{ $i }}">
                                    </div>
                                    <div>
                                        <input type="number" name="stok_fisik_rusak[]" min="0" value="{{ $oldFisikRusak }}"
                                               placeholder="Rusak"
                                               class="stok-fisik-input h-7 w-16 rounded border dark:bg-dark-900 border-gray-300 bg-transparent px-0.5 py-0.5 text-sm text-gray-800 text-center focus:border-brand-300 focus:outline-hidden focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                               data-index="{{ $i }}">
                                    </div>
                                    <div>
                                        <input type="number" name="stok_fisik_sales[]" min="0" value="{{ $oldFisikSales }}"
                                               placeholder="Sales"
                                               class="stok-fisik-input h-7 w-16 rounded border dark:bg-dark-900 border-gray-300 bg-transparent px-0.5 py-0.5 text-sm text-gray-800 text-center focus:border-brand-300 focus:outline-hidden focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                               data-index="{{ $i }}">
                                    </div>
                                </div>
                            </td>
                            <td class="px-2 py-3 text-center whitespace-nowrap">
                                <span class="selisih-label text-sm font-medium text-gray-500 dark:text-gray-400" data-index="{{ $i }}">0/0/0</span>
                            </td>
                        </tr>
                        @empty
                        <tr class="empty-row">
                            <td colspan="5" class="px-5 py-12 text-center">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada data barang</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

        <div class="flex items-center justify-between px-5 py-4 border-t border-gray-200 dark:border-gray-800">
            <a href="{{ route('transaksi.stokopname.detail', $opname->id) }}" class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs ring-1 ring-inset ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.8334 10H4.16669M4.16669 10L9.16669 15M4.16669 10L9.16669 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Batal
            </a>
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.8334 8.33333V15.8333C15.8334 16.7538 15.0872 17.5 14.1667 17.5H5.83341C4.91294 17.5 4.16675 16.7538 4.16675 15.8333V4.16667C4.16675 3.24619 4.91294 2.5 5.83341 2.5H11.6667M15.8334 8.33333L11.6667 4.16667M15.8334 8.33333H11.6667V4.16667M7.50008 12.5H10.0001M7.50008 15H12.5001" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Update (Draft)
            </button>
        </div>

    </form>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.stok-fisik-input').forEach(function(input) {
        input.addEventListener('input', function() {
            var idx = this.getAttribute('data-index');
            if (idx === null) return;

            var tr = this.closest('tr');
            var stokLabel = tr.querySelector('.stok-sistem-label');
            var stokBaik = parseInt(stokLabel.getAttribute('data-baik')) || 0;
            var stokRusak = parseInt(stokLabel.getAttribute('data-rusak')) || 0;
            var stokSales = parseInt(stokLabel.getAttribute('data-sales')) || 0;

            var fisikBaik = parseInt(tr.querySelector('input[name="stok_fisik_baik[]"]').value) || 0;
            var fisikRusak = parseInt(tr.querySelector('input[name="stok_fisik_rusak[]"]').value) || 0;
            var fisikSales = parseInt(tr.querySelector('input[name="stok_fisik_sales[]"]').value) || 0;

            var selisihBaik = fisikBaik - stokBaik;
            var selisihRusak = fisikRusak - stokRusak;
            var selisihSales = fisikSales - stokSales;

            var selisihLabel = tr.querySelector('.selisih-label');
            var parts = [];
            if (selisihBaik !== 0) parts.push((selisihBaik > 0 ? '+' : '') + selisihBaik + ' baik');
            if (selisihRusak !== 0) parts.push((selisihRusak > 0 ? '+' : '') + selisihRusak + ' rusak');
            if (selisihSales !== 0) parts.push((selisihSales > 0 ? '+' : '') + selisihSales + ' sales');

            var teks = parts.length > 0 ? parts.join(' / ') : '-';
            selisihLabel.textContent = teks;

            selisihLabel.className = 'selisih-label text-sm font-medium';
            var adaNegatif = selisihBaik < 0 || selisihRusak < 0 || selisihSales < 0;
            var adaPositif = selisihBaik > 0 || selisihRusak > 0 || selisihSales > 0;
            if (adaNegatif) {
                selisihLabel.classList.add('text-error-600', 'dark:text-error-400');
            } else if (adaPositif) {
                selisihLabel.classList.add('text-success-600', 'dark:text-success-400');
            } else {
                selisihLabel.classList.add('text-gray-500', 'dark:text-gray-400');
            }
        });
    });

    document.querySelectorAll('.stok-fisik-input').forEach(function(input) {
        input.dispatchEvent(new Event('input'));
    });

    document.getElementById('cariBarang').addEventListener('input', function() {
        var q = this.value.toLowerCase().trim();
        var rows = document.querySelectorAll('#detailContainer tr[data-kode]');
        var visibleCount = 0;
        rows.forEach(function(row) {
            var kode = row.getAttribute('data-kode').toLowerCase();
            var nama = row.getAttribute('data-nama').toLowerCase();
            var match = q === '' || kode.includes(q) || nama.includes(q);
            row.style.display = match ? '' : 'none';
            if (match) visibleCount++;
        });
        var emptyRow = document.querySelector('#detailContainer tr.empty-row');
        if (emptyRow) {
            emptyRow.style.display = visibleCount === 0 ? '' : 'none';
        }
    });

    function updateCatatanPlaceholder() {
        var tglInput = document.querySelector('input[name="tanggal_opname"]');
        var catatanInput = document.getElementById('catatan');
        if (!tglInput || !catatanInput) return;

        var bulanNama = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        var val = tglInput.value;
        if (val) {
            var parts = val.split('-');
            if (parts.length === 3) {
                var bln = parseInt(parts[1]) - 1;
                var thn = parts[0];
                catatanInput.placeholder = 'CONTOH: OPNAME ' + (bulanNama[bln] || '') + ' ' + thn;
            }
        }
    }

    updateCatatanPlaceholder();
    document.querySelector('input[name="tanggal_opname"]').addEventListener('change', updateCatatanPlaceholder);
    document.querySelector('input[name="tanggal_opname"]').addEventListener('input', updateCatatanPlaceholder);
});
</script>
@endpush
