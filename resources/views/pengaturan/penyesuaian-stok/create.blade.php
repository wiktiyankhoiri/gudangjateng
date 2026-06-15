@extends('layouts.app')

@section('content')

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

    <form data-protect-submit="true" data-confirm-message="Pastikan data sudah sesuai. Lanjutkan simpan?" data-confirm-ok="Ya, Simpan" method="post"
          action="{{ route('transaksi.penyesuaianstok.store') }}">

        @csrf

        <div class="p-5 sm:p-6">

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Barang</label>
                    <select name="barang_id" id="barang_id"
                        class="h-11 w-full rounded-lg border dark:bg-dark-900 border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800 select2" required>
                        <option value="">-- Pilih Barang --</option>
                        @foreach($barang as $b)
                            @php $sInfo = $stokAll[$b->id] ?? null; @endphp
                            <option value="{{ $b->id }}"
                                data-stok-baik="{{ $sInfo->stok_baik ?? 0 }}"
                                data-stok-rusak="{{ $sInfo->stok_rusak ?? 0 }}">
                                {{ $b->kode_barang }} - {{ $b->nama_barang }}
                            </option>
                        @endforeach
                    </select>
                    <p id="stokInfoText" class="mt-1.5 text-sm text-gray-500 dark:text-gray-400 hidden">
                        Stok saat ini: <strong id="stokBaikLabel">0</strong> baik, <strong id="stokRusakLabel">0</strong> rusak
                    </p>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tanggal</label>
                    <div class="relative">
                        <input type="text" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required
                            class="datepicker h-11 w-full rounded-lg border dark:bg-dark-900 border-gray-300 bg-transparent py-2.5 pr-11 pl-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                            placeholder="Pilih tanggal">
                        <span class="pointer-events-none absolute top-1/2 right-3 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                            <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M6.66659 1.5415C7.0808 1.5415 7.41658 1.87729 7.41658 2.2915V2.99984H12.5833V2.2915C12.5833 1.87729 12.919 1.5415 13.3333 1.5415C13.7475 1.5415 14.0833 1.87729 14.0833 2.2915V2.99984L15.4166 2.99984C16.5212 2.99984 17.4166 3.89527 17.4166 4.99984V7.49984V15.8332C17.4166 16.9377 16.5212 17.8332 15.4166 17.8332H4.58325C3.47868 17.8332 2.58325 16.9377 2.58325 15.8332V7.49984V4.99984C2.58325 3.89527 3.47868 2.99984 4.58325 2.99984L5.91659 2.99984V2.2915C5.91659 1.87729 6.25237 1.5415 6.66659 1.5415ZM6.66659 4.49984H4.58325C4.30711 4.49984 4.08325 4.7237 4.08325 4.99984V6.74984H15.9166V4.99984C15.9166 4.7237 15.6927 4.49984 15.4166 4.49984H13.3333H6.66659ZM15.9166 8.24984H4.08325V15.8332C4.08325 16.1093 4.30711 16.3332 4.58325 16.3332H15.4166C15.6927 16.3332 15.9166 16.1093 15.9166 15.8332V8.24984Z" fill=""/>
                            </svg>
                        </span>
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Stok Baik Final</label>
                    <input type="number" name="stok_baik_sesudah" id="stokBaikFinal" value="0" min="0" required
                        class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-3 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                    <p id="selisihBaik" class="mt-1 text-xs text-gray-500 dark:text-gray-400">Selisih: 0</p>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Stok Rusak Final</label>
                    <input type="number" name="stok_rusak_sesudah" id="stokRusakFinal" value="0" min="0" required
                        class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-3 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                    <p id="selisihRusak" class="mt-1 text-xs text-gray-500 dark:text-gray-400">Selisih: 0</p>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Alasan Penyesuaian</label>
                    <textarea name="alasan" rows="3" placeholder="Contoh: Koreksi qty barang masuk, Selisih stok opname, Barang hilang, dll" required
                        class="w-full rounded-lg border dark:bg-dark-900 border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800 uppercase"
                        oninput="const s=this.selectionStart;this.value=this.value.toUpperCase();this.setSelectionRange(s,s)"></textarea>
                </div>
            </div>

        </div>

        <div class="flex items-center justify-between px-5 py-4">
            <a href="{{ route('transaksi.penyesuaianstok.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs ring-1 ring-inset ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.8334 10H4.16669M4.16669 10L9.16669 15M4.16669 10L9.16669 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Kembali
            </a>
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.8334 8.33333V15.8333C15.8334 16.7538 15.0872 17.5 14.1667 17.5H5.83341C4.91294 17.5 4.16675 16.7538 4.16675 15.8333V4.16667C4.16675 3.24619 4.91294 2.5 5.83341 2.5H11.6667M15.8334 8.33333L11.6667 4.16667M15.8334 8.33333H11.6667V4.16667M7.50008 12.5H10.0001M7.50008 15H12.5001" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Simpan
            </button>
        </div>    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var stokIndex = @json($stokAll ?? []);
    var sel = document.getElementById("barang_id");
    if (typeof TomSelect !== 'undefined') {
        var ts = new TomSelect(sel, { searchField: ["text"] });
    }

    function upd() {
        var ts = sel.tomselect;
        var id = ts ? ts.getValue() : sel.value;
        var stok = stokIndex[id] || {};
        var sb = parseInt(stok.stok_baik) || 0;
        var sr = parseInt(stok.stok_rusak) || 0;
        if (id) {
            document.getElementById('stokBaikLabel').textContent = sb;
            document.getElementById('stokRusakLabel').textContent = sr;
            document.getElementById('stokInfoText').classList.remove('hidden');
            ['stokBaikFinal','stokRusakFinal'].forEach(function(fid, i) {
                var el = document.getElementById(fid);
                if (el.value == '0' || el.dataset.auto) {
                    el.value = i === 0 ? sb : sr;
                    el.dataset.auto = 'true';
                }
            });
        }
        calc();
    }

    function calc() {
        var ts = sel.tomselect;
        var id = ts ? ts.getValue() : sel.value;
        var stok = stokIndex[id] || {};
        var sb = parseInt(stok.stok_baik) || 0;
        var sr = parseInt(stok.stok_rusak) || 0;
        var bf = parseInt(document.getElementById('stokBaikFinal').value) || 0;
        var rf = parseInt(document.getElementById('stokRusakFinal').value) || 0;
        var db = bf - sb, dr = rf - sr;
        ['selisihBaik','selisihRusak'].forEach(function(id, i) {
            var el = document.getElementById(id);
            var v = i === 0 ? db : dr;
            el.textContent = 'Selisih: ' + (v > 0 ? '+' : '') + v;
            el.className = 'mt-1 text-xs ' + (v !== 0 ? 'text-warning-500 font-medium' : 'text-gray-500 dark:text-gray-400');
        });
    }

    if (typeof TomSelect !== 'undefined') {
        sel.tomselect && sel.tomselect.on('change', upd);
    } else {
        sel.addEventListener('change', upd);
    }
    document.getElementById('stokBaikFinal').addEventListener('input', calc);
    document.getElementById('stokRusakFinal').addEventListener('input', calc);
});
</script>
@endpush
