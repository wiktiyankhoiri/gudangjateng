@extends('layouts.app')

@section('content')

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <form method="get" action="{{ route('laporan.kartustok.index') }}" class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
        <div class="flex flex-col gap-4 sm:flex-row">
            <div class="filter-item" style="flex:4 1 330px; min-width:180px;">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Pilih Barang</label>
                <select name="barang_id" class="h-11 w-full rounded-lg border dark:bg-dark-900 border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800 select2" required>
                    <option value="">Pilih Barang</option>
                    @foreach($barang ?? [] as $b)
                        <option value="{{ $b->id }}" {{ request('barang_id') == $b->id ? 'selected' : '' }}>
                            {{ $b->kode_barang }} - {{ $b->nama_barang }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-item" style="flex:1 1 130px; min-width:100px;">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Dari Tanggal</label>
                <div class="relative">
                    <input type="text" name="tanggal_awal" value="{{ request('tanggal_awal') }}" class="datepicker h-11 w-full appearance-none rounded-lg border dark:bg-dark-900 border-gray-300 bg-transparent bg-none py-2.5 pr-11 pl-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" placeholder="Pilih tanggal">
                    <span class="pointer-events-none absolute top-1/2 right-3 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M6.66659 1.5415C7.0808 1.5415 7.41658 1.87729 7.41658 2.2915V2.99984H12.5833V2.2915C12.5833 1.87729 12.919 1.5415 13.3333 1.5415C13.7475 1.5415 14.0833 1.87729 14.0833 2.2915V2.99984L15.4166 2.99984C16.5212 2.99984 17.4166 3.89527 17.4166 4.99984V7.49984V15.8332C17.4166 16.9377 16.5212 17.8332 15.4166 17.8332H4.58325C3.47868 17.8332 2.58325 16.9377 2.58325 15.8332V7.49984V4.99984C2.58325 3.89527 3.47868 2.99984 4.58325 2.99984L5.91659 2.99984V2.2915C5.91659 1.87729 6.25237 1.5415 6.66659 1.5415ZM6.66659 4.49984H4.58325C4.30711 4.49984 4.08325 4.7237 4.08325 4.99984V6.74984H15.9166V4.99984C15.9166 4.7237 15.6927 4.49984 15.4166 4.49984H13.3333H6.66659ZM15.9166 8.24984H4.08325V15.8332C4.08325 16.1093 4.30711 16.3332 4.58325 16.3332H15.4166C15.6927 16.3332 15.9166 16.1093 15.9166 15.8332V8.24984Z" fill=""/>
                        </svg>
                    </span>
                </div>
            </div>
            <div class="filter-item" style="flex:1 1 130px; min-width:100px;">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Sampai Tanggal</label>
                <div class="relative">
                    <input type="text" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}" class="datepicker h-11 w-full appearance-none rounded-lg border dark:bg-dark-900 border-gray-300 bg-transparent bg-none py-2.5 pr-11 pl-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" placeholder="Pilih tanggal">
                    <span class="pointer-events-none absolute top-1/2 right-3 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M6.66659 1.5415C7.0808 1.5415 7.41658 1.87729 7.41658 2.2915V2.99984H12.5833V2.2915C12.5833 1.87729 12.919 1.5415 13.3333 1.5415C13.7475 1.5415 14.0833 1.87729 14.0833 2.2915V2.99984L15.4166 2.99984C16.5212 2.99984 17.4166 3.89527 17.4166 4.99984V7.49984V15.8332C17.4166 16.9377 16.5212 17.8332 15.4166 17.8332H4.58325C3.47868 17.8332 2.58325 16.9377 2.58325 15.8332V7.49984V4.99984C2.58325 3.89527 3.47868 2.99984 4.58325 2.99984L5.91659 2.99984V2.2915C5.91659 1.87729 6.25237 1.5415 6.66659 1.5415ZM6.66659 4.49984H4.58325C4.30711 4.49984 4.08325 4.7237 4.08325 4.99984V6.74984H15.9166V4.99984C15.9166 4.7237 15.6927 4.49984 15.4166 4.49984H13.3333H6.66659ZM15.9166 8.24984H4.08325V15.8332C4.08325 16.1093 4.30711 16.3332 4.58325 16.3332H15.4166C15.6927 16.3332 15.9166 16.1093 15.9166 15.8332V8.24984Z" fill=""/>
                        </svg>
                    </span>
                </div>
            </div>
            <div class="filter-btn-wrap" style="flex-shrink:0; padding-top:28px;">
                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600 flex-1">
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 10H15M2.5 5H17.5M7.5 15H12.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                        Cari
                    </button>
                    <a href="{{ route('laporan.kartustok.index') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs ring-1 ring-inset ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 2.5C5.85786 2.5 2.5 5.85786 2.5 10C2.5 10.4142 2.83579 10.75 3.25 10.75C3.66421 10.75 4 10.4142 4 10C4 6.68629 6.68629 4 10 4C13.3137 4 16 6.68629 16 10C16 13.3137 13.3137 16 10 16C9.58579 16 9.25 16.3358 9.25 16.75C9.25 17.1642 9.58579 17.5 10 17.5C14.1421 17.5 17.5 14.1421 17.5 10C17.5 5.85786 14.1421 2.5 10 2.5ZM10.5303 6.96967C10.2374 6.67678 9.76256 6.67678 9.46967 6.96967L6.96967 9.46967C6.67678 9.76256 6.67678 10.2374 6.96967 10.5303C7.26256 10.8232 7.73744 10.8232 8.03033 10.5303L9.25 9.31066V13.25C9.25 13.6642 9.58579 14 10 14C10.4142 14 10.75 13.6642 10.75 13.25V9.31066L11.9697 10.5303C12.2626 10.8232 12.7374 10.8232 13.0303 10.5303C13.3232 10.2374 13.3232 9.76256 13.0303 9.46967L10.5303 6.96967Z" fill="currentColor"/></svg>
                        Atur Ulang
                    </a>
                </div>
            </div>
        </div>
    </form>

    @if(isset($barangSelected) && $barangSelected)
    <div class="p-5">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-800">
                <h5 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                    Histori Kartu Stok: <span class="text-brand-500 dark:text-brand-400">{{ $barangSelected->nama_barang }}</span>
                </h5>
                <span class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-1 text-xs font-medium text-brand-500 dark:bg-brand-500/15 dark:text-brand-400">{{ $barangSelected->kode_barang }}</span>
            </div>
            <div class="overflow-x-auto"><table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="px-3 py-2.5 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">TANGGAL</th>
                            <th class="px-3 py-2.5 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">SURAT JALAN</th>
                            <th class="px-3 py-2.5 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">TRANSAKSI</th>
                            <th class="px-3 py-2.5 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">STOK</th>
                            <th class="px-3 py-2.5 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">KETERANGAN</th>
                            <th class="px-3 py-2.5 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">MASUK</th>
                            <th class="px-3 py-2.5 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">KELUAR</th>
                            <th class="px-3 py-2.5 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">SALDO</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @php $saldo = (int)($saldoAwal ?? 0); @endphp
                        @if(!empty($histori))
                            @foreach($histori as $h)
                            @php
                                $saldo += (int)($h['saldo_masuk'] ?? $h['masuk']);
                                $saldo -= (int)($h['saldo_keluar'] ?? $h['keluar']);
                                $transaksi = $h['transaksi'] ?? '';
                                $transaksiClass = 'text-gray-800 dark:text-white/90';

                                if ($transaksi === 'Barang Keluar') {
                                    $transaksiClass = 'text-error-600 dark:text-error-500';
                                } elseif (in_array($transaksi, ['Barang Masuk', 'Retur Toko'], true)) {
                                    $transaksiClass = 'text-brand-500 dark:text-brand-400';
                                } elseif ($transaksi === 'Mutasi Kondisi') {
                                    $transaksiClass = 'text-warning-600 dark:text-warning-400';
                                } elseif ($transaksi === 'Mutasi Kanvas') {
                                    $transaksiClass = 'text-purple-600 dark:text-purple-400';
                                }
                            @endphp
                            <tr>
                                <td class="px-3 py-2.5 text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ isset($h['tanggal']) ? date('d/m/Y', strtotime($h['tanggal'])) : '-' }}</td>
                                <td class="px-3 py-2.5">
                                    <span class="inline-flex items-center rounded-full bg-gray-50 px-2 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400 whitespace-nowrap">{{ $h['surat_jalan'] ?: '-' }}</span>
                                </td>
                                <td class="px-3 py-2.5 text-xs font-semibold {{ $transaksiClass }} whitespace-nowrap">{{ $h['transaksi'] }}</td>
                                <td class="px-3 py-2.5 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ ($h['sumber_stok'] ?? '') === 'Sales' ? 'bg-purple-50 text-purple-600 dark:bg-purple-500/15 dark:text-purple-400' : 'bg-gray-50 text-gray-600 dark:bg-gray-800 dark:text-gray-400' }}">{{ $h['sumber_stok'] ?? '-' }}</span>
                                </td>
                                <td class="px-3 py-2.5 text-xs text-gray-500 dark:text-gray-400">{{ $h['keterangan'] ?: '-' }}</td>
                                <td class="px-3 py-2.5 text-center text-xs font-semibold text-brand-500 dark:text-brand-400 whitespace-nowrap">{{ $h['masuk'] ? number_format($h['masuk']) : '-' }}</td>
                                <td class="px-3 py-2.5 text-center text-xs font-semibold text-error-600 dark:text-error-500 whitespace-nowrap">{{ $h['keluar'] ? number_format($h['keluar']) : '-' }}</td>
                                <td class="px-3 py-2.5 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-full bg-success-50 px-2 py-0.5 text-xs font-medium text-success-600 dark:bg-success-500/15 dark:text-success-500">{{ number_format($saldo) }}</span>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="px-3 py-6 text-center text-xs text-gray-500 dark:text-gray-400">Tidak ada riwayat</td>
                            </tr>
                        @endif
                    </tbody>
                    @if(!empty($histori))
                    <tfoot>
                        <tr class="bg-gray-50 dark:bg-gray-800 font-semibold">
                            <td colspan="5" class="px-3 py-2.5 text-right text-xs text-gray-800 dark:text-white/90">TOTAL</td>
                            <td class="px-3 py-2.5 text-center text-xs text-brand-500 dark:text-brand-400">{{ number_format($totalMasuk ?? 0) }}</td>
                            <td class="px-3 py-2.5 text-center text-xs text-error-600 dark:text-error-500">{{ number_format($totalKeluar ?? 0) }}</td>
                            <td class="px-3 py-2.5 text-center text-xs text-gray-400 dark:text-gray-500">-</td>
                        </tr>
                        <tr class="bg-gray-50 dark:bg-gray-800 font-semibold border-t border-gray-200 dark:border-gray-700">
                            <td colspan="5" class="px-3 py-2.5 text-right text-xs text-gray-800 dark:text-white/90">SALDO AKHIR</td>
                            <td class="px-3 py-2.5 text-center text-xs text-gray-400 dark:text-gray-500"></td>
                            <td class="px-3 py-2.5 text-center text-xs text-gray-400 dark:text-gray-500"></td>
                            <td class="px-3 py-2.5 text-center text-xs font-bold text-success-600 dark:text-success-500">{{ number_format($saldoAkhir ?? 0) }}</td>
                        </tr>
                        <tr class="bg-gray-50/50 dark:bg-gray-800/50">
                            <td colspan="8" class="px-3 py-2.5 text-xs text-gray-500 dark:text-gray-400">
                                <div class="flex items-center justify-end gap-5">
                                    <span class="inline-flex items-center gap-1.5">
                                        <span class="inline-block w-2.5 h-2.5 rounded-full bg-brand-500"></span>
                                        Stok Baik: <strong class="text-brand-600 dark:text-brand-400">{{ number_format($stokBaik ?? 0) }}</strong>
                                    </span>
                                    <span class="inline-flex items-center gap-1.5">
                                        <span class="inline-block w-2.5 h-2.5 rounded-full bg-error-500"></span>
                                        Stok Rusak: <strong class="text-error-600 dark:text-error-500">{{ number_format($stokRusak ?? 0) }}</strong>
                                    </span>
                                    <span class="inline-flex items-center gap-1.5">
                                        <span class="inline-block w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                                        Stok Sales: <strong class="text-purple-600 dark:text-purple-400">{{ number_format($stokSales ?? 0) }}</strong>
                                    </span>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>

            @if(isset($pagination) && $pagination['totalPages'] > 1)
            @php
                $buildUrl = function($page) {
                    $params = array_filter([
                        'barang_id' => request('barang_id'),
                        'tanggal_awal' => request('tanggal_awal'),
                        'tanggal_akhir' => request('tanggal_akhir'),
                        'page' => $page,
                    ]);
                    return route('laporan.kartustok.index') . '?' . http_build_query($params);
                };
                $current = $pagination['page'];
                $total = $pagination['totalPages'];
                $start = max(1, $current - 2);
                $end = min($total, $current + 2);
                if ($end - $start < 4) {
                    if ($start == 1) $end = min($total, $start + 4);
                    else $start = max(1, $end - 4);
                }
            @endphp
            <div class="border-t border-gray-200 dark:border-gray-800">
                <div class="flex items-center justify-center gap-3 px-6 py-4">
                    @if($current > 1)
                    <a href="{{ $buildUrl($current - 1) }}" class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-2 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200 sm:px-3.5 sm:py-2.5">
                        <svg class="fill-current shrink-0" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M2.58203 9.99868C2.58174 10.1909 2.6549 10.3833 2.80152 10.53L7.79818 15.5301C8.09097 15.8231 8.56584 15.8233 8.85883 15.5305C9.15183 15.2377 9.152 14.7629 8.85921 14.4699L5.13911 10.7472L16.6665 10.7472C17.0807 10.7472 17.4165 10.4114 17.4165 9.99715C17.4165 9.58294 17.0807 9.24715 16.6665 9.24715L5.14456 9.24715L8.85919 5.53016C9.15199 5.23717 9.15184 4.7623 8.85885 4.4695C8.56587 4.1767 8.09099 4.17685 7.79819 4.46984L2.84069 9.43049C2.68224 9.568 2.58203 9.77087 2.58203 9.99715C2.58203 9.99766 2.58203 9.99817 2.58203 9.99868Z" fill=""/>
                        </svg>
                        <span class="hidden sm:inline">Sebelumnya</span>
                    </a>
                    @endif
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-400 sm:hidden">{{ $current }}/{{ $total }}</span>
                    <ul class="hidden items-center gap-0.5 sm:flex">
                        @if($start > 1)
                        <li><a href="{{ $buildUrl(1) }}" class="flex h-10 w-10 items-center justify-center rounded-lg text-sm font-medium text-gray-700 hover:bg-brand-600 hover:text-white dark:text-gray-400 dark:hover:text-white">1</a></li>
                        @if($start > 2)
                        <li><span class="flex h-10 w-10 items-center justify-center text-sm font-medium text-gray-500 dark:text-gray-400">...</span></li>
                        @endif
                        @endif
                        @for($i = $start; $i <= $end; $i++)
                            @if($i == $current)
                            <li><span class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand-500 text-sm font-medium text-white dark:bg-brand-500">{{ $i }}</span></li>
                            @else
                            <li><a href="{{ $buildUrl($i) }}" class="flex h-10 w-10 items-center justify-center rounded-lg text-sm font-medium text-gray-700 hover:bg-brand-600 hover:text-white dark:text-gray-400 dark:hover:text-white">{{ $i }}</a></li>
                            @endif
                        @endfor
                        @if($end < $total)
                        @if($end < $total - 1)
                        <li><span class="flex h-10 w-10 items-center justify-center text-sm font-medium text-gray-500 dark:text-gray-400">...</span></li>
                        @endif
                        <li><a href="{{ $buildUrl($total) }}" class="flex h-10 w-10 items-center justify-center rounded-lg text-sm font-medium text-gray-700 hover:bg-brand-600 hover:text-white dark:text-gray-400 dark:hover:text-white">{{ $total }}</a></li>
                        @endif
                    </ul>
                    @if($current < $total)
                    <a href="{{ $buildUrl($current + 1) }}" class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-2 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200 sm:px-3.5 sm:py-2.5">
                        <span class="hidden sm:inline">Selanjutnya</span>
                        <svg class="fill-current shrink-0" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M17.4165 9.9986C17.4168 10.1909 17.3437 10.3832 17.197 10.53L12.2004 15.5301C11.9076 15.8231 11.4327 15.8233 11.1397 15.5305C10.8467 15.2377 10.8465 14.7629 11.1393 14.4699L14.8594 10.7472L3.33203 10.7472C2.91782 10.7472 2.58203 10.4114 2.58203 9.99715C2.58203 9.58294 2.91782 9.24715 3.33203 9.24715L14.854 9.24715L11.1393 5.53016C10.8465 5.23717 10.8467 4.7623 11.1397 4.4695C11.4327 4.1767 11.9075 4.17685 12.2003 4.46984L17.1578 9.43049C17.3163 9.568 17.4165 9.77087 17.4165 9.99715C17.4165 9.99763 17.4165 9.99812 17.4165 9.9986Z" fill=""/>
                        </svg>
                    </a>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.select2').forEach(function(el) {
        if (typeof TomSelect !== 'undefined') {
            new TomSelect(el, { searchField: ['text'] });
        }
    });
});
</script>
@endpush
@endsection
