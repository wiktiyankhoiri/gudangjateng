<header
    x-data="{ menuToggle: false }"
    class="sticky top-0 z-99999 flex w-full border-gray-200 bg-white lg:border-b dark:border-gray-800 dark:bg-gray-900">
    <div class="flex grow flex-col items-center justify-between lg:flex-row lg:px-6">
        <div class="flex w-full items-center justify-between gap-2 border-b border-gray-200 px-3 py-3 sm:gap-4 lg:justify-normal lg:border-b-0 lg:px-0 lg:py-4 dark:border-gray-800">
            <!-- Hamburger Toggle BTN -->
            <button
                x-bind:class="sidebarToggle ? 'lg:bg-transparent dark:lg:bg-transparent bg-gray-100 dark:bg-gray-800' : ''"
                class="z-99999 flex h-10 w-10 items-center justify-center rounded-lg border-gray-200 text-gray-500 lg:h-11 lg:w-11 lg:border dark:border-gray-800 dark:text-gray-400"
                @click.stop="sidebarToggle = !sidebarToggle">
                <x-icons.hamburger class="hidden fill-current lg:block w-4 h-3" viewBox="0 0 16 12" />
                <x-icons.hamburger x-bind:class="sidebarToggle ? 'hidden' : 'block lg:hidden'" class="fill-current lg:hidden w-6 h-6" viewBox="0 0 24 24" />
                <x-icons.close x-bind:class="sidebarToggle ? 'block lg:hidden' : 'hidden'" class="fill-current w-6 h-6" viewBox="0 0 24 24" />
            </button>
            <!-- Hamburger Toggle BTN -->

            <a href="{{ route('beranda') }}" class="hidden">
                <img src="{{ asset('images/logo-full.png') }}" alt="GudangJateng" class="h-8 w-auto">
            </a>

            <!-- Mobile Search Bar (visible on mobile only, replaces logo) -->
            <div class="block lg:hidden flex-1" x-data="searchBar()" @click.outside="open = false">
                <div class="relative">
                    <input
                        type="text"
                        placeholder="Cari stok barang atau No surat jalan ..."
                        x-model="query"
                        @input="search()"
                        @focus="if (hasResults()) open = true"
                        @keydown.escape.prevent="open = false"
                        @keydown.down.prevent="moveDown()"
                        @keydown.up.prevent="moveUp()"
                        @keydown.enter.prevent="if (activeItem) goTo(activeItem)"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-200 bg-transparent py-2.5 pr-10 pl-3 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-800 dark:bg-gray-900 dark:bg-white/[0.03] dark:text-white/90 dark:placeholder:text-white/30"
                    >
                    <!-- Dropdown results -->
                    <div
                        x-show="open && hasResults()"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="absolute top-full left-0 right-0 mt-1 max-h-[60vh] overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-lg z-50 dark:border-gray-800 dark:bg-gray-900"
                    >
                        <template x-if="!barang.length && !transaksi.length">
                            <div class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">Tidak ada hasil</div>
                        </template>
                        <template x-if="barang.length">
                            <div>
                                <div class="px-4 py-2 text-xs font-semibold uppercase tracking-wider text-gray-400 bg-gray-50 dark:bg-gray-800 dark:text-gray-400">Barang</div>
                                <template x-for="item in barang" :key="'b-'+item.id">
                                    <div @click="goToBarang(item)" @mouseenter="activeItem = item; item._type='barang'" x-bind:class="activeItem && activeItem._type==='barang' && activeItem.id===item.id ? 'bg-gray-50 dark:bg-white/[0.03]' : ''" class="flex flex-col gap-1 px-4 py-3 cursor-pointer border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-white/[0.03]">
                                        <div class="flex items-start gap-2">
                                            <span class="inline-flex items-center flex-shrink-0 rounded-md bg-brand-50 px-2 py-0.5 text-xs font-semibold text-brand-600 dark:bg-brand-500/15 dark:text-brand-400" x-text="item['kode_barang']"></span>
                                            <div class="min-w-0 flex-1">
                                                <div class="text-sm font-medium text-gray-800 dark:text-white/90 leading-snug" x-text="item['nama_barang']"></div>
                                                <div class="flex items-center gap-3 mt-0.5">
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">Baik: <span class="font-semibold text-success-600 dark:text-success-400" x-text="item.stok_baik || 0"></span></span>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">Rusak: <span class="font-semibold text-error-600 dark:text-error-400" x-text="item.stok_rusak || 0"></span></span>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">Sales: <span class="font-semibold text-purple-600 dark:text-purple-400" x-text="item.stok_sales || 0"></span></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template x-if="transaksi.length">
                            <div>
                                <div class="px-4 py-2 text-xs font-semibold uppercase tracking-wider text-gray-400 bg-gray-50 dark:bg-gray-800 dark:text-gray-400">Transaksi</div>
                                <template x-for="item in transaksi" :key="'t-'+item.tipe+'-'+item.id">
                                    <div @click="goToTransaksi(item)" @mouseenter="activeItem = item; item._type='transaksi'" x-bind:class="activeItem && activeItem._type==='transaksi' && activeItem.id===item.id && activeItem.tipe===item.tipe ? 'bg-gray-50 dark:bg-white/[0.03]' : ''" class="flex items-center gap-3 px-4 py-3 cursor-pointer border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-white/[0.03]">
                                        <span class="inline-flex items-center flex-shrink-0 rounded-md bg-blue-50 px-2 py-1 text-xs font-semibold text-blue-600 dark:bg-blue-500/15 dark:text-blue-400" x-text="tipeLabel[item.tipe] || item.tipe"></span>
                                        <span class="flex-1 text-sm font-medium text-gray-800 dark:text-white/90 truncate" x-text="item.no_surat"></span>
                                        <span class="flex-shrink-0 text-xs text-gray-500 dark:text-gray-400" x-text="item.tanggal"></span>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Application nav menu button -->
            <button
                class="z-99999 flex h-10 w-10 items-center justify-center rounded-lg text-gray-700 hover:bg-gray-100 lg:hidden dark:text-gray-400 dark:hover:bg-gray-800"
                x-bind:class="menuToggle ? 'bg-gray-100 dark:bg-gray-800' : ''"
                @click.stop="menuToggle = !menuToggle">
                <x-icons.menu-group class="fill-current w-6 h-6" />
            </button>
            <!-- Application nav menu button -->

            <div class="hidden lg:block" x-data="searchBar()" @click.outside="open = false">
                <div class="relative">
                    <span class="absolute top-1/2 left-3 lg:left-4 -translate-y-1/2 pointer-events-none">
                        <x-icons.search class="text-gray-500 dark:text-gray-400 w-5 h-5" viewBox="0 0 20 20" />
                    </span>
                    <input
                        type="text"
                        placeholder="Cari stok barang atau No surat jalan ..."
                        x-model="query"
                        @input="search()"
                        @focus="if (hasResults()) open = true"
                        @keydown.escape.prevent="open = false"
                        @keydown.down.prevent="moveDown()"
                        @keydown.up.prevent="moveUp()"
                        @keydown.enter.prevent="if (activeItem) goTo(activeItem)"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-200 bg-transparent py-2.5 pr-14 pl-12 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden xl:w-[430px] dark:border-gray-800 dark:bg-gray-900 dark:bg-white/[0.03] dark:text-white/90 dark:placeholder:text-white/30"
                    >
                    <span class="hidden lg:inline-flex absolute top-1/2 right-2.5 -translate-y-1/2 items-center gap-0.5 rounded-lg border border-gray-200 bg-gray-50 px-[7px] py-[4.5px] text-xs -tracking-[0.2px] text-gray-500 pointer-events-none dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-400">
                        <span> &#x2318; </span>
                        <span> K </span>
                    </span>

                    <div
                        x-show="open && hasResults()"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="absolute top-full left-0 right-0 mt-1 max-h-[380px] overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-lg z-50 dark:border-gray-800 dark:bg-gray-900"
                    >
                        <template x-if="!barang.length && !transaksi.length">
                            <div class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                Tidak ada hasil
                            </div>
                        </template>

                        <template x-if="barang.length">
                            <div>
                                <div class="px-4 py-2 text-xs font-semibold uppercase tracking-wider text-gray-400 bg-gray-50 dark:bg-gray-800 dark:text-gray-400">Barang</div>
                                <template x-for="item in barang" :key="'b-'+item.id">
                                    <div
                                        @click="goToBarang(item)"
                                        @mouseenter="activeItem = item; item._type = 'barang'"
                                        x-bind:class="activeItem && activeItem._type==='barang' && activeItem.id===item.id ? 'bg-gray-50 dark:bg-white/[0.03]' : ''"
                                        class="flex flex-col gap-1 px-4 py-3 cursor-pointer border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-white/[0.03]"
                                    >
                                        <div class="flex items-start gap-2">
                                            <span class="inline-flex items-center flex-shrink-0 rounded-md bg-brand-50 px-2 py-0.5 text-xs font-semibold text-brand-600 dark:bg-brand-500/15 dark:text-brand-400" x-text="item['kode_barang']"></span>
                                            <div class="min-w-0 flex-1">
                                                <div class="text-sm font-medium text-gray-800 dark:text-white/90 leading-snug" x-text="item['nama_barang']"></div>
                                                <div class="flex items-center gap-3 mt-0.5">
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                                        Baik: <span class="font-semibold text-success-600 dark:text-success-400" x-text="item.stok_baik || 0"></span>
                                                    </span>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                                        Rusak: <span class="font-semibold text-error-600 dark:text-error-400" x-text="item.stok_rusak || 0"></span>
                                                    </span>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                                        Sales: <span class="font-semibold text-purple-600 dark:text-purple-400" x-text="item.stok_sales || 0"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <template x-if="transaksi.length">
                            <div>
                                <div class="px-4 py-2 text-xs font-semibold uppercase tracking-wider text-gray-400 bg-gray-50 dark:bg-gray-800 dark:text-gray-400">Transaksi</div>
                                <template x-for="item in transaksi" :key="'t-'+item.tipe+'-'+item.id">
                                    <div
                                        @click="goToTransaksi(item)"
                                        @mouseenter="activeItem = item; item._type = 'transaksi'"
                                        x-bind:class="activeItem && activeItem._type==='transaksi' && activeItem.id===item.id && activeItem.tipe===item.tipe ? 'bg-gray-50 dark:bg-white/[0.03]' : ''"
                                        class="flex items-center gap-3 px-4 py-3 cursor-pointer border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-white/[0.03]"
                                    >
                                        <span class="inline-flex items-center flex-shrink-0 rounded-md bg-blue-50 px-2 py-1 text-xs font-semibold text-blue-600 dark:bg-blue-500/15 dark:text-blue-400" x-text="tipeLabel[item.tipe] || item.tipe"></span>
                                        <span class="flex-1 text-sm font-medium text-gray-800 dark:text-white/90 truncate" x-text="item.no_surat"></span>
                                        <span class="flex-shrink-0 text-xs text-gray-500 dark:text-gray-400" x-text="item.tanggal"></span>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

        </div>
        <div
            x-bind:class="menuToggle ? 'flex' : 'hidden'"
            class="shadow-theme-md w-full items-center justify-between gap-4 px-5 py-4 lg:flex lg:justify-end lg:px-0 lg:shadow-none">
            <div class="flex items-center gap-2 2xsm:gap-3">
                <!-- Dark Mode Toggler -->
                <button
                    @click="darkMode = !darkMode"
                    class="relative flex h-11 w-11 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 shadow-theme-xs transition-colors hover:bg-gray-100 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white">
                    <x-icons.sun x-show="darkMode" class="w-5 h-5" viewBox="0 0 20 20" />
                    <x-icons.moon x-show="!darkMode" class="w-5 h-5" viewBox="0 0 20 20" />
                </button>
                <!-- Dark Mode Toggler -->

                <!-- Notification Bell -->
                <div
                    class="relative"
                    x-data="notificationBell()"
                    x-init="fetchNotifications(); setInterval(() => fetchNotifications(), 30000)"
                    @click.outside="dropdownOpen = false">
                    <button
                        class="relative flex h-11 w-11 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
                        @click.prevent="dropdownOpen = !dropdownOpen; if(dropdownOpen) { fetchNotifications(); notifying = false; }">
                        <span
                            x-bind:class="!notifying ? 'hidden' : 'flex'"
                            class="absolute top-0.5 right-0 z-1 h-2 w-2 rounded-full bg-orange-400">
                            <span
                                class="absolute -z-1 inline-flex h-full w-full animate-ping rounded-full bg-orange-400 opacity-75"></span>
                        </span>
                        <x-icons.bell class="w-5 h-5" viewBox="0 0 20 20" />
                    </button>

                    <!-- Dropdown Start -->
                    <div
                        x-show="dropdownOpen"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-1"
                        class="shadow-theme-lg dark:bg-gray-dark absolute -right-[240px] mt-[17px] flex h-[480px] w-[350px] flex-col rounded-2xl border border-gray-200 bg-white p-3 sm:w-[361px] lg:right-0 dark:border-gray-800"
                        @wheel.stop
                        @touchmove.stop>
                        <div class="flex-shrink-0 mb-3 flex items-center justify-between border-b border-gray-100 pb-3 dark:border-gray-800">
                            <h5 class="text-lg font-semibold text-gray-800 dark:text-white/90">Notifikasi</h5>
                            <button @click="dropdownOpen = false" class="text-gray-500 dark:text-gray-400">
                                <x-icons.close class="w-6 h-6" viewBox="0 0 24 24" />
                            </button>
                        </div>

                        <ul class="flex-1 overflow-y-auto custom-scrollbar">
                            <template x-if="loading">
                                <li class="flex items-center justify-center py-8">
                                    <div class="h-6 w-6 animate-spin rounded-full border-2 border-solid border-brand-500 border-t-transparent"></div>
                                </li>
                            </template>

                            <template x-if="!loading && notifications.length === 0">
                                <li class="flex flex-col items-center justify-center py-8 text-gray-400">
                                    <p class="text-xs">Tidak ada notifikasi</p>
                                </li>
                            </template>

                            <template x-for="n in notifications" :key="n.id">
                                <li>
                                    <a
                                        @click.prevent="markRead(n.id, n.ref_id, n.type)"
                                        class="flex gap-3 rounded-lg border-b border-gray-100 px-4.5 py-3 hover:bg-gray-100 dark:border-gray-800 dark:hover:bg-white/5"
                                        href="#">
                                        <span class="relative z-1 block h-10 w-full max-w-10 rounded-full">
                                            <span class="flex h-full w-full items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                                                <span class="text-sm font-semibold text-gray-600 dark:text-gray-300" x-text="n.title.charAt(0)"></span>
                                            </span>
                                            <span
                                                x-bind:class="n.is_read ? 'bg-gray-300' : 'bg-brand-500'"
                                                class="absolute right-0 bottom-0 z-10 h-2.5 w-full max-w-2.5 rounded-full border-[1.5px] border-white dark:border-gray-900"></span>
                                        </span>

                                        <span class="block min-w-0 flex-1">
                                            <span class="text-theme-sm mb-1.5 block font-medium text-gray-800 dark:text-white/90" x-text="n.message"></span>
                                            <span class="text-theme-xs flex items-center gap-2 text-gray-500 dark:text-gray-400">
                                                <span x-text="typeLabel(n.type)"></span>
                                                <span class="h-1 w-1 rounded-full bg-gray-400"></span>
                                                <span x-text="timeAgo(n.created_at)"></span>
                                            </span>
                                        </span>
                                    </a>
                                </li>
                            </template>
                        </ul>

                        <a
                            href="{{ route('notifications.all') }}"
                            class="flex-shrink-0 text-theme-sm shadow-theme-xs mt-3 flex justify-center rounded-lg border border-gray-300 bg-white p-3 font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200"
                        >
                            Lihat Semua Notifikasi
                        </a>
                    </div>
                    <!-- Dropdown End -->
                </div>
                <!-- Notification Bell -->
            </div>

            <div class="flex items-center gap-2">
                <!-- User Area -->
                <div
                    class="relative"
                    x-data="{ dropdownOpen: false }"
                    @click.outside="dropdownOpen = false">
                    <a
                        class="flex items-center text-gray-700 dark:text-gray-400"
                        href="#"
                        @click.prevent="dropdownOpen = ! dropdownOpen">
                        <span class="mr-3 h-11 w-11 overflow-hidden rounded-full bg-brand-500 text-white flex items-center justify-center text-sm font-semibold">
                            {{ strtoupper(substr(auth()->user()->nama ?? auth()->user()->name, 0, 1)) }}
                        </span>

                        <span class="text-theme-sm mr-1 block font-medium"> {{ auth()->user()->username ?? auth()->user()->email }} </span>

                        <x-icons.chevron-down
                            x-bind:class="dropdownOpen && 'rotate-180'"
                            class="w-4 h-5 stroke-gray-500 dark:stroke-gray-400"
                        />
                    </a>

                    <!-- Dropdown Start -->
                    <div
                        x-show="dropdownOpen"
                        class="shadow-theme-lg dark:bg-gray-dark absolute right-0 mt-[17px] flex w-[260px] flex-col rounded-2xl border border-gray-200 bg-white p-3 dark:border-gray-800">
                        <div>
                            <span class="text-theme-sm block font-medium text-gray-700 dark:text-gray-400">
                                {{ auth()->user()->nama ?? auth()->user()->name }}
                            </span>
                        </div>

                        <ul class="flex flex-col gap-1 border-b border-gray-200 pt-4 pb-3 dark:border-gray-800">
                            <li>
                                <a
                                    href="{{ route('profile.index') }}"
                                    class="group text-theme-sm flex items-center gap-3 rounded-lg px-3 py-2 font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                                    <x-icons.user class="w-6 h-6 fill-gray-500 group-hover:fill-gray-700 dark:fill-gray-400 dark:group-hover:fill-gray-300" />
                                    Edit profil
                                </a>
                            </li>
                        </ul>
                        <form action="{{ route('logout') }}" method="post">
                            @csrf
                            <button
                                type="submit"
                                class="group text-theme-sm mt-3 flex items-center gap-3 rounded-lg px-3 py-2 font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300 w-full">
                                <x-icons.logout class="w-6 h-6 fill-gray-500 group-hover:fill-gray-700 dark:group-hover:fill-gray-300" />
                                Keluar
                            </button>
                        </form>
                    </div>
                    <!-- Dropdown End -->
                </div>
                <!-- User Area -->
            </div>
        </div>
    </div>
</header>

