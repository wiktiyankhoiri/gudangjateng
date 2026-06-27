@php $totalStokAll = max($totalStokBaik + $totalStokRusak, 1); @endphp

<!-- ====== ROW 1: 3 KPI Cards ====== -->
<div class="grid grid-cols-1 gap-4 md:grid-cols-3 md:gap-6">

    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-800">
            <svg class="fill-gray-800 dark:fill-white/90" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12.378 1.602a.75.75 0 00-.756 0L3 6.632l9 5.25 9-5.25-8.622-5.03zM21.75 7.93l-9 5.25v9l8.628-5.032a.75.75 0 00.372-.648V7.93zM11.25 22.18v-9l-9-5.25v8.57a.75.75 0 00.372.648l8.628 5.032z"/>
            </svg>
        </div>
        <div class="mt-5">
            <div class="flex items-stretch gap-3">
                <div class="flex-1 text-center min-w-0">
                    <span class="text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">Stok Baik</span>
                    <h4 class="mt-2 text-title-sm font-bold text-success-600 dark:text-success-500 whitespace-nowrap">{{ number_format($totalStokBaik) }}</h4>
                </div>
                <div class="w-px bg-gray-200 dark:bg-gray-700"></div>
                <div class="flex-1 text-center min-w-0">
                    <span class="text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">Stok Rusak</span>
                    <h4 class="mt-2 text-title-sm font-bold text-error-600 dark:text-error-500 whitespace-nowrap">{{ number_format($totalStokRusak) }}</h4>
                </div>
                <div class="w-px bg-gray-200 dark:bg-gray-700"></div>
                <div class="flex-1 text-center min-w-0">
                    <span class="text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">Stok Sales</span>
                    <h4 class="mt-2 text-title-sm font-bold text-purple-600 dark:text-purple-400 whitespace-nowrap">{{ number_format($totalStokSales) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-800">
            <svg class="fill-gray-800 dark:fill-white/90" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C12.4142 2 12.75 2.33579 12.75 2.75V13.6893L16.9697 9.46967C17.2626 9.17678 17.7374 9.17678 18.0303 9.46967C18.3232 9.76256 18.3232 10.2374 18.0303 10.5303L12.5303 16.0303C12.2374 16.3232 11.7626 16.3232 11.4697 16.0303L5.96967 10.5303C5.67678 10.2374 5.67678 9.76256 5.96967 9.46967C6.26256 9.17678 6.73744 9.17678 7.03033 9.46967L11.25 13.6893V2.75C11.25 2.33579 11.5858 2 12 2ZM4.25 19C4.25 18.5858 4.58579 18.25 5 18.25H19C19.4142 18.25 19.75 18.5858 19.75 19C19.75 19.4142 19.4142 19.75 19 19.75H5C4.58579 19.75 4.25 19.4142 4.25 19Z"/>
            </svg>
        </div>
        <div class="mt-5 flex items-end justify-between">
            <div>
                <span class="text-sm text-gray-500 dark:text-gray-400">Barang Masuk</span>
                <h4 class="mt-2 text-title-sm font-bold text-success-600 dark:text-success-500">{{ number_format($totalBarangMasuk) }}</h4>
            </div>
            <span class="flex items-center gap-1 rounded-full bg-success-50 py-0.5 pl-2 pr-2.5 text-sm font-medium text-success-600 dark:bg-success-500/15 dark:text-success-500">
                <svg class="fill-current" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M5.56462 1.62393C5.70193 1.47072 5.90135 1.37432 6.12329 1.37432C6.1236 1.37432 6.12391 1.37432 6.12422 1.37432C6.31631 1.37415 6.50845 1.44731 6.65505 1.59381L9.65514 4.5918C9.94814 4.88459 9.94831 5.35947 9.65552 5.65246C9.36273 5.94546 8.88785 5.94562 8.59486 5.65283L6.87329 3.93247L6.87329 10.125C6.87329 10.5392 6.53751 10.875 6.12329 10.875C5.70908 10.875 5.37329 10.5392 5.37329 10.125L5.37329 3.93578L3.65516 5.65282C3.36218 5.94562 2.8873 5.94547 2.5945 5.65248C2.3017 5.35949 2.30185 4.88462 2.59484 4.59182L5.56462 1.62393Z" fill=""/>
                </svg>
                transaksi
            </span>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-800">
            <svg class="fill-gray-800 dark:fill-white/90" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 22C12.4142 22 12.75 21.6642 12.75 21.25V10.3107L16.9697 14.5303C17.2626 14.8232 17.7374 14.8232 18.0303 14.5303C18.3232 14.2374 18.3232 13.7626 18.0303 13.4697L12.5303 7.96967C12.2374 7.67678 11.7626 7.67678 11.4697 7.96967L5.96967 13.4697C5.67678 13.7626 5.67678 14.2374 5.96967 14.5303C6.26256 14.8232 6.73744 14.8232 7.03033 14.5303L11.25 10.3107V21.25C11.25 21.6642 11.5858 22 12 22ZM4.25 5C4.25 4.58579 4.58579 4.25 5 4.25H19C19.4142 4.25 19.75 4.58579 19.75 5C19.75 5.41421 19.4142 5.75 19 5.75H5C4.58579 5.75 4.25 5.41421 4.25 5Z"/>
            </svg>
        </div>
        <div class="mt-5 flex items-end justify-between">
            <div>
                <span class="text-sm text-gray-500 dark:text-gray-400">Barang Keluar</span>
                <h4 class="mt-2 text-title-sm font-bold text-error-600 dark:text-error-500">{{ number_format($totalBarangKeluar) }}</h4>
            </div>
            <span class="flex items-center gap-1 rounded-full bg-error-50 py-0.5 pl-2 pr-2.5 text-sm font-medium text-error-600 dark:bg-error-500/15 dark:text-error-500">
                <svg class="fill-current" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M6.43538 10.3761C6.29807 10.5293 6.09865 10.6257 5.87671 10.6257C5.8764 10.6257 5.87609 10.6257 5.87578 10.6257C5.68369 10.6258 5.49155 10.5527 5.34495 10.4062L2.34486 7.4082C2.05186 7.11541 2.05169 6.64053 2.34448 6.34754C2.63727 6.05454 3.11215 6.05438 3.40514 6.34717L5.12671 8.06753L5.12671 1.875C5.12671 1.46079 5.46249 1.125 5.87671 1.125C6.29092 1.125 6.62671 1.46079 6.62671 1.875L6.62671 8.06422L8.34484 6.34718C8.63782 6.05438 9.1127 6.05453 9.4055 6.34752C9.6983 6.64051 9.69815 7.11538 9.40516 7.40818L6.43538 10.3761Z" fill=""/>
                </svg>
                transaksi
            </span>
        </div>
    </div>

</div>

<!-- ====== ROW 2: Barang Masuk Terbaru | Barang Keluar Terbaru ====== -->
<div class="mt-6 grid grid-cols-12 gap-4 md:gap-6 md:mt-8">

    <div class="col-span-12 lg:col-span-6">
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 sm:px-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Barang Masuk Terbaru</h3>
                    <a href="{{ route('laporan.barangmasuk.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">Lihat Semua</a>
                </div>
            </div>
            <div class="w-full overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-y border-gray-100 dark:border-gray-800">
                            <th class="py-3 px-5 text-left"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">No. Surat</p></th>
                            <th class="py-3 px-5 text-left"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Pabrik/Toko</p></th>
                            <th class="py-3 px-5 text-left"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Tipe</p></th>
                            <th class="py-3 px-5 text-right"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Item</p></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @if(!empty($adminBarangMasukTerbaru))
                            @foreach(array_slice($adminBarangMasukTerbaru, 0, 5) as $bm)
                                <tr>
                                    <td class="py-3 px-5"><a href="{{ route('transaksi.barangmasuk.detail', $bm['id']) }}" class="text-brand-500 hover:text-brand-600 dark:text-brand-400 font-medium text-theme-sm">{{ esc($bm['no_surat']) }}</a></td>
                                    <td class="py-3 px-5"><p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $bm['tipe'] === 'pabrik' ? esc($bm['nama_pabrik'] ?? '-') : esc($bm['nama_toko'] ?? '-') }}</p></td>
                                    <td class="py-3 px-5"><p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ esc(ucfirst($bm['tipe'] ?? 'Transaksi')) }}</p></td>
                                    <td class="py-3 px-5 text-right"><span class="inline-flex items-center rounded-full bg-success-50 px-2 py-0.5 text-theme-xs font-medium text-success-600 dark:bg-success-500/15 dark:text-success-500">{{ number_format($bm['total_item']) }}</span></td>
                                </tr>
                            @endforeach
                        @else
                            <tr><td colspan="4" class="py-8 text-center text-gray-500 dark:text-gray-400">Belum ada barang masuk</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-span-12 lg:col-span-6">
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 sm:px-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Barang Keluar Terbaru</h3>
                    <a href="{{ route('laporan.barangkeluar.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">Lihat Semua</a>
                </div>
            </div>
            <div class="w-full overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-y border-gray-100 dark:border-gray-800">
                            <th class="py-3 px-5 text-left"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">No. Surat</p></th>
                            <th class="py-3 px-5 text-left"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Toko</p></th>
                            <th class="py-3 px-5 text-left"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Sales</p></th>
                            <th class="py-3 px-5 text-right"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Item</p></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @if(!empty($barangKeluarTerbaru))
                            @foreach(array_slice($barangKeluarTerbaru, 0, 5) as $bk)
                                <tr>
                                    <td class="py-3 px-5"><a href="{{ route('transaksi.barangkeluar.detail', $bk['id']) }}" class="text-brand-500 hover:text-brand-600 dark:text-brand-400 font-medium text-theme-sm">{{ esc($bk['no_surat']) }}</a></td>
                                    <td class="py-3 px-5"><p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ esc($bk['nama_toko']) }}</p></td>
                                    <td class="py-3 px-5"><p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ esc($bk['nama_sales'] ?? '-') }}</p></td>
                                    <td class="py-3 px-5 text-right"><span class="inline-flex items-center rounded-full bg-error-50 px-2 py-0.5 text-theme-xs font-medium text-error-600 dark:bg-error-500/15 dark:text-error-500">{{ number_format($bk['total_item']) }}</span></td>
                                </tr>
                            @endforeach
                        @else
                            <tr><td colspan="4" class="py-8 text-center text-gray-500 dark:text-gray-400">Belum ada barang keluar</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- ====== ROW 3: Mutasi Terbaru + Retur Terbaru ====== -->
<div class="mt-6 md:mt-8 grid grid-cols-12 gap-4 md:gap-6">

    <div class="col-span-12 lg:col-span-6">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-800">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Mutasi Terbaru</h3>
                <a href="{{ route('transaksi.mutasi.index') }}" class="text-theme-sm font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400">Lihat Semua</a>
            </div>
            <div class="w-full overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-y border-gray-100 dark:border-gray-800">
                            <th class="py-3 px-5 text-left"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">No. Mutasi</p></th>
                            <th class="py-3 px-5 text-left"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Tipe</p></th>
                            <th class="py-3 px-5 text-right"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Item</p></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @if(!empty($adminMutasiTerbaruList))
                            @foreach(array_slice($adminMutasiTerbaruList, 0, 5) as $row)
                                <tr>
                                    <td class="py-3 px-5"><a href="{{ route('transaksi.mutasi.detail', $row['mutasi_id'] ?? 0) }}" class="text-brand-500 hover:text-brand-600 dark:text-brand-400 font-medium text-theme-sm">{{ esc($row['no_mutasi'] ?? '-') }}</a></td>
                                    <td class="py-3 px-5"><p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ esc($row['tipe'] ?? '') }}</p></td>
                                    <td class="py-3 px-5 text-right"><span class="inline-flex items-center rounded-full bg-brand-50 px-2 py-0.5 text-theme-xs font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-500">{{ number_format($row['total_item']) }}</span></td>
                                </tr>
                            @endforeach
                        @else
                            <tr><td colspan="3" class="py-8 text-center text-gray-500 dark:text-gray-400">Belum ada mutasi</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-span-12 lg:col-span-6">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-800">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Retur Terbaru</h3>
                <a href="{{ route('laporan.barangmasuk.index', ['tipe' => 'retur']) }}" class="text-theme-sm font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400">Lihat Semua</a>
            </div>
            <div class="p-5">
                <div class="flex flex-col gap-4">
                    @if(!empty($adminReturTerbaru))
                        @foreach(array_slice($adminReturTerbaru, 0, 5) as $row)
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3 dark:border-gray-800 last:border-b-0 last:pb-0">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="flex size-10 items-center justify-center rounded-full bg-warning-50 dark:bg-warning-500/15">
                                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 3V17M10 3L5 8M10 3L15 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-warning-500 dark:text-warning-500"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ route('transaksi.barangmasuk.detail', $row['id']) }}" class="text-sm font-semibold text-brand-500 hover:text-brand-600 dark:text-brand-400 truncate">{{ esc($row['no_surat'] ?? '-') }}</a>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ esc($row['nama_toko'] ?? '-') }}{!! !empty($row['keterangan']) ? ' &bull; ' . esc($row['keterangan']) : '' !!}</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center rounded-full bg-warning-50 px-2.5 py-1 text-xs font-medium text-warning-600 dark:bg-warning-500/15 dark:text-warning-500">
                                {{ number_format($row['total_item'] ?? 0) }} item
                            </span>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-6"><p class="text-sm text-gray-500 dark:text-gray-400">Belum ada retur</p></div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ====== ROW 4: Stok Opname + Ringkasan Sistem ====== -->
<div class="mt-6 md:mt-8 grid grid-cols-12 gap-4 md:gap-6">
    <div class="col-span-12 lg:col-span-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Stok Opname ({{ \Carbon\Carbon::now()->translatedFormat('F Y') }})</h3>
                <a href="{{ route('transaksi.stokopname.index') }}" class="text-theme-xs font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400">Lihat Semua</a>
            </div>
            <div class="mt-3 flex">
                <div class="flex-1 text-center border-r border-gray-200 dark:border-gray-700 last:border-r-0">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Draft</p>
                    <p class="mt-1 text-sm font-semibold text-warning-600 dark:text-warning-500">{{ number_format($stokOpnameCount['draft']) }}</p>
                </div>
                <div class="flex-1 text-center border-r border-gray-200 dark:border-gray-700 last:border-r-0">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Selesai</p>
                    <p class="mt-1 text-sm font-semibold text-brand-600 dark:text-brand-400">{{ number_format($stokOpnameCount['selesai']) }}</p>
                </div>
                <div class="flex-1 text-center border-r-0">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Diterapkan</p>
                    <p class="mt-1 text-sm font-semibold text-success-600 dark:text-success-500">{{ number_format($stokOpnameCount['diterapkan']) }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-span-12 lg:col-span-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Ringkasan Sistem</h3>
            <div class="mt-3 grid grid-cols-4">
                <div class="text-center border-r border-gray-200 dark:border-gray-700 last:border-r-0">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Barang</p>
                    <p class="mt-1 text-sm font-semibold text-gray-800 dark:text-white/90">{{ number_format($totalBarang) }}</p>
                </div>
                <div class="text-center border-r border-gray-200 dark:border-gray-700 last:border-r-0">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Pengguna</p>
                    <p class="mt-1 text-sm font-semibold text-gray-800 dark:text-white/90">{{ number_format($userAktif) }}</p>
                </div>
                <div class="text-center border-r border-gray-200 dark:border-gray-700 last:border-r-0">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Log Audit</p>
                    <p class="mt-1 text-sm font-semibold text-gray-800 dark:text-white/90">{{ number_format($totalAuditLog) }}</p>
                </div>
                <div class="text-center border-r-0">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Cadangan</p>
                    <p class="mt-1 text-sm font-semibold text-gray-800 dark:text-white/90">{{ number_format($totalBackup) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
