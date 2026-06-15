@extends('layouts.app')

@section('content')

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <form method="get" class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
        <div class="flex flex-col gap-4 sm:flex-row">
            <div class="filter-item" style="flex:1 1 200px; min-width:140px;">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Pencarian</label>
                <div class="relative">
                    <span class="absolute top-1/2 left-0 -translate-y-1/2 border-r border-gray-200 px-3.5 py-3 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M3.04175 9.37363C3.04175 5.87693 5.87711 3.04199 9.37508 3.04199C12.8731 3.04199 15.7084 5.87693 15.7084 9.37363C15.7084 12.8703 12.8731 15.7053 9.37508 15.7053C5.87711 15.7053 3.04175 12.8703 3.04175 9.37363ZM9.37508 1.54199C5.04902 1.54199 1.54175 5.04817 1.54175 9.37363C1.54175 13.6991 5.04902 17.2053 9.37508 17.2053C11.2674 17.2053 13.003 16.5344 14.357 15.4176L17.177 18.238C17.4699 18.5309 17.9448 18.5309 18.2377 18.238C18.5306 17.9451 18.5306 17.4703 18.2377 17.1774L15.418 14.3573C16.5365 13.0033 17.2084 11.2669 17.2084 9.37363C17.2084 5.04817 13.7011 1.54199 9.37508 1.54199Z" fill="currentColor"/>
                        </svg>
                    </span>
                    <input type="text" name="keyword" value="{{ $keyword ?? '' }}" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pl-[62px] text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" placeholder="No Surat...">
                </div>
            </div>
            <div class="filter-item" style="flex:1 1 160px; min-width:120px;">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Sales</label>
                <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                    <select name="sales_id" class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                        x-bind:class="isOptionSelected && 'text-gray-800 dark:text-white/90'"
                        @change="isOptionSelected = true">
                        <option value="" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">Semua Sales</option>
                        @foreach($salesList ?? [] as $s)
                        <option value="{{ $s->id }}" {{ ($salesId ?? '') == $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
                        @endforeach
                    </select>
                    <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                        <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                </div>
            </div>
            <div class="filter-item" style="flex:1 1 160px; min-width:120px;">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tgl Awal</label>
                <div class="relative">
                    <input type="text" name="tanggal_awal" value="{{ $tanggalAwal ?? '' }}" class="datepicker h-11 w-full appearance-none rounded-lg border dark:bg-dark-900 border-gray-300 bg-transparent bg-none py-2.5 pr-11 pl-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" placeholder="Pilih tanggal">
                    <span class="pointer-events-none absolute top-1/2 right-3 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M6.66659 1.5415C7.0808 1.5415 7.41658 1.87729 7.41658 2.2915V2.99984H12.5833V2.2915C12.5833 1.87729 12.919 1.5415 13.3333 1.5415C13.7475 1.5415 14.0833 1.87729 14.0833 2.2915V2.99984L15.4166 2.99984C16.5212 2.99984 17.4166 3.89527 17.4166 4.99984V7.49984V15.8332C17.4166 16.9377 16.5212 17.8332 15.4166 17.8332H4.58325C3.47868 17.8332 2.58325 16.9377 2.58325 15.8332V7.49984V4.99984C2.58325 3.89527 3.47868 2.99984 4.58325 2.99984L5.91659 2.99984V2.2915C5.91659 1.87729 6.25237 1.5415 6.66659 1.5415ZM6.66659 4.49984H4.58325C4.30711 4.49984 4.08325 4.7237 4.08325 4.99984V6.74984H15.9166V4.99984C15.9166 4.7237 15.6927 4.49984 15.4166 4.49984H13.3333H6.66659ZM15.9166 8.24984H4.08325V15.8332C4.08325 16.1093 4.30711 16.3332 4.58325 16.3332H15.4166C15.6927 16.3332 15.9166 16.1093 15.9166 15.8332V8.24984Z" fill=""/>
                        </svg>
                    </span>
                </div>
            </div>
            <div class="filter-item" style="flex:1 1 160px; min-width:120px;">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tgl Akhir</label>
                <div class="relative">
                    <input type="text" name="tanggal_akhir" value="{{ $tanggalAkhir ?? '' }}" class="datepicker h-11 w-full appearance-none rounded-lg border dark:bg-dark-900 border-gray-300 bg-transparent bg-none py-2.5 pr-11 pl-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" placeholder="Pilih tanggal">
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
                    <a href="{{ route('laporan.barangkeluar.index') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs ring-1 ring-inset ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 2.5C5.85786 2.5 2.5 5.85786 2.5 10C2.5 10.4142 2.83579 10.75 3.25 10.75C3.66421 10.75 4 10.4142 4 10C4 6.68629 6.68629 4 10 4C13.3137 4 16 6.68629 16 10C16 13.3137 13.3137 16 10 16C9.58579 16 9.25 16.3358 9.25 16.75C9.25 17.1642 9.58579 17.5 10 17.5C14.1421 17.5 17.5 14.1421 17.5 10C17.5 5.85786 14.1421 2.5 10 2.5ZM10.5303 6.96967C10.2374 6.67678 9.76256 6.67678 9.46967 6.96967L6.96967 9.46967C6.67678 9.76256 6.67678 10.2374 6.96967 10.5303C7.26256 10.8232 7.73744 10.8232 8.03033 10.5303L9.25 9.31066V13.25C9.25 13.6642 9.58579 14 10 14C10.4142 14 10.75 13.6642 10.75 13.25V9.31066L11.9697 10.5303C12.2626 10.8232 12.7374 10.8232 13.0303 10.5303C13.3232 10.2374 13.3232 9.76256 13.0303 9.46967L10.5303 6.96967Z" fill="currentColor"/></svg>
                        Atur Ulang
                    </a>
                </div>
            </div>
        </div>
    </form>

    <div class="overflow-x-auto p-5"><table class="table-sticky min-w-full">
            <thead>
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400 w-[60px]">NO</th>
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">NO SURAT</th>
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">TANGGAL</th>
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">TOKO</th>
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">SALES</th>
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">ITEM</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @if(!empty($data))
                    @foreach($data as $i => $d)
                    <tr>
                        <td class="px-5 py-4 text-sm text-center text-gray-800 dark:text-white/80">{{ ($data->currentPage() - 1) * $data->perPage() + $i + 1 }}</td>
                        <td class="px-5 py-4 text-center">
                            <a href="{{ route('transaksi.barangkeluar.detail', $d->id) }}" class="text-sm font-semibold text-brand-500 hover:text-brand-600 dark:text-brand-400">{{ $d->no_surat ?? '-' }}</a>
                        </td>
                        <td class="px-5 py-4 text-sm text-center text-gray-500 dark:text-gray-400">{{ $d->tanggal ? $d->tanggal->format('d/m/Y') : '-' }}</td>
                        <td class="px-5 py-4 text-sm text-center text-gray-800 dark:text-white/80">{{ $d->nama_toko ?? '-' }}</td>
                        <td class="px-5 py-4 text-sm text-center text-gray-500 dark:text-gray-400">{{ $d->nama_sales ?? '-' }}</td>
                        <td class="px-5 py-4 text-center">
                            <span class="inline-flex items-center rounded-full bg-warning-50 px-2.5 py-1 text-xs font-medium text-warning-600 dark:bg-warning-500/15 dark:text-warning-400">{{ $d->details->count() }}</span>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Data tidak ditemukan</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    @if($data->hasPages())
    <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-800">
        {{ $data->appends(request()->only(['keyword', 'tanggal_awal', 'tanggal_akhir', 'sales_id']))->links('vendor.pagination.tailadmin') }}
    </div>
    @endif
</div>

@endsection


