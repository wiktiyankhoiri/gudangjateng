@php
$page = request()->route()->getName();
$pageMap = [
    // Dashboard & Profile
    'beranda' => 'ecommerce',
    'profile.index' => 'profile',

    // Master Data
    'masterdata.barang.index' => 'barang',
    'masterdata.barang.create' => 'barang',
    'masterdata.barang.store' => 'barang',
    'masterdata.barang.edit' => 'barang',
    'masterdata.barang.update' => 'barang',
    'masterdata.barang.destroy' => 'barang',
    'masterdata.barang.delete' => 'barang',
    'masterdata.barang.export' => 'barang',
    'masterdata.barang.import' => 'barang',
    'masterdata.barang.template' => 'barang',
    'masterdata.toko.index' => 'toko',
    'masterdata.toko.create' => 'toko',
    'masterdata.toko.store' => 'toko',
    'masterdata.toko.edit' => 'toko',
    'masterdata.toko.update' => 'toko',
    'masterdata.toko.destroy' => 'toko',
    'masterdata.toko.delete' => 'toko',
    'masterdata.toko.export' => 'toko',
    'masterdata.toko.import' => 'toko',
    'masterdata.toko.template' => 'toko',
    'masterdata.pabrik.index' => 'pabrik',
    'masterdata.pabrik.create' => 'pabrik',
    'masterdata.pabrik.store' => 'pabrik',
    'masterdata.pabrik.edit' => 'pabrik',
    'masterdata.pabrik.update' => 'pabrik',
    'masterdata.pabrik.destroy' => 'pabrik',
    'masterdata.pabrik.delete' => 'pabrik',
    'masterdata.pabrik.export' => 'pabrik',
    'masterdata.pabrik.import' => 'pabrik',
    'masterdata.pabrik.template' => 'pabrik',

    // Transaksi
    'transaksi.barangmasuk.index' => 'barangMasuk',
    'transaksi.barangmasuk.create' => 'barangMasuk',
    'transaksi.barangmasuk.store' => 'barangMasuk',
    'transaksi.barangmasuk.edit' => 'barangMasuk',
    'transaksi.barangmasuk.update' => 'barangMasuk',
    'transaksi.barangmasuk.destroy' => 'barangMasuk',
    'transaksi.barangmasuk.delete' => 'barangMasuk',
    'transaksi.barangmasuk.detail' => 'barangMasuk',
    'transaksi.barangkeluar.index' => 'barangKeluar',
    'transaksi.barangkeluar.create' => 'barangKeluar',
    'transaksi.barangkeluar.store' => 'barangKeluar',
    'transaksi.barangkeluar.edit' => 'barangKeluar',
    'transaksi.barangkeluar.update' => 'barangKeluar',
    'transaksi.barangkeluar.destroy' => 'barangKeluar',
    'transaksi.barangkeluar.delete' => 'barangKeluar',
    'transaksi.barangkeluar.detail' => 'barangKeluar',
    'transaksi.mutasi.index' => 'mutasi',
    'transaksi.mutasi.create' => 'mutasi',
    'transaksi.mutasi.store' => 'mutasi',
    'transaksi.mutasi.edit' => 'mutasi',
    'transaksi.mutasi.update' => 'mutasi',
    'transaksi.mutasi.destroy' => 'mutasi',
    'transaksi.mutasi.delete' => 'mutasi',
    'transaksi.mutasi.detail' => 'mutasi',

    // Penyesuaian Stok
    'transaksi.penyesuaianstok.index' => 'penyesuaian',
    'transaksi.penyesuaianstok.create' => 'penyesuaian',
    'transaksi.penyesuaianstok.store' => 'penyesuaian',
    'transaksi.penyesuaianstok.detail' => 'penyesuaian',

    // Stok Opname
    'transaksi.stokopname.index' => 'stokopname',
    'transaksi.stokopname.create' => 'stokopname',
    'transaksi.stokopname.store' => 'stokopname',
    'transaksi.stokopname.detail' => 'stokopname',
    'transaksi.stokopname.import' => 'stokopname',
    'transaksi.stokopname.selesaikan' => 'stokopname',
    'transaksi.stokopname.terapkan' => 'stokopname',
    'transaksi.stokopname.batalkan' => 'stokopname',
    'transaksi.stokopname.template' => 'stokopname',

    // Initial Stok
    'pengaturan.initialstok.index' => 'initialStok',
    'pengaturan.initialstok.create' => 'initialStok',
    'pengaturan.initialstok.store' => 'initialStok',
    'pengaturan.initialstok.edit' => 'initialStok',
    'pengaturan.initialstok.update' => 'initialStok',
    'pengaturan.initialstok.destroy' => 'initialStok',
    'pengaturan.initialstok.delete' => 'initialStok',
    'pengaturan.initialstok.export' => 'initialStok',
    'pengaturan.initialstok.import' => 'initialStok',
    'pengaturan.initialstok.template' => 'initialStok',

    // Laporan
    'laporan.salesstok.index' => 'lapSalesStok',
    'laporan.salesstok.export' => 'lapSalesStok',
    'laporan.barangmasuk.index' => 'lapMasuk',
    'laporan.barangkeluar.index' => 'lapKeluar',
    'laporan.mutasi.index' => 'lapMutasi',
    'laporan.kartustok.index' => 'kartuStok',

    // Audit & User
    'pengaturan.auditlog.index' => 'auditLog',
    'pengaturan.auditlog.detail' => 'auditLog',
    'pengaturan.user.index' => 'user',
    'pengaturan.user.create' => 'user',
    'pengaturan.user.store' => 'user',
    'pengaturan.user.edit' => 'user',
    'pengaturan.user.update' => 'user',
    'pengaturan.user.destroy' => 'user',
    'pengaturan.user.delete' => 'user',

    // Backup
    'pengaturan.backup.index' => 'backup',
    'pengaturan.backup.do' => 'backup',
    'pengaturan.backup.download' => 'backup',
    'pengaturan.backup.delete' => 'backup',
    'pengaturan.backup.cleanup' => 'backup',
    'pengaturan.backup.restore' => 'restore',
    'pengaturan.backup.doRestore' => 'restore',

    // Notifikasi
    'notifications.all' => 'notifikasi',
    'pengaturan.tentangsistem.index' => 'system',
    'pengaturan.tentangsistem.prune-audit' => 'system',
    'pengaturan.tentangsistem.clear-cache' => 'system',
    'pengaturan.tentangsistem.cleanup-logs' => 'system',
];
$page = $pageMap[$page] ?? null;

$breadcrumbsMap = [
    // Beranda
    'ecommerce'    => [['label' => 'Beranda', 'route' => 'beranda']],

    // Master Data
    'barang'       => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Barang', 'route' => null]],
    'barangCreate' => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Barang', 'route' => null]],
    'barangEdit'   => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Barang', 'route' => null]],
    'toko'         => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Toko', 'route' => null]],
    'tokoCreate'   => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Toko', 'route' => null]],
    'tokoEdit'     => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Toko', 'route' => null]],
    'pabrik'       => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Pabrik', 'route' => null]],
    'pabrikCreate' => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Pabrik', 'route' => null]],
    'pabrikEdit'   => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Pabrik', 'route' => null]],

    // Transaksi - Barang Masuk
    'barangMasuk'       => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Barang Masuk', 'route' => null]],
    'barangMasukCreate' => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Barang Masuk', 'route' => null]],
    'barangMasukEdit'   => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Barang Masuk', 'route' => null]],
    'barangMasukDetail' => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Barang Masuk', 'route' => null]],

    // Transaksi - Barang Keluar
    'barangKeluar'       => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Barang Keluar', 'route' => null]],
    'barangKeluarCreate' => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Barang Keluar', 'route' => null]],
    'barangKeluarEdit'   => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Barang Keluar', 'route' => null]],
    'barangKeluarDetail' => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Barang Keluar', 'route' => null]],

    // Transaksi - Mutasi
    'mutasi'       => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Mutasi', 'route' => null]],
    'mutasiCreate' => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Mutasi', 'route' => null]],
    'mutasiEdit'   => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Mutasi', 'route' => null]],
    'mutasiDetail' => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Mutasi', 'route' => null]],

    // Penyesuaian Stok
    'penyesuaian'        => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Penyesuaian Stok', 'route' => null]],
    'penyesuaianCreate'  => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Penyesuaian Stok', 'route' => null]],
    'penyesuaianDetail'  => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Penyesuaian Stok', 'route' => null]],

    // Stok Opname
    'stokOpname'       => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Stok Opname', 'route' => null]],
    'stokOpnameCreate' => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Stok Opname', 'route' => null]],
    'stokOpnameDetail' => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Stok Opname', 'route' => null]],

    // Initial Stok
    'initialStok'       => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Initial Stok', 'route' => null]],
    'initialStokCreate' => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Initial Stok', 'route' => null]],
    'initialStokEdit'   => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Initial Stok', 'route' => null]],

    // Laporan
    'lapSalesStok' => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Laporan Sales Stok', 'route' => null]],
    'lapMasuk'     => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Laporan Barang Masuk', 'route' => null]],
    'lapKeluar'    => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Laporan Barang Keluar', 'route' => null]],
    'lapMutasi'    => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Laporan Mutasi', 'route' => null]],
    'kartuStok'    => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Kartu Stok', 'route' => null]],

    // Audit & User
    'auditLog'       => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Audit Log', 'route' => null]],
    'auditLogDetail' => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Audit Log', 'route' => null]],
    'user'           => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'User', 'route' => null]],
    'userCreate'     => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'User', 'route' => null]],
    'userEdit'       => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'User', 'route' => null]],

    // Backup
    'backup'  => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Backup', 'route' => null]],
    'restore' => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Restore', 'route' => null]],

    // Notifikasi & Profile
    'notifikasi' => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Notifikasi', 'route' => null]],
    'profile'    => [['label' => 'Beranda', 'route' => 'beranda'], ['label' => 'Profile', 'route' => null]],
];
$currentBreadcrumbs = $breadcrumbsMap[$page ?? 'ecommerce'] ?? [['label' => 'Beranda', 'route' => 'beranda']];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'GudangJateng' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#0A1633">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="GudangJateng">

    @vite(['resources/css/app.css'])
    @stack('styles')
</head>
<body
    x-data="{ page: '{{ $page ?? 'unknown' }}', loaded: true, darkMode: false, stickyMenu: false, sidebarToggle: false, scrollTop: false }" 
    x-init="
         darkMode = JSON.parse(localStorage.getItem('darkMode'));
         $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)));
         window.matchMedia('(min-width: 1024px)').addEventListener('change', function(e) {
             sidebarToggle = false;
         });"
    :class="{'dark bg-gray-900': darkMode === true}"
>
    <!-- ===== Preloader Start ===== -->
    <div
        x-show="loaded"
        x-init="setTimeout(() => loaded = false, 500)"
        class="fixed left-0 top-0 z-999999 flex h-screen w-screen items-center justify-center bg-white dark:bg-black"
    >
        <div class="h-16 w-16 animate-spin rounded-full border-4 border-solid border-brand-500 border-t-transparent"></div>
    </div>
    <!-- ===== Preloader End ===== -->

    <x-network-banner />

    <!-- ===== Page Wrapper Start ===== -->
    <div class="flex h-screen overflow-hidden">
        <!-- ===== Sidebar Start ===== -->
        @include('layouts.sidebar')
        <!-- ===== Sidebar End ===== -->

        <!-- Small Device Overlay Start -->
        <div
            @click="sidebarToggle = false"
            x-bind:class="sidebarToggle ? 'block lg:hidden' : 'hidden'"
            class="fixed inset-0 z-9 bg-gray-900/50"
        ></div>
        <!-- Small Device Overlay End -->

        <!-- ===== Content Area Start ===== -->
        <div class="relative flex flex-col flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 dark:bg-gray-900">

            <!-- ===== Header Start ===== -->
            @include('layouts.navbar')
            <!-- ===== Header End ===== -->

            <!-- ===== Main Content Start ===== -->
            <main class="flex-1">
                <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6 2xl:p-10">

                    {{-- Flash Messages --}}
                    @php
                        $notifications = [
                            'success' => [
                                'border'     => 'border-success-500',
                                'darkBorder' => 'dark:border-success-500/30',
                                'bg'         => 'bg-success-50',
                                'darkBg'     => 'dark:bg-success-500/15',
                                'iconBg'     => 'bg-success-500',
                                'icon'       => '<svg class="fill-white" width="18" height="18" viewBox="0 0 22 22" xmlns="http://www.w3.org/2000/svg"><path d="M11 0C4.93 0 0 4.93 0 11s4.93 11 11 11 11-4.93 11-11S17.07 0 11 0zm5.23 7.08l-6.3 6.3a.78.78 0 01-1.1 0l-3.06-3.06a.78.78 0 011.1-1.1l2.51 2.51 5.75-5.75a.78.78 0 011.1 1.1z"/></svg>',
                                'title'      => 'Berhasil!',
                            ],
                            'error' => [
                                'border'     => 'border-error-500',
                                'darkBorder' => 'dark:border-error-500/30',
                                'bg'         => 'bg-error-50',
                                'darkBg'     => 'dark:bg-error-500/15',
                                'iconBg'     => 'bg-error-500',
                                'icon'       => '<svg class="fill-white" width="18" height="18" viewBox="0 0 22 22" xmlns="http://www.w3.org/2000/svg"><path d="M11 0C4.93 0 0 4.93 0 11s4.93 11 11 11 11-4.93 11-11S17.07 0 11 0zm3.54 14.54a.78.78 0 01-1.1 0L11 12.1l-2.44 2.44a.78.78 0 01-1.1-1.1L9.9 11 7.46 8.56a.78.78 0 011.1-1.1L11 9.9l2.44-2.44a.78.78 0 011.1 1.1L12.1 11l2.44 2.44a.78.78 0 010 1.1z"/></svg>',
                                'title'      => 'Gagal!',
                            ],
                            'warning' => [
                                'border'     => 'border-warning-500',
                                'darkBorder' => 'dark:border-warning-500/30',
                                'bg'         => 'bg-warning-50',
                                'darkBg'     => 'dark:bg-warning-500/15',
                                'iconBg'     => 'bg-warning-500',
                                'icon'       => '<svg class="fill-white" width="18" height="18" viewBox="0 0 22 22" xmlns="http://www.w3.org/2000/svg"><path d="M11 0C4.93 0 0 4.93 0 11s4.93 11 11 11 11-4.93 11-11S17.07 0 11 0zm0 16.5a1.1 1.1 0 110-2.2 1.1 1.1 0 010 2.2zm.9-4.95a.9.9 0 01-1.8 0V5.5a.9.9 0 011.8 0v6.05z"/></svg>',
                                'title'      => 'Perhatian!',
                            ],
                            'info' => [
                                'border'     => 'border-blue-light-500',
                                'darkBorder' => 'dark:border-blue-light-500/30',
                                'bg'         => 'bg-blue-light-50',
                                'darkBg'     => 'dark:bg-blue-light-500/15',
                                'iconBg'     => 'bg-blue-light-500',
                                'icon'       => '<svg class="fill-white" width="18" height="18" viewBox="0 0 22 22" xmlns="http://www.w3.org/2000/svg"><path d="M11 0C4.93 0 0 4.93 0 11s4.93 11 11 11 11-4.93 11-11S17.07 0 11 0zm-.5 5.5a1.1 1.1 0 112.2 0 1.1 1.1 0 01-2.2 0zm.4 4.4h1.8v6.6h-1.8V9.9z"/></svg>',
                                'title'      => 'Informasi',
                            ],
                        ];
                    @endphp
                    @foreach ($notifications as $type => $config)
                        @if(session()->has($type))
                        <div class="flex w-full border-l-6 {{ $config['border'] }} {{ $config['darkBorder'] }} {{ $config['bg'] }} {{ $config['darkBg'] }} px-7 py-6 shadow-theme-md mb-4" role="alert">
                            <div class="mr-3 flex h-10 w-10 items-center justify-center rounded-lg {{ $config['iconBg'] }}">
                                {!! $config['icon'] !!}
                            </div>
                            <div class="w-full">
                                <h5 class="mb-2 text-lg font-semibold text-black dark:text-white">{{ $config['title'] }}</h5>
                                @php $message = session($type); @endphp
                                @if(is_array($message))
                                    <ul class="text-sm text-gray-700 dark:text-white/80" style="list-style:disc;margin-left:1.25rem">
                                        @foreach ($message as $msg)
                                            <li>{{ esc($msg) }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-sm text-gray-700 dark:text-white/80">{{ esc($message) }}</p>
                                    @if($type === 'error' && is_string($message))
                                        @php
                                            $stockErrors = [
                                                'Stok baik tidak mencukupi' => 'Stok barang sudah berkurang karena transaksi lain (Barang Keluar / Mutasi). Tidak bisa mengurangi jumlah melebihi stok yang tersisa.',
                                                'Stok rusak tidak mencukupi' => 'Stok barang sudah berkurang karena transaksi lain (Barang Keluar / Mutasi). Tidak bisa mengurangi jumlah melebihi stok yang tersisa.',
                                                'Jumlah yang Anda masukkan melebihi stok baik' => 'Jumlah yang Anda masukkan melebihi stok baik yang tersedia. Stok baik telah terpakai transaksi lain.',
                                                'Jumlah yang Anda masukkan melebihi stok rusak' => 'Jumlah yang Anda masukkan melebihi stok rusak yang tersedia. Stok rusak telah terpakai transaksi lain.',
                                                'Stok barang tidak ditemukan' => 'Barang belum memiliki stok. Pastikan sudah ada Barang Masuk atau Initial Stok untuk barang ini.',
                                            ];
                                        @endphp
                                        @foreach ($stockErrors as $key => $detail)
                                            @if(str_contains($message, $key))
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 border-t border-error-200 dark:border-error-700 pt-2">{{ $detail }}</p>
                                            @break
                                            @endif
                                        @endforeach
                                    @endif
                                @endif
                            </div>
                        </div>
                        @endif
                    @endforeach

                    @if($page !== 'ecommerce')
                    <div class="mb-6 flex flex-row flex-wrap items-center justify-between gap-2">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $title ?? '' }}
                        </h2>

                        @if(!empty($currentBreadcrumbs))
                        <nav aria-label="Breadcrumb">
                            <ol class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400">
                                @foreach ($currentBreadcrumbs as $index => $crumb)
                                    @if($crumb['route'])
                                    <li>
                                        <a href="{{ route($crumb['route']) }}" class="hover:text-brand-600 transition-colors">{{ esc($crumb['label']) }}</a>
                                    </li>
                                    @else
                                    <li>
                                        <span class="text-gray-700 dark:text-gray-300" aria-current="page">{{ esc($crumb['label']) }}</span>
                                    </li>
                                    @endif
                                    @if($index < count($currentBreadcrumbs) - 1)
                                    <li class="text-gray-400 dark:text-gray-500 select-none">/</li>
                                    @endif
                                @endforeach
                            </ol>
                        </nav>
                        @endif
                    </div>
                    @endif

                    @yield('content')
                </div>
            </main>
            <!-- ===== Main Content End ===== -->

        </div>
        <!-- ===== Content Area End ===== -->
    </div>
    <!-- ===== Page Wrapper End ===== -->

    {{-- Global Confirm Modal --}}
    <div id="globalConfirmModal" class="fixed inset-0 z-999999 hidden items-center justify-center bg-gray-900/50 px-4 py-6">
        <div class="w-full max-w-md rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xl dark:border-gray-800 dark:bg-gray-900">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-400">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 2.25C6.61522 2.25 2.25 6.61522 2.25 12C2.25 17.3848 6.61522 21.75 12 21.75C17.3848 21.75 21.75 17.3848 21.75 12C21.75 6.61522 17.3848 2.25 12 2.25ZM12.75 7.5C12.75 7.08579 12.4142 6.75 12 6.75C11.5858 6.75 11.25 7.08579 11.25 7.5V12C11.25 12.4142 11.5858 12.75 12 12.75C12.4142 12.75 12.75 12.4142 12.75 12V7.5ZM12 17.25C12.6213 17.25 13.125 16.7463 13.125 16.125C13.125 15.5037 12.6213 15 12 15C11.3787 15 10.875 15.5037 10.875 16.125C10.875 16.7463 11.3787 17.25 12 17.25Z" fill="currentColor"/></svg>
            </div>
            <h3 id="globalConfirmTitle" class="mb-2 text-lg font-semibold text-gray-800 dark:text-white/90">Konfirmasi Aksi</h3>
            <p id="globalConfirmMessage" class="mb-6 text-sm text-gray-500 dark:text-gray-400">Yakin ingin melanjutkan aksi ini?</p>
            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <button type="button" id="globalConfirmCancel" class="inline-flex items-center justify-center rounded-lg bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs ring-1 ring-inset ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">Batal</button>
                <button type="button" id="globalConfirmOk" class="inline-flex items-center justify-center gap-2 rounded-lg bg-error-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-error-600">Ya, Lanjutkan</button>
            </div>
        </div>
    </div>

    {{-- App Config --}}
    <script>
        window.__gudangjateng = {
            csrfToken: '{{ csrf_token() }}',
            searchUrl: '{{ route("search") }}',
            kartuStokUrl: '{{ route("laporan.kartustok.index") }}',
            notificationsListUrl: '{{ route("notifications.list") }}',
            notificationsReadAllUrl: '{{ route("notifications.read-all") }}',
            notificationsMarkReadUrl: '{{ route("notifications.read", ["notification" => "__ID__"]) }}',
            notificationsAllUrl: '{{ route("notifications.all") }}',
            baseUrl: '{{ url("") }}',
            transaksiDetailUrls: {
                barang_masuk: '{{ route("transaksi.barangmasuk.detail", ["barangMasuk" => "__ID__"]) }}',
                barang_keluar: '{{ route("transaksi.barangkeluar.detail", ["barangKeluar" => "__ID__"]) }}',
                mutasi: '{{ route("transaksi.mutasi.detail", ["mutasi" => "__ID__"]) }}',
            },
        };
    </script>

    {{-- Scripts --}}
    @vite(['resources/js/app.js'])
    @vite(['resources/js/app-inline.js'])
    <script>
        // Apply field error classes from Laravel validation errors
        (function () {
            @if ($errors->any())
            const fieldErrors = @json($errors->getMessages());
            if (fieldErrors && Object.keys(fieldErrors).length > 0) {
                Object.keys(fieldErrors).forEach(function(field) {
                    const input = document.querySelector('[name="' + field + '"], [name="' + field + '[]"]');
                    if (input) {
                        input.classList.add('border-error-500', 'focus:border-error-500', 'focus:ring-error-500/10');
                        const wrapper = input.closest('div') || input.parentNode;
                        const errorDiv = document.createElement('p');
                        errorDiv.className = 'mt-1 text-xs text-error-500';
                        errorDiv.textContent = fieldErrors[field][0];
                        wrapper.appendChild(errorDiv);
                    }
                });
                const firstError = document.querySelector('.border-error-500');
                if (firstError) {
                    firstError.focus();
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
            @endif
        })();
    </script>
    @stack('scripts')
</body>
</html>
