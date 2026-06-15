@extends('layouts.app')

@section('content')

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="p-5 sm:p-6">

        <div class="grid grid-cols-1 gap-5 md:grid-cols-3 mb-6">
            <div>
                <p class="mb-1 text-xs font-medium text-gray-500 uppercase dark:text-gray-400">WAKTU</p>
                <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $log->created_at ? date('d/m/Y H:i:s', strtotime($log->created_at)) : '-' }}</p>
            </div>
            <div>
                <p class="mb-1 text-xs font-medium text-gray-500 uppercase dark:text-gray-400">USER</p>
                <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $log->user_nama ?? 'System' }}</p>
            </div>
            <div>
                <p class="mb-1 text-xs font-medium text-gray-500 uppercase dark:text-gray-400">AKSI</p>
                <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                    @php
                        $isError = in_array($log->action, ['update_gagal', 'delete', 'error']);
                        $isSuccess = in_array($log->action, ['create', 'store', 'update']);
                        $isLogin = $log->action === 'login';
                    @endphp
                    @if($isError)
                        <span class="inline-flex items-center rounded-full bg-error-50 px-2.5 py-1 text-xs font-medium text-error-600 dark:bg-error-500/15 dark:text-error-400">{{ $log->action }}</span>
                    @elseif($isLogin)
                        <span class="inline-flex items-center rounded-full bg-blue-light-50 px-2.5 py-1 text-xs font-medium text-blue-light-600 dark:bg-blue-light-500/15 dark:text-blue-light-400">{{ $log->action }}</span>
                    @elseif($isSuccess)
                        <span class="inline-flex items-center rounded-full bg-success-50 px-2.5 py-1 text-xs font-medium text-success-600 dark:bg-success-500/15 dark:text-success-400">{{ $log->action }}</span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-gray-50 px-2.5 py-1 text-xs font-medium text-gray-700 dark:text-white/90 dark:bg-gray-800">{{ $log->action }}</span>
                    @endif
                </p>
            </div>
            <div>
                <p class="mb-1 text-xs font-medium text-gray-500 uppercase dark:text-gray-400">TABEL</p>
                <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $log->table_name ?? '-' }}</p>
            </div>
            <div>
                <p class="mb-1 text-xs font-medium text-gray-500 uppercase dark:text-gray-400">REFERENCE ID</p>
                <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $log->reference_id ?? '-' }}</p>
            </div>
            <div class="md:col-span-3">
                <p class="mb-1 text-xs font-medium text-gray-500 uppercase dark:text-gray-400">DESKRIPSI</p>
                <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $log->description ?? '-' }}</p>
            </div>
        </div>

        @if(!empty($log->data) && $log->data !== 'null' && $log->data !== '[]')
        <div class="border-t border-gray-200 pt-6 dark:border-gray-800">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-white/90 mb-3">Data JSON</h3>
            <pre class="rounded-lg border border-gray-200 bg-gray-50 p-4 font-mono text-xs leading-relaxed text-gray-800 overflow-x-auto dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" style="max-height:500px; white-space:pre-wrap; word-break:break-word;">
@php
    $jsonData = is_string($log->data) ? json_decode($log->data, true) : $log->data;
    echo json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
@endphp</pre>
        </div>
        @endif

        <div class="mt-6 flex items-center gap-3 border-t border-gray-200 pt-4 dark:border-gray-800">
            <a href="{{ route('pengaturan.auditlog.index') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs ring-1 ring-inset ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.8334 10H4.16669M4.16669 10L9.16669 15M4.16669 10L9.16669 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Kembali
            </a>
        </div>
    </div>
</div>

@endsection
