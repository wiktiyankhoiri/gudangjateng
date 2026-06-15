@extends('layouts.app')

@section('content')

<div x-data="{ isProfileInfoModal: false }" class="mx-auto max-w-(--breakpoint-2xl)">

    <!-- Profile -->
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">

        <!-- Profile Header Card -->
        <div class="mb-6 rounded-2xl border border-gray-200 p-5 dark:border-gray-800 lg:p-6">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
                <div class="flex w-full flex-col items-center gap-6 xl:flex-row">
                    <div class="flex h-20 w-20 items-center justify-center overflow-hidden rounded-full border border-gray-200 bg-brand-500 dark:border-gray-800">
                        <span class="text-3xl font-bold text-white">{{ strtoupper(substr($user->nama ?? $user->name, 0, 1)) }}</span>
                    </div>
                    <div class="order-3 xl:order-2">
                        <h4 class="mb-2 text-center text-lg font-semibold text-gray-800 dark:text-white/90 xl:text-left">{{ $user->nama ?? $user->name }}</h4>
                        <div class="flex flex-col items-center gap-1 text-center xl:flex-row xl:gap-3 xl:text-left">
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ ucfirst($user->role) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informasi Akun -->
        <div class="mb-6 rounded-2xl border border-gray-200 p-5 dark:border-gray-800 lg:p-6">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90 lg:mb-6">Informasi Akun</h4>
                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 lg:gap-7 2xl:gap-x-32">
                        <div>
                            <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Nama Lengkap</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $user->nama ?? $user->name }}</p>
                        </div>
                        <div>
                            <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Username</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $user->username }}</p>
                        </div>
                        <div>
                            <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Email</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $user->email ?? '-' }}</p>
                        </div>
                    </div>
                </div>
                <button @click="isProfileInfoModal = true" class="flex w-full items-center justify-center gap-2 rounded-full border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200 lg:inline-flex lg:w-auto">
                    <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z" fill="currentColor"/></svg>
                    Edit
                </button>
            </div>
        </div>

    </div>

    <!-- MODAL: Edit Informasi Akun -->
    <div x-show="isProfileInfoModal" class="fixed inset-0 z-99999 flex items-center justify-center overflow-y-auto p-5">
        <div class="fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]"></div>
        <div @click.outside="isProfileInfoModal = false" class="no-scrollbar relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11">
            <button @click="isProfileInfoModal = false" type="button" class="transition-color absolute right-5 top-5 z-999 flex h-11 w-11 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-600 dark:bg-gray-700 dark:bg-white/[0.05] dark:text-gray-400 dark:hover:bg-white/[0.07] dark:hover:text-gray-300">
                <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.04289 16.5418C5.65237 16.9323 5.65237 17.5655 6.04289 17.956C6.43342 18.3465 7.06658 18.3465 7.45711 17.956L11.9987 13.4144L16.5408 17.9565C16.9313 18.347 17.5645 18.347 17.955 17.9565C18.3455 17.566 18.3455 16.9328 17.955 16.5423L13.4129 12.0002L17.955 7.45808C18.3455 7.06756 18.3455 6.43439 17.955 6.04387C17.5645 5.65335 16.9313 5.65335 16.5408 6.04387L11.9987 10.586L7.45711 6.04439C7.06658 5.65386 6.43342 5.65386 6.04289 6.04439C5.65237 6.43491 5.65237 7.06808 6.04289 7.4586L10.5845 12.0002L6.04289 16.5418Z" fill="currentColor"/></svg>
            </button>

            <div class="px-2 pr-14">
                <h4 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90">Edit Informasi Akun</h4>
                <p class="mb-6 text-sm text-gray-500 dark:text-gray-400 lg:mb-7">Perbarui detail Anda untuk menjaga profil tetap terkini.</p>
            </div>

            <form id="formProfile" method="post" action="{{ route('profile.update') }}" class="flex flex-col">
                @csrf
                <div class="custom-scrollbar h-[450px] overflow-y-auto px-2">
                    <div>
                        @if ($errors->any())
                        <div class="mb-4 rounded-lg bg-error-50 p-4 dark:bg-error-500/15">
                            <ul class="mb-0 list-disc ps-4 text-sm text-red-800 dark:text-red-200">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        @if(session('success'))
                        <div class="mb-4 rounded-lg bg-success-50 p-4 dark:bg-success-500/15">
                            <p class="text-sm text-green-800 dark:text-green-200">{{ session('success') }}</p>
                        </div>
                        @endif

                        <h5 class="mb-5 text-lg font-medium text-gray-800 dark:text-white/90 lg:mb-6">Informasi Akun</h5>
                        <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nama Lengkap</label>
                                <input type="text" name="nama" value="{{ old('nama', $user->nama) }}"
                                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Username</label>
                                <input type="text" name="username" value="{{ old('username', $user->username) }}"
                                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Email</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
                            </div>
                        </div>
                    </div>

                    <div class="mt-7">
                        <h5 class="mb-5 text-lg font-medium text-gray-800 dark:text-white/90 lg:mb-6">Ubah Password</h5>

                        <div class="mb-5">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Password Lama</label>
                            <div class="relative">
                                <input type="password" name="password_lama" class="password-field dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" placeholder="Password saat ini">
                                <button type="button" class="toggle-password absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <svg class="eye-open" width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 4.5C4.5 4.5 1 10 1 10C1 10 4.5 15.5 10 15.5C15.5 15.5 19 10 19 10C19 10 15.5 4.5 10 4.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><circle cx="10" cy="10" r="3" stroke="currentColor" stroke-width="1.5"/></svg>
                                    <svg class="eye-closed hidden" width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2 2L18 18M7 7C7.456 6.522 8.182 6 10 6C13 6 15.5 8.5 17.5 10C16.5 11.5 15.364 12.636 14 13.5M7 7L3.5 10.5L7 14M7 7L10.5 10.5M13 13C12.5 13.5 11.5 14 10 14C8 14 6 12 5 10C5.5 9 6.5 8 7.5 7.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Password Baru</label>
                                <div class="relative">
                                    <input type="password" name="password_baru" class="password-field dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" placeholder="Password baru">
                                    <button type="button" class="toggle-password absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                        <svg class="eye-open" width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 4.5C4.5 4.5 1 10 1 10C1 10 4.5 15.5 10 15.5C15.5 15.5 19 10 19 10C19 10 15.5 4.5 10 4.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><circle cx="10" cy="10" r="3" stroke="currentColor" stroke-width="1.5"/></svg>
                                        <svg class="eye-closed hidden" width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2 2L18 18M7 7C7.456 6.522 8.182 6 10 6C13 6 15.5 8.5 17.5 10C16.5 11.5 15.364 12.636 14 13.5M7 7L3.5 10.5L7 14M7 7L10.5 10.5M13 13C12.5 13.5 11.5 14 10 14C8 14 6 12 5 10C5.5 9 6.5 8 7.5 7.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Konfirmasi</label>
                                <div class="relative">
                                    <input type="password" name="konfirmasi_password" class="password-field dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" placeholder="Ulangi password baru">
                                    <button type="button" class="toggle-password absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                        <svg class="eye-open" width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 4.5C4.5 4.5 1 10 1 10C1 10 4.5 15.5 10 15.5C15.5 15.5 19 10 19 10C19 10 15.5 4.5 10 4.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><circle cx="10" cy="10" r="3" stroke="currentColor" stroke-width="1.5"/></svg>
                                        <svg class="eye-closed hidden" width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2 2L18 18M7 7C7.456 6.522 8.182 6 10 6C13 6 15.5 8.5 17.5 10C16.5 11.5 15.364 12.636 14 13.5M7 7L3.5 10.5L7 14M7 7L10.5 10.5M13 13C12.5 13.5 11.5 14 10 14C8 14 6 12 5 10C5.5 9 6.5 8 7.5 7.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-600 dark:border-blue-800 dark:bg-blue-500/10 dark:text-blue-400">
                            <svg class="inline-block mr-1.5 -mt-0.5" width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="10" cy="10" r="7.5" stroke="currentColor" stroke-width="1.5"/><path d="M10 6.5V10.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><circle cx="10" cy="13.5" r="0.5" fill="currentColor"/></svg>
                            Kosongkan field password jika tidak ingin mengganti password.
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center gap-3 px-2 lg:justify-end">
                    <button @click="isProfileInfoModal = false" type="button" class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto">Tutup</button>
                    <button type="submit" class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">Simpan</button>
                </div>
            </form>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.toggle-password').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var input = this.closest('.relative').querySelector('.password-field');
            var open = this.querySelector('.eye-open');
            var closed = this.querySelector('.eye-closed');
            if (input.type === 'password') {
                input.type = 'text';
                open.classList.add('hidden');
                closed.classList.remove('hidden');
            } else {
                input.type = 'password';
                closed.classList.add('hidden');
                open.classList.remove('hidden');
            }
        });
    });
});
</script>
@endpush
