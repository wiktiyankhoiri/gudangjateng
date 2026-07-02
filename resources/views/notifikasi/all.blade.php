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
                <input type="text" name="cari" value="{{ $cari ?? '' }}" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full sm:w-[280px] rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pl-[62px] text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" placeholder="Cari notifikasi...">
            </div>
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">Cari</button>
        </form>
        <button onclick="markAllReadAndRedirect()" class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-50 px-4 py-3 text-sm font-medium text-brand-600 shadow-theme-xs transition hover:bg-brand-100 dark:bg-brand-500/15 dark:text-brand-400 dark:hover:bg-brand-500/25">
            <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M12.75 2.125C13.1642 2.125 13.5 2.46079 13.5 2.875V5.0568L16.0683 5.0568C16.4825 5.0568 16.8183 5.39259 16.8183 5.8068C16.8183 6.22102 16.4825 6.5568 16.0683 6.5568L14.4379 6.5568L13.5 6.5568L13.5 14.375C13.5 16.5841 11.7091 18.375 9.5 18.375H4.5C2.29086 18.375 0.5 16.5841 0.5 14.375V6.25C0.5 4.04086 2.29086 2.25 4.5 2.25H12C12.4142 2.25 12.75 2.58579 12.75 3V2.125ZM12 3.75H4.5C3.11929 3.75 2 4.86929 2 6.25V14.375C2 15.7557 3.11929 16.875 4.5 16.875H9.5C10.8807 16.875 12 15.7557 12 14.375V6.5568V5.8068H12V4.375V3.75ZM7.625 8.625C8.03921 8.625 8.375 8.96079 8.375 9.375V11H10C10.4142 11 10.75 11.3358 10.75 11.75C10.75 12.1642 10.4142 12.5 10 12.5H8.375V14.125C8.375 14.5392 8.03921 14.875 7.625 14.875C7.21079 14.875 6.875 14.5392 6.875 14.125V12.5H5.25C4.83579 12.5 4.5 12.1642 4.5 11.75C4.5 11.3358 4.83579 11 5.25 11H6.875V9.375C6.875 8.96079 7.21079 8.625 7.625 8.625Z" fill="currentColor"/>
            </svg>
            Tandai Semua Dibaca
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400" style="width:50px">#</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">TIPE</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">PESAN</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">TANGGAL</th>
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">STATUS</th>
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">AKSI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @if($notifications->isNotEmpty())
                    @php
                        $typeLabels = [
                            'barang_masuk' => ['label' => 'Barang Masuk', 'class' => 'bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-400'],
                            'barang_keluar' => ['label' => 'Barang Keluar', 'class' => 'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-400'],
                            'mutasi' => ['label' => 'Mutasi', 'class' => 'bg-warning-50 text-warning-600 dark:bg-warning-500/15 dark:text-warning-400'],
                            'penyesuaian_stok' => ['label' => 'Penyesuaian', 'class' => 'bg-blue-50 text-blue-600 dark:bg-blue-500/15 dark:text-blue-400'],
                            'initialstok' => ['label' => 'Initial Stok', 'class' => 'bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400'],
                        ];
                    @endphp
                    @foreach($notifications as $i => $n)
                    <tr id="notif-{{ $n->id }}" class="{{ $barangIds[$n->id] ? 'cursor-pointer hover:bg-gray-50 dark:hover:bg-white/5' : '' }}" onclick="{{ $barangIds[$n->id] ? 'goToKartuStok(' . $barangIds[$n->id] . ')' : '' }}">
                        <td class="px-5 py-4 text-sm text-center text-gray-500 dark:text-gray-400">{{ $notifications->firstItem() + $i }}</td>
                        <td class="px-5 py-4">
                            @php $t = $typeLabels[$n->type] ?? ['label' => ucfirst($n->type), 'class' => 'bg-gray-50 text-gray-600 dark:bg-gray-500/15 dark:text-gray-400']; @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $t['class'] }}">{{ $t['label'] }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="text-sm font-medium text-gray-800 dark:text-white/90">{{ esc($n->title) }}</div>
                            <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400 truncate max-w-[300px]">{{ esc($n->message) }}</div>
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $n->created_at?->format('d/m/Y H:i') ?? $n->created_at }}</td>
                        <td class="px-5 py-4 text-center">
                            @if(!$n->is_read)
                                <span class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-1 text-xs font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">Belum dibaca</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-50 px-2.5 py-1 text-xs font-medium text-gray-500 dark:bg-gray-800 dark:text-gray-400">Sudah dibaca</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if(!$n->is_read)
                            <button onclick="event.stopPropagation(); markReadAndReload({{ $n->id }})" class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-50 px-3 py-2 text-xs font-medium text-brand-600 transition hover:bg-brand-100 dark:bg-brand-500/15 dark:text-brand-400 dark:hover:bg-brand-500/25">
                                <svg width="14" height="14" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M16.7071 4.29289C17.0976 4.68342 17.0976 5.31658 16.7071 5.70711L7.70711 14.7071C7.31658 15.0976 6.68342 15.0976 6.29289 14.7071L2.29289 10.7071C1.90237 10.3166 1.90237 9.68342 2.29289 9.29289C2.68342 8.90237 3.31658 8.90237 3.70711 9.29289L7 12.5858L15.2929 4.29289C15.6834 3.90237 16.3166 3.90237 16.7071 4.29289Z" fill="currentColor"/>
                                </svg>
                                Tandai dibaca
                            </button>
                            @else
                            <button onclick="event.stopPropagation(); goToKartuStok({{ $barangIds[$n->id] ?? 'null' }})" class="inline-flex items-center justify-center gap-2 rounded-lg bg-gray-50 px-3 py-2 text-xs font-medium text-gray-600 transition hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700">
                                <svg width="14" height="14" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11.5 10C11.5 10.8284 10.8284 11.5 10 11.5C9.17157 11.5 8.5 10.8284 8.5 10C8.5 9.17157 9.17157 8.5 10 8.5C10.8284 8.5 11.5 9.17157 11.5 10Z" stroke="currentColor" stroke-width="1.5"/><path fill-rule="evenodd" clip-rule="evenodd" d="M10 4C6.13401 4 3.5 7.5 3.5 10C3.5 12.5 6.13401 16 10 16C13.866 16 16.5 12.5 16.5 10C16.5 7.5 13.866 4 10 4ZM10 13C11.6569 13 13 11.6569 13 10C13 8.34315 11.6569 7 10 7C8.34315 7 7 8.34315 7 10C7 11.6569 8.34315 13 10 13Z" fill="currentColor"/>
                                </svg>
                                Lihat
                            </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center">
                            <svg class="mx-auto mb-4 h-16 w-16 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                            </svg>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada notifikasi</p>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    @if($notifications->hasPages())
    <div class="border-t border-gray-200 dark:border-gray-800">
        {{ $notifications->appends(['cari' => $cari ?? ''])->links('vendor.pagination.tailadmin') }}
    </div>
    @endif
</div>

<script>
function goToKartuStok(barangId) {
    if (barangId) {
        window.location.href = '{{ route("laporan.kartustok.index") }}?barang_id=' + barangId;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    const highlight = params.get('highlight');
    if (highlight) {
        const el = document.getElementById('notif-' + highlight);
        if (el) {
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            el.style.backgroundColor = '#f0f4ff';
            setTimeout(function() {
                el.style.transition = 'background-color 1s';
                el.style.backgroundColor = '';
            }, 2000);
        }
    }
});

function markAllReadAndRedirect() {
    fetch('{{ route("notifications.read-all") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
    }).then(() => {
        window.location.reload();
    }).catch(() => {
        alert('Gagal menandai notifikasi. Coba lagi.');
    });
}

function markReadAndReload(id) {
    fetch('{{ route("notifications.read", ["notification" => "__ID__"]) }}'.replace('__ID__', id), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
    }).then(() => {
        window.location.reload();
    }).catch(() => {
        alert('Gagal menandai notifikasi. Coba lagi.');
    });
}
</script>
@endsection
