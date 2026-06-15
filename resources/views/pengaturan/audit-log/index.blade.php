@extends('layouts.app')

@section('content')

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

    <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800">
        <form method="get">
            <div class="flex flex-col gap-4 sm:flex-row sm:flex-wrap">

                <div class="filter-item" style="flex:1 1 140px; min-width:110px;">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Aksi</label>
                    <select name="action"
                        class="h-11 w-full rounded-lg border dark:bg-dark-900 border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800">
                        <option value="">Semua</option>
                        @foreach($actions ?? [] as $a)
                            <option value="{{ $a }}" {{ ($action ?? '') === $a ? 'selected' : '' }}>{{ $a }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-item" style="flex:1 1 140px; min-width:110px;">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tabel</label>
                    <select name="table_name"
                        class="h-11 w-full rounded-lg border dark:bg-dark-900 border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800">
                        <option value="">Semua</option>
                        @foreach($tables ?? [] as $t)
                            <option value="{{ $t }}" {{ ($tableName ?? '') === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-item" style="flex:1 1 150px; min-width:120px;">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Dari Tanggal</label>
                    <div class="relative">
                        <input type="text" name="tanggal_awal" value="{{ $tanggalAwal ?? '' }}"
                            class="datepicker h-11 w-full appearance-none rounded-lg border dark:bg-dark-900 border-gray-300 bg-transparent bg-none py-2.5 pr-11 pl-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                            placeholder="Pilih tanggal">
                        <span class="pointer-events-none absolute top-1/2 right-3 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                            <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M6.66659 1.5415C7.0808 1.5415 7.41658 1.87729 7.41658 2.2915V2.99984H12.5833V2.2915C12.5833 1.87729 12.919 1.5415 13.3333 1.5415C13.7475 1.5415 14.0833 1.87729 14.0833 2.2915V2.99984L15.4166 2.99984C16.5212 2.99984 17.4166 3.89527 17.4166 4.99984V7.49984V15.8332C17.4166 16.9377 16.5212 17.8332 15.4166 17.8332H4.58325C3.47868 17.8332 2.58325 16.9377 2.58325 15.8332V7.49984V4.99984C2.58325 3.89527 3.47868 2.99984 4.58325 2.99984L5.91659 2.99984V2.2915C5.91659 1.87729 6.25237 1.5415 6.66659 1.5415ZM6.66659 4.49984H4.58325C4.30711 4.49984 4.08325 4.7237 4.08325 4.99984V6.74984H15.9166V4.99984C15.9166 4.7237 15.6927 4.49984 15.4166 4.49984H13.3333H6.66659ZM15.9166 8.24984H4.08325V15.8332C4.08325 16.1093 4.30711 16.3332 4.58325 16.3332H15.4166C15.6927 16.3332 15.9166 16.1093 15.9166 15.8332V8.24984Z" fill=""/>
                            </svg>
                        </span>
                    </div>
                </div>

                <div class="filter-item" style="flex:1 1 150px; min-width:120px;">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Sampai Tanggal</label>
                    <div class="relative">
                        <input type="text" name="tanggal_akhir" value="{{ $tanggalAkhir ?? '' }}"
                            class="datepicker h-11 w-full appearance-none rounded-lg border dark:bg-dark-900 border-gray-300 bg-transparent bg-none py-2.5 pr-11 pl-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                            placeholder="Pilih tanggal">
                        <span class="pointer-events-none absolute top-1/2 right-3 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                            <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M6.66659 1.5415C7.0808 1.5415 7.41658 1.87729 7.41658 2.2915V2.99984H12.5833V2.2915C12.5833 1.87729 12.919 1.5415 13.3333 1.5415C13.7475 1.5415 14.0833 1.87729 14.0833 2.2915V2.99984L15.4166 2.99984C16.5212 2.99984 17.4166 3.89527 17.4166 4.99984V7.49984V15.8332C17.4166 16.9377 16.5212 17.8332 15.4166 17.8332H4.58325C3.47868 17.8332 2.58325 16.9377 2.58325 15.8332V7.49984V4.99984C2.58325 3.89527 3.47868 2.99984 4.58325 2.99984L5.91659 2.99984V2.2915C5.91659 1.87729 6.25237 1.5415 6.66659 1.5415ZM6.66659 4.49984H4.58325C4.30711 4.49984 4.08325 4.7237 4.08325 4.99984V6.74984H15.9166V4.99984C15.9166 4.7237 15.6927 4.49984 15.4166 4.49984H13.3333H6.66659ZM15.9166 8.24984H4.08325V15.8332C4.08325 16.1093 4.30711 16.3332 4.58325 16.3332H15.4166C15.6927 16.3332 15.9166 16.1093 15.9166 15.8332V8.24984Z" fill=""/>
                            </svg>
                        </span>
                    </div>
                </div>

                <div class="filter-item" style="flex:1 1 140px; min-width:110px;">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Pengguna</label>
                    <select name="user_id"
                        class="h-11 w-full rounded-lg border dark:bg-dark-900 border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800">
                        <option value="">Semua</option>
                        @foreach($users ?? [] as $u)
                            @if(!empty($u->user_id))
                            <option value="{{ $u->user_id }}" {{ ($userId ?? '') == $u->user_id ? 'selected' : '' }}>{{ $u->nama }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="filter-item" style="flex:1 1 160px; min-width:130px;">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Cari</label>
                    <input type="text" name="search" value="{{ $search ?? '' }}"
                        class="h-11 w-full rounded-lg border dark:bg-dark-900 border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                        placeholder="Cari deskripsi...">
                </div>

                <div class="filter-btn-wrap flex flex-wrap gap-2" style="flex-shrink:0; padding-top:28px;">
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17.5 17.5L12.5 12.5M14.1667 8.33333C14.1667 11.555 11.555 14.1667 8.33333 14.1667C5.11167 14.1667 2.5 11.555 2.5 8.33333C2.5 5.11167 5.11167 2.5 8.33333 2.5C11.555 2.5 14.1667 5.11167 14.1667 8.33333Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Cari
                    </button>
                    <a href="{{ route('pengaturan.auditlog.index') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs ring-1 ring-inset ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 2.5C5.85786 2.5 2.5 5.85786 2.5 10C2.5 10.4142 2.83579 10.75 3.25 10.75C3.66421 10.75 4 10.4142 4 10C4 6.68629 6.68629 4 10 4C13.3137 4 16 6.68629 16 10C16 13.3137 13.3137 16 10 16C9.58579 16 9.25 16.3358 9.25 16.75C9.25 17.1642 9.58579 17.5 10 17.5C14.1421 17.5 17.5 14.1421 17.5 10C17.5 5.85786 14.1421 2.5 10 2.5ZM10.5303 6.96967C10.2374 6.67678 9.76256 6.67678 9.46967 6.96967L6.96967 9.46967C6.67678 9.76256 6.67678 10.2374 6.96967 10.5303C7.26256 10.8232 7.73744 10.8232 8.03033 10.5303L9.25 9.31066V13.25C9.25 13.6642 9.58579 14 10 14C10.4142 14 10.75 13.6642 10.75 13.25V9.31066L11.9697 10.5303C12.2626 10.8232 12.7374 10.8232 13.0303 10.5303C13.3232 10.2374 13.3232 9.76256 13.0303 9.46967L10.5303 6.96967Z" fill="currentColor"/></svg>
                        Atur Ulang
                    </a>
                </div>

            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400 w-[50px]">#</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Waktu</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Pengguna</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Aksi</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Tabel</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400 w-[200px]">Deskripsi</th>
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400 w-[80px]">Detail</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($data as $i => $d)
                @php
                    $isError = in_array($d->action, ['update_gagal', 'delete', 'error']);
                    $isSuccess = in_array($d->action, ['create', 'store', 'update', 'login']);
                @endphp
                <tr class="{{ $isError ? 'bg-red-50/30 dark:bg-red-500/5' : '' }}">
                    <td class="px-5 py-4 text-sm text-center text-gray-500 dark:text-gray-400">{{ $data->firstItem() + $i }}</td>
                    <td class="px-5 py-4 text-sm text-gray-800 dark:text-white/90 whitespace-nowrap">{{ $d->created_at instanceof \Carbon\Carbon ? $d->created_at->format('d/m/Y H:i') : date('d/m/Y H:i', strtotime($d->created_at)) }}</td>
                    <td class="px-5 py-4 text-sm text-gray-800 dark:text-white/90">{{ $d->user_nama ?? 'System' }}</td>
                    <td class="px-5 py-4">
                        @if($isError)
                            <span class="inline-flex items-center rounded-full bg-error-50 px-2.5 py-1 text-xs font-medium text-error-600 dark:bg-error-500/15 dark:text-error-400">{{ $d->action }}</span>
                        @elseif(in_array($d->action, ['login']))
                            <span class="inline-flex items-center rounded-full bg-blue-light-50 px-2.5 py-1 text-xs font-medium text-blue-light-600 dark:bg-blue-light-500/15 dark:text-blue-light-400">{{ $d->action }}</span>
                        @elseif($isSuccess)
                            <span class="inline-flex items-center rounded-full bg-success-50 px-2.5 py-1 text-xs font-medium text-success-600 dark:bg-success-500/15 dark:text-success-400">{{ $d->action }}</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-gray-50 px-2.5 py-1 text-xs font-medium text-gray-700 dark:text-white/90 dark:bg-gray-800">{{ $d->action }}</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $d->table_name ?? '-' }}</td>
                    <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400 max-w-[200px] truncate" title="{{ $d->description ?? '-' }}">{{ $d->description ?? '-' }}</td>
                    <td class="px-5 py-4 text-center">
                        <a href="{{ route('pengaturan.auditlog.detail', $d->id) }}"
                            class="inline-flex items-center justify-center rounded-lg bg-brand-50 p-2 text-brand-600 transition hover:bg-brand-100 dark:bg-brand-500/15 dark:text-brand-400 dark:hover:bg-brand-500/25">
                            <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11.5 10C11.5 10.8284 10.8284 11.5 10 11.5C9.17157 11.5 8.5 10.8284 8.5 10C8.5 9.17157 9.17157 8.5 10 8.5C10.8284 8.5 11.5 9.17157 11.5 10Z" stroke="currentColor" stroke-width="1.5"/><path fill-rule="evenodd" clip-rule="evenodd" d="M10 4C6.13401 4 3.5 7.5 3.5 10C3.5 12.5 6.13401 16 10 16C13.866 16 16.5 12.5 16.5 10C16.5 7.5 13.866 4 10 4ZM10 13C11.6569 13 13 11.6569 13 10C13 8.34315 11.6569 7 10 7C8.34315 7 7 8.34315 7 10C7 11.6569 8.34315 13 10 13Z" fill="currentColor"/></svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center">
                        <svg class="mx-auto mb-4 h-16 w-16 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada data log audit</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($data->hasPages())
    <div class="border-t border-gray-200 px-5 py-4 dark:border-gray-800">
        {{ $data->appends([
            'action' => $action,
            'table_name' => $tableName,
            'tanggal_awal' => $tanggalAwal,
            'tanggal_akhir' => $tanggalAkhir,
            'user_id' => $userId,
            'search' => $search,
        ])->links('vendor.pagination.tailadmin') }}
    </div>
    @endif
</div>
@endsection
