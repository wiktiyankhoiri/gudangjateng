@extends('layouts.app')

@section('content')

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="flex flex-col gap-4 px-5 py-4 sm:flex-row sm:items-center sm:justify-between border-b border-gray-200 dark:border-gray-800">
        <form method="get" class="flex items-center gap-2">
            <div class="relative">
                <span class="absolute top-1/2 left-0 -translate-y-1/2 border-r border-gray-200 px-3.5 py-3 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M3.04175 9.37363C3.04175 5.87693 5.87711 3.04199 9.37508 3.04199C12.8731 3.04199 15.7084 5.87693 15.7084 9.37363C15.7084 12.8703 12.8731 15.7053 9.37508 15.7053C5.87711 15.7053 3.04175 12.8703 3.04175 9.37363ZM9.37508 1.54199C5.04902 1.54199 1.54175 5.04817 1.54175 9.37363C1.54175 13.6991 5.04902 17.2053 9.37508 17.2053C11.2674 17.2053 13.003 16.5344 14.357 15.4176L17.177 18.238C17.4699 18.5309 17.9448 18.5309 18.2377 18.238C18.5306 17.9451 18.5306 17.4703 18.2377 17.1774L15.418 14.3573C16.5365 13.0033 17.2084 11.2669 17.2084 9.37363C17.2084 5.04817 13.7011 1.54199 9.37508 1.54199Z" fill="currentColor"/>
                    </svg>
                </span>
                <input type="text" name="q" value="{{ $q ?? '' }}" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full sm:w-[280px] rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pl-[62px] text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" placeholder="Cari...">
            </div>
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">Cari</button>
        </form>
        <a href="{{ route('transaksi.mutasi.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
            <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10 3.75V16.25M16.25 10H3.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            Tambah Mutasi
        </a>
    </div>

    <div class="overflow-x-auto"><table class="table-sticky min-w-full">
            <thead>
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">NO</th>
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">NO MUTASI</th>
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">TANGGAL</th>
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">TIPE & QTY</th>
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">KETERANGAN</th>
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">AKSI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($mutasi as $i => $m)
                <tr>
                    <td class="px-5 py-4 text-sm text-center text-gray-800 dark:text-white/90">{{ $i + 1 }}</td>
                    <td class="px-5 py-4 text-center">
                        <span class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-1 text-xs font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">
                            {{ esc($m->no_mutasi) }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-sm text-center text-gray-500 dark:text-gray-400">{{ $m->tanggal->format('d/m/Y') }}</td>
                    <td class="px-5 py-4 text-sm text-center">
                        @if(!empty($m->detail_summary))
                            @foreach(explode(', ', $m->detail_summary) as $det)
                                @php
                                    list($tipe, $qty) = explode(':', $det);
                                    $label = $tipe === 'baik_ke_rusak' ? 'Baik→Rusak' : 'Rusak→Baik';
                                    $color = $tipe === 'baik_ke_rusak' ? 'bg-warning-50 text-warning-600 dark:bg-warning-500/15 dark:text-warning-400' : 'bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400';
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $color }} mr-1 mb-1">
                                    {{ $label }} : {{ $qty }}
                                </span>
                            @endforeach
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-sm text-center text-gray-500 dark:text-gray-400">{{ $m->keterangan ?: '-' }}</td>
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('transaksi.mutasi.detail', $m->id) }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-50 p-2 text-brand-600 hover:bg-brand-100 dark:bg-brand-500/15 dark:text-brand-400" title="Lihat Detail">
                                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10 3.75C5.83333 3.75 2.5 7.5 2.5 10C2.5 12.5 5.83333 16.25 10 16.25C14.1667 16.25 17.5 12.5 17.5 10C17.5 7.5 14.1667 3.75 10 3.75ZM10 14.1667C7.75 14.1667 5.83333 12.25 5.83333 10C5.83333 7.75 7.75 5.83333 10 5.83333C12.25 5.83333 14.1667 7.75 14.1667 10C14.1667 12.25 12.25 14.1667 10 14.1667ZM10 7.5C8.61667 7.5 7.5 8.61667 7.5 10C7.5 11.3833 8.61667 12.5 10 12.5C11.3833 12.5 12.5 11.3833 12.5 10C12.5 8.61667 11.3833 7.5 10 7.5Z" fill="currentColor"/>
                                </svg>
                            </a>
                            <a href="{{ route('transaksi.mutasi.edit', $m->id) }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-warning-50 p-2 text-warning-600 hover:bg-warning-100 dark:bg-warning-500/15 dark:text-warning-400">
                                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M13.75 2.125C14.1642 2.125 14.5 2.46079 14.5 2.875V5.0568L17.0683 5.0568C17.4825 5.0568 17.8183 5.39259 17.8183 5.8068C17.8183 6.22102 17.4825 6.5568 17.0683 6.5568L15.4379 6.5568L14.5 6.5568L14.5 14.375C14.5 16.5841 12.7091 18.375 10.5 18.375H5.5C3.29086 18.375 1.5 16.5841 1.5 14.375V6.25C1.5 4.04086 3.29086 2.25 5.5 2.25H13C13.4142 2.25 13.75 2.58579 13.75 3V2.125ZM13 3.75H5.5C4.11929 3.75 3 4.86929 3 6.25V14.375C3 15.7557 4.11929 16.875 5.5 16.875H10.5C11.8807 16.875 13 15.7557 13 14.375V6.5568V5.8068H13V4.375V3.75ZM8 8.75C8 8.33579 8.33579 8 8.75 8C9.16421 8 9.5 8.33579 9.5 8.75V10.75H11.5C11.9142 10.75 12.25 11.0858 12.25 11.5C12.25 11.9142 11.9142 12.25 11.5 12.25H9.5V14.25C9.5 14.6642 9.16421 15 8.75 15C8.33579 15 8 14.6642 8 14.25V12.25H6C5.58579 12.25 5.25 11.9142 5.25 11.5C5.25 11.0858 5.58579 10.75 6 10.75H8V8.75Z" fill="currentColor"/>
                                </svg>
                            </a>
                            <form method="post" action="{{ route('transaksi.mutasi.delete', $m->id) }}" class="inline" data-confirm-message="Yakin ingin menghapus data mutasi ini?" data-confirm-ok="Ya, Hapus">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-error-50 p-2 text-error-600 hover:bg-error-100 dark:bg-error-500/15 dark:text-error-400">
                                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M7.5 2.5C7.5 2.08579 7.83579 1.75 8.25 1.75H11.75C12.1642 1.75 12.5 2.08579 12.5 2.5V3.5H6.25V2.5H7.5ZM4.75 4.5V3.5C4.75 2.25736 5.75736 1.25 7 1.25H13C14.2426 1.25 15.25 2.25736 15.25 3.5V4.5H16.5C17.1904 4.5 17.75 5.05964 17.75 5.75C17.75 6.44036 17.1904 7 16.5 7H16.1273L15.2865 16.2885C15.1399 17.8235 13.8455 19 12.3043 19H7.6957C6.15448 19 4.86011 17.8235 4.71346 16.2885L3.87273 7H3.5C2.80964 7 2.25 6.44036 2.25 5.75C2.25 5.05964 2.80964 4.5 3.5 4.5H4.75ZM6.21817 7L7.04185 16.1443C7.07708 16.5095 7.39145 16.75 7.6957 16.75H12.3043C12.6086 16.75 12.9229 16.5095 12.9582 16.1443L13.7818 7H6.21817Z" fill="currentColor"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center">
                        <svg class="mx-auto mb-4 h-16 w-16 text-gray-300 dark:text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" />
                        </svg>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada data mutasi</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($mutasi->hasPages())
    <div class="border-t border-gray-200 dark:border-gray-800">
        {{ $mutasi->links('vendor.pagination.tailadmin') }}
    </div>
    @endif
</div>
@endsection
