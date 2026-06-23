<div class="overflow-x-auto">
    <table class="min-w-full">
        <thead>
            <tr class="border-b border-gray-100 dark:border-gray-800">
                <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">ID</th>
                <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">KODE</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">NAMA TOKO</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">ALAMAT</th>
                @can('admin')
                <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">AKSI</th>
                @endcan
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            @forelse($toko as $t)
            <tr>
                <td class="px-5 py-4 text-sm text-center text-gray-800 dark:text-white/90">{{ $t->id }}</td>
                <td class="px-5 py-4 text-center">
                    <span class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-1 text-xs font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">
                        {{ esc($t->kode_toko) }}
                    </span>
                </td>
                <td class="px-5 py-4 text-sm text-left text-gray-800 dark:text-white/90">{{ esc($t->nama_toko) }}</td>
                <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">{{ esc($t->alamat) }}</td>
                @can('admin')
                <td class="px-5 py-4">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('masterdata.toko.edit', $t->id) }}" class="inline-flex items-center justify-center rounded-lg bg-warning-50 p-2 text-warning-600 hover:bg-warning-100 dark:bg-warning-500/15 dark:text-warning-400">
                            <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M13.75 2.125C14.1642 2.125 14.5 2.46079 14.5 2.875V5.0568L17.0683 5.0568C17.4825 5.0568 17.8183 5.39259 17.8183 5.8068C17.8183 6.22102 17.4825 6.5568 17.0683 6.5568L15.4379 6.5568L14.5 6.5568L14.5 14.375C14.5 16.5841 12.7091 18.375 10.5 18.375H5.5C3.29086 18.375 1.5 16.5841 1.5 14.375V6.25C1.5 4.04086 3.29086 2.25 5.5 2.25H13C13.4142 2.25 13.75 2.58579 13.75 3V2.125ZM13 3.75H5.5C4.11929 3.75 3 4.86929 3 6.25V14.375C3 15.7557 4.11929 16.875 5.5 16.875H10.5C11.8807 16.875 13 15.7557 13 14.375V6.5568V5.8068H13V4.375V3.75ZM8 8.75C8 8.33579 8.33579 8 8.75 8C9.16421 8 9.5 8.33579 9.5 8.75V10.75H11.5C11.9142 10.75 12.25 11.0858 12.25 11.5C12.25 11.9142 11.9142 12.25 11.5 12.25H9.5V14.25C9.5 14.6642 9.16421 15 8.75 15C8.33579 15 8 14.6642 8 14.25V12.25H6C5.58579 12.25 5.25 11.9142 5.25 11.5C5.25 11.0858 5.58579 10.75 6 10.75H8V8.75Z" fill="currentColor"/>
                            </svg>
                        </a>
                        <form method="post" action="{{ route('masterdata.toko.delete', $t->id) }}" class="inline" data-confirm-message="Yakin ingin menghapus data toko ini?" data-confirm-ok="Ya, Hapus">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-error-50 p-2 text-error-600 hover:bg-error-100 dark:bg-error-500/15 dark:text-error-400">
                                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M7.5 2.5C7.5 2.08579 7.83579 1.75 8.25 1.75H11.75C12.1642 1.75 12.5 2.08579 12.5 2.5V3.5H6.25V2.5H7.5ZM4.75 4.5V3.5C4.75 2.25736 5.75736 1.25 7 1.25H13C14.2426 1.25 15.25 2.25736 15.25 3.5V4.5H16.5C17.1904 4.5 17.75 5.05964 17.75 5.75C17.75 6.44036 17.1904 7 16.5 7H16.1273L15.2865 16.2885C15.1399 17.8235 13.8455 19 12.3043 19H7.6957C6.15448 19 4.86011 17.8235 4.71346 16.2885L3.87273 7H3.5C2.80964 7 2.25 6.44036 2.25 5.75C2.25 5.05964 2.80964 4.5 3.5 4.5H4.75ZM6.21817 7L7.04185 16.1443C7.07708 16.5095 7.39145 16.75 7.6957 16.75H12.3043C12.6086 16.75 12.9229 16.5095 12.9582 16.1443L13.7818 7H6.21817Z" fill="currentColor"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </td>
                @endcan
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-5 py-12 text-center">
                    <svg class="mx-auto mb-4 h-16 w-16 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada data toko</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($toko->hasPages())
<div class="border-t border-gray-200 dark:border-gray-800">
    {{ $toko->links('vendor.pagination.tailadmin') }}
</div>
@endif
