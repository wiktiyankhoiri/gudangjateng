@php
$role = auth()->user()->role;
$isAdmin = $role === 'admin';
$isAudit = $role === 'audit';
$isManager = $role === 'manager';
$isSales = $role === 'sales';
$isSuperAdmin = $role === 'super_admin';

$showMaster = $isAdmin || $isSuperAdmin;
$showTransaksi = $isAdmin || $isAudit || $isSuperAdmin;
$showInitialStok = $isAdmin || $isSuperAdmin;
$showPenyesuaian = $isAudit || $isSuperAdmin;
$showUser = $isSuperAdmin;
$showBackup = $isAdmin || $isSuperAdmin;
$showRestore = $isAdmin || $isSuperAdmin;
$showLaporan = $isAdmin || $isAudit || $isManager || $isSales || $isSuperAdmin;
$showLapSalesStok = $isAdmin || $isAudit || $isManager || $isSales || $isSuperAdmin;
$showKartuStok = $isAdmin || $isAudit || $isManager || $isSales || $isSuperAdmin;
$showLapMasuk = $isAdmin || $isAudit || $isManager || $isSales || $isSuperAdmin;
$showLapKeluar = $isAdmin || $isAudit || $isManager || $isSales || $isSuperAdmin;
$showLapMutasi = $isAdmin || $isAudit || $isManager || $isSuperAdmin;
$showStokOpname = $isAdmin || $isAudit || $isSuperAdmin;
$showAuditLog = $isAdmin || $isAudit || $isSuperAdmin;
$showSystem = $isAdmin || $isAudit || $isSuperAdmin;

// Determine alpine selected menu based on current page
$currentPage = request()->route()->getName();
$isDashboard = $currentPage === 'beranda';
$isBarang = str_starts_with($currentPage, 'masterdata.barang.');
$isToko = str_starts_with($currentPage, 'masterdata.toko.');
$isPabrik = str_starts_with($currentPage, 'masterdata.pabrik.');
$isStokOpname = str_starts_with($currentPage, 'transaksi.stokopname.');
$isBarangMasuk = str_starts_with($currentPage, 'transaksi.barangmasuk.');
$isBarangKeluar = str_starts_with($currentPage, 'transaksi.barangkeluar.');
$isMutasi = str_starts_with($currentPage, 'transaksi.mutasi.');
$isLapSalesStok = str_starts_with($currentPage, 'laporan.salesstok.');
$isKartuStok = str_starts_with($currentPage, 'laporan.kartustok.');
$isLapMasuk = str_starts_with($currentPage, 'laporan.barangmasuk.');
$isLapKeluar = str_starts_with($currentPage, 'laporan.barangkeluar.');
$isLapMutasi = str_starts_with($currentPage, 'laporan.mutasi.');
$isAuditLog = str_starts_with($currentPage, 'pengaturan.auditlog.');
$isInitial = str_starts_with($currentPage, 'pengaturan.initialstok.');
$isPenyesuaian = str_starts_with($currentPage, 'transaksi.penyesuaianstok.');
$isUser = str_starts_with($currentPage, 'pengaturan.user.');
$isBackup = str_starts_with($currentPage, 'pengaturan.backup.') && !str_contains($currentPage, 'restore');
$isRestore = $currentPage === 'pengaturan.backup.restore';

$masterOpen = $isBarang || $isToko || $isPabrik;
$transOpen = $isBarangMasuk || $isBarangKeluar || $isMutasi || $isPenyesuaian || $isStokOpname;
$lapOpen = $isLapSalesStok || $isLapMasuk || $isLapKeluar || $isLapMutasi || $isKartuStok;
$isSystem = str_starts_with($currentPage, 'pengaturan.tentangsistem.');
$settingOpen = $isInitial || $isUser || $isBackup || $isRestore || $isAuditLog || $isSystem;

$alpineSelected = 'Beranda';
if ($masterOpen) $alpineSelected = 'Master Data';
elseif ($transOpen) $alpineSelected = 'Transaksi';
elseif ($lapOpen) $alpineSelected = 'Laporan';
elseif ($settingOpen) $alpineSelected = 'Pengaturan';
if ($isDashboard) $alpineSelected = 'Beranda';
@endphp

<aside
    x-bind:class="sidebarToggle ? 'translate-x-0 lg:w-[90px]' : '-translate-x-full'"
    class="sidebar fixed left-0 top-0 z-9999 flex h-screen w-[290px] flex-col overflow-y-hidden border-r border-gray-200 bg-white px-5 dark:border-gray-800 dark:bg-gray-900 lg:static lg:translate-x-0"
>
    <!-- SIDEBAR HEADER -->
    <div class="flex items-center justify-start pt-8 sidebar-header pb-7 px-4">
        <a href="{{ route('beranda') }}" class="flex items-center justify-start w-full">
            <div class="flex items-center justify-start min-w-[48px]">
                <span class="logo flex items-center justify-center" x-bind:class="sidebarToggle ? 'lg:hidden' : ''">
                    <img src="{{ asset('images/logo-full.png') }}" alt="GudangJateng" class="h-8 w-auto">
                </span>
                <span class="logo-icon hidden lg:flex items-center justify-center" x-bind:class="sidebarToggle ? '' : 'lg:hidden'">
                    <img src="{{ asset('images/logo-icon.png') }}" alt="G" class="h-8 w-auto">
                </span>
            </div>
        </a>
    </div>
    <!-- SIDEBAR HEADER -->

    <div class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar">
        <!-- Sidebar Menu -->
        <nav x-data="{selected: '{{ $alpineSelected }}'}">
            <!-- Menu Group -->
            <div>
                <h3 class="mb-4 text-xs uppercase leading-[20px] text-gray-400">
                    <span class="menu-group-title" x-bind:class="sidebarToggle ? 'lg:hidden' : ''">MENU</span>
                    <x-icons.menu-group
                        x-bind:class="sidebarToggle ? 'lg:block hidden' : 'hidden'"
                        class="mx-auto menu-group-icon w-6 h-6"
                    />
                </h3>

                <ul class="flex flex-col gap-4 mb-6">

                    <!-- BERANDA -->
                    <li>
                        <a
                            href="{{ route('beranda') }}"
                            class="menu-item group"
                            x-bind:class="page === 'ecommerce' ? 'menu-item-active' : 'menu-item-inactive'"
                        >
                            <x-icons.dashboard
                                x-bind:class="page === 'ecommerce' ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
                                class="w-6 h-6"
                            />
                            <span class="menu-item-text" x-bind:class="sidebarToggle ? 'lg:hidden' : ''">Beranda</span>
                        </a>
                    </li>

                    <!-- MASTER DATA -->
                    @if($showMaster)
                    <li>
                        <a
                            href="#"
                            @click.prevent="selected = (selected === 'Master Data' ? '':'Master Data')"
                            class="menu-item group"
                            x-bind:class="(selected === 'Master Data') || (page === 'barang' || page === 'barangCreate' || page === 'barangEdit' || page === 'toko' || page === 'tokoCreate' || page === 'tokoEdit' || page === 'pabrik' || page === 'pabrikCreate' || page === 'pabrikEdit') ? 'menu-item-active' : 'menu-item-inactive'"
                        >
                            <x-icons.master-data
                                x-bind:class="(selected === 'Master Data') || (page === 'barang' || page === 'barangCreate' || page === 'barangEdit' || page === 'toko' || page === 'tokoCreate' || page === 'tokoEdit' || page === 'pabrik' || page === 'pabrikCreate' || page === 'pabrikEdit') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
                                class="w-6 h-6"
                            />
                            <span class="menu-item-text" x-bind:class="sidebarToggle ? 'lg:hidden' : ''">Master Data</span>
                            <x-icons.chevron-down
                                class="menu-item-arrow w-5 h-5"
                                x-bind:class="[(selected === 'Master Data') ? 'menu-item-arrow-active' : 'menu-item-arrow-inactive', sidebarToggle ? 'lg:hidden' : '']"
                            />
                        </a>
                        <div class="overflow-hidden translate" x-bind:class="(selected === 'Master Data') ? 'block' : 'hidden'">
                            <ul x-bind:class="sidebarToggle ? 'lg:hidden' : 'flex'" class="flex flex-col gap-1 mt-2 menu-dropdown pl-9">
                                <li>
                                    <a href="{{ route('masterdata.barang.index') }}" class="menu-dropdown-item group" x-bind:class="page === 'barang' || page === 'barangCreate' || page === 'barangEdit' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                                        Data Barang
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('masterdata.toko.index') }}" class="menu-dropdown-item group" x-bind:class="page === 'toko' || page === 'tokoCreate' || page === 'tokoEdit' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                                        Data Toko
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('masterdata.pabrik.index') }}" class="menu-dropdown-item group" x-bind:class="page === 'pabrik' || page === 'pabrikCreate' || page === 'pabrikEdit' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                                        Data Pabrik
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    @endif

                    <!-- TRANSAKSI -->
                    @if($showTransaksi)
                    <li>
                        <a
                            href="#"
                            @click.prevent="selected = (selected === 'Transaksi' ? '':'Transaksi')"
                            class="menu-item group"
                            x-bind:class="(selected === 'Transaksi') || (page.startsWith('barangMasuk') || page.startsWith('barangKeluar') || page.startsWith('mutasi') || page.startsWith('penyesuaian') || page.startsWith('stokopname')) ? 'menu-item-active' : 'menu-item-inactive'"
                        >
                            <x-icons.transaksi
                                x-bind:class="(selected === 'Transaksi') || (page.startsWith('barangMasuk') || page.startsWith('barangKeluar') || page.startsWith('mutasi') || page.startsWith('penyesuaian') || page.startsWith('stokopname')) ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
                                class="w-6 h-6"
                            />
                            <span class="menu-item-text" x-bind:class="sidebarToggle ? 'lg:hidden' : ''">Transaksi</span>
                            <x-icons.chevron-down
                                class="menu-item-arrow w-5 h-5"
                                x-bind:class="[(selected === 'Transaksi') ? 'menu-item-arrow-active' : 'menu-item-arrow-inactive', sidebarToggle ? 'lg:hidden' : '']"
                            />
                        </a>
                        <div class="overflow-hidden translate" x-bind:class="(selected === 'Transaksi') ? 'block' : 'hidden'">
                            <ul x-bind:class="sidebarToggle ? 'lg:hidden' : 'flex'" class="flex flex-col gap-1 mt-2 menu-dropdown pl-9">
                                @if($isAdmin || $isSuperAdmin)
                                <li>
                                    <a href="{{ route('transaksi.barangmasuk.index') }}" class="menu-dropdown-item group" x-bind:class="page.startsWith('barangMasuk') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                                        Barang Masuk
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('transaksi.barangkeluar.index') }}" class="menu-dropdown-item group" x-bind:class="page.startsWith('barangKeluar') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                                        Barang Keluar
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('transaksi.mutasi.index') }}" class="menu-dropdown-item group" x-bind:class="page.startsWith('mutasi') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                                        Mutasi
                                    </a>
                                </li>
                                @endif
                                @if($showPenyesuaian)
                                <li>
                                    <a href="{{ route('transaksi.penyesuaianstok.index') }}" class="menu-dropdown-item group" x-bind:class="page.startsWith('penyesuaian') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                                        Penyesuaian Stok
                                    </a>
                                </li>
                                @endif
                                @if($showStokOpname)
                                <li>
                                    <a href="{{ route('transaksi.stokopname.index') }}" class="menu-dropdown-item group" x-bind:class="page.startsWith('stokopname') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                                        Stok Opname
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                    @endif

                    <!-- LAPORAN -->
                    @if($showLaporan)
                    <li>
                        <a
                            href="#"
                            @click.prevent="selected = (selected === 'Laporan' ? '':'Laporan')"
                            class="menu-item group"
                            x-bind:class="(selected === 'Laporan') || (page === 'lapSalesStok' || page === 'kartuStok' || page === 'lapMasuk' || page === 'lapKeluar' || page === 'lapMutasi') ? 'menu-item-active' : 'menu-item-inactive'"
                        >
                            <x-icons.laporan
                                x-bind:class="(selected === 'Laporan') || (page === 'lapSalesStok' || page === 'kartuStok' || page === 'lapMasuk' || page === 'lapKeluar' || page === 'lapMutasi') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
                                class="w-6 h-6"
                            />
                            <span class="menu-item-text" x-bind:class="sidebarToggle ? 'lg:hidden' : ''">Laporan</span>
                            <x-icons.chevron-down
                                class="menu-item-arrow w-5 h-5"
                                x-bind:class="[(selected === 'Laporan') ? 'menu-item-arrow-active' : 'menu-item-arrow-inactive', sidebarToggle ? 'lg:hidden' : '']"
                            />
                        </a>
                        <div class="overflow-hidden translate" x-bind:class="(selected === 'Laporan') ? 'block' : 'hidden'">
                            <ul x-bind:class="sidebarToggle ? 'lg:hidden' : 'flex'" class="flex flex-col gap-1 mt-2 menu-dropdown pl-9">
                                @if($showLapSalesStok)
                                <li>
                                    <a href="{{ route('laporan.salesstok.index') }}" class="menu-dropdown-item group" x-bind:class="page === 'lapSalesStok' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                                        Laporan Sales Stok
                                    </a>
                                </li>
                                @endif
                                @if($showKartuStok)
                                <li>
                                    <a href="{{ route('laporan.kartustok.index') }}" class="menu-dropdown-item group" x-bind:class="page === 'kartuStok' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                                        Kartu Stok
                                    </a>
                                </li>
                                @endif
                                @if($showLapMasuk)
                                <li>
                                    <a href="{{ route('laporan.barangmasuk.index') }}" class="menu-dropdown-item group" x-bind:class="page === 'lapMasuk' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                                        Barang Masuk
                                    </a>
                                </li>
                                @endif
                                @if($showLapKeluar)
                                <li>
                                    <a href="{{ route('laporan.barangkeluar.index') }}" class="menu-dropdown-item group" x-bind:class="page === 'lapKeluar' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                                        Barang Keluar
                                    </a>
                                </li>
                                @endif
                                @if($showLapMutasi)
                                <li>
                                    <a href="{{ route('laporan.mutasi.index') }}" class="menu-dropdown-item group" x-bind:class="page === 'lapMutasi' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                                        Mutasi
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                    @endif

                    <!-- PENGATURAN -->
                    @if($showInitialStok || $showAuditLog || $showUser || $showBackup || $showRestore || $showSystem)
                    <li>
                        <a
                            href="#"
                            @click.prevent="selected = (selected === 'Pengaturan' ? '':'Pengaturan')"
                            class="menu-item group"
                            x-bind:class="(selected === 'Pengaturan') || (page.startsWith('initialStok') || page.startsWith('auditLog') || page.startsWith('user') || page === 'backup' || page === 'restore' || page === 'system') ? 'menu-item-active' : 'menu-item-inactive'"
                        >
                            <x-icons.pengaturan
                                x-bind:class="(selected === 'Pengaturan') || (page.startsWith('initialStok') || page.startsWith('auditLog') || page.startsWith('user') || page === 'backup' || page === 'restore' || page === 'system') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
                                class="w-6 h-6"
                            />
                            <span class="menu-item-text" x-bind:class="sidebarToggle ? 'lg:hidden' : ''">Pengaturan</span>
                            <x-icons.chevron-down
                                class="menu-item-arrow w-5 h-5"
                                x-bind:class="[(selected === 'Pengaturan') ? 'menu-item-arrow-active' : 'menu-item-arrow-inactive', sidebarToggle ? 'lg:hidden' : '']"
                            />
                        </a>
                        <div class="overflow-hidden translate" x-bind:class="(selected === 'Pengaturan') ? 'block' : 'hidden'">
                            <ul x-bind:class="sidebarToggle ? 'lg:hidden' : 'flex'" class="flex flex-col gap-1 mt-2 menu-dropdown pl-9">
                                @if($showInitialStok)
                                <li>
                                    <a href="{{ route('pengaturan.initialstok.index') }}" class="menu-dropdown-item group" x-bind:class="page.startsWith('initialStok') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                                        Initial Stok
                                    </a>
                                </li>
                                @endif
                                @if($showAuditLog)
                                <li>
                                    <a href="{{ route('pengaturan.auditlog.index') }}" class="menu-dropdown-item group" x-bind:class="page.startsWith('auditLog') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                                        Audit Log
                                    </a>
                                </li>
                                @endif
                @if($showUser)
                <li>
                    <a href="{{ route('pengaturan.user.index') }}" class="menu-dropdown-item group" x-bind:class="page.startsWith('user') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                        Management User
                    </a>
                                </li>
                                @endif
                                @if($showBackup)
                                <li>
                                    <a href="{{ route('pengaturan.backup.index') }}" class="menu-dropdown-item group" x-bind:class="page === 'backup' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                                        Backup Database
                                    </a>
                                </li>
                                @endif
                                @if($showRestore)
                                <li>
                                    <a href="{{ route('pengaturan.backup.restore') }}" class="menu-dropdown-item group" x-bind:class="page === 'restore' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                                        Restore Database
                                    </a>
                                </li>
                                @endif
                                @if($showSystem)
                                <li>
                                    <a href="{{ route('pengaturan.tentangsistem.index') }}" class="menu-dropdown-item group" x-bind:class="page === 'tentangsistem' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                                        Tentang Sistem
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                    @endif

                </ul>
            </div>
        </nav>
        <!-- Sidebar Menu -->

        <!-- Sidebar Promo -->
        <div
            x-bind:class="sidebarToggle ? 'lg:hidden' : ''"
            class="mx-auto mb-10 w-full max-w-60 rounded-2xl bg-gray-50 px-4 py-5 text-center dark:bg-white/[0.03]"
        >
            <h3 class="mb-2 font-semibold text-gray-900 dark:text-white">
                Gudang Jateng
            </h3>
            <p class="mb-4 text-gray-500 text-theme-sm dark:text-gray-400">
                Versi 1.0.0 &copy; {{ date('Y') }}
            </p>
            <a
                href="https://wa.me/6281215966784"
                target="_blank"
                rel="nofollow"
                class="flex items-center justify-center p-3 font-medium text-white rounded-lg bg-brand-500 text-theme-sm hover:bg-brand-600"
            >
                Contact Developer
            </a>
        </div>
    </div>
</aside>
