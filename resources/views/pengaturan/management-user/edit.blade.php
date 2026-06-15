@extends('layouts.app')

@section('content')

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <form method="post" action="{{ route('pengaturan.user.update', $data->id) }}">
        @csrf @method('PUT')
        <div class="p-5">
            @if ($errors->any())
            <div class="mb-4 rounded-lg border-l-4 border-l-error-500 bg-error-50 p-4 dark:bg-error-500/15">
                <ul class="mb-0 list-disc ps-4 text-sm text-red-800 dark:text-red-200">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 pt-3 pb-3">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nama</label>
                    <input type="text" name="nama" value="{{ old('nama', $data->nama) }}" required
                        class="h-11 w-full rounded-lg border dark:bg-dark-900 border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Username</label>
                    <input type="text" name="username" value="{{ old('username', $data->username) }}" required
                        class="h-11 w-full rounded-lg border dark:bg-dark-900 border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Email</label>
                    <input type="email" name="email" value="{{ old('email', $data->email) }}"
                        class="h-11 w-full rounded-lg border dark:bg-dark-900 border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Kosongkan untuk auto (username@gudangjateng.com)</p>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Password Baru</label>
                    <div class="relative">
                        <input type="password" name="password"
                            class="password-field h-11 w-full rounded-lg border dark:bg-dark-900 border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
                        <button type="button" class="toggle-password absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="eye-open" width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 4.5C4.5 4.5 1 10 1 10C1 10 4.5 15.5 10 15.5C15.5 15.5 19 10 19 10C19 10 15.5 4.5 10 4.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><circle cx="10" cy="10" r="3" stroke="currentColor" stroke-width="1.5"/></svg>
                            <svg class="eye-closed hidden" width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2 2L18 18M7 7C7.456 6.522 8.182 6 10 6C13 6 15.5 8.5 17.5 10C16.5 11.5 15.364 12.636 14 13.5M7 7L3.5 10.5L7 14M7 7L10.5 10.5M13 13C12.5 13.5 11.5 14 10 14C8 14 6 12 5 10C5.5 9 6.5 8 7.5 7.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                        </button>
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Kosongkan jika tidak ingin mengganti password</p>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Role</label>
                    <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                        <select name="role" required
                            class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                            x-bind:class="isOptionSelected && 'text-gray-800 dark:text-white/90'"
                            @change="isOptionSelected = true">
                            <option value="admin" {{ old('role', $data->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="sales" {{ old('role', $data->role) === 'sales' ? 'selected' : '' }}>Sales</option>
                            <option value="audit" {{ old('role', $data->role) === 'audit' ? 'selected' : '' }}>Audit</option>
                            <option value="manager" {{ old('role', $data->role) === 'manager' ? 'selected' : '' }}>Manager</option>
                            <option value="super_admin" {{ old('role', $data->role) === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                        </select>
                        <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                            <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-between px-5 py-4">
            <a href="{{ route('pengaturan.user.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs ring-1 ring-inset ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.8334 10H4.16669M4.16669 10L9.16669 15M4.16669 10L9.16669 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Kembali
            </a>
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.8334 8.33333V15.8333C15.8334 16.7538 15.0872 17.5 14.1667 17.5H5.83341C4.91294 17.5 4.16675 16.7538 4.16675 15.8333V4.16667C4.16675 3.24619 4.91294 2.5 5.83341 2.5H11.6667M15.8334 8.33333L11.6667 4.16667M15.8334 8.33333H11.6667V4.16667M7.50008 12.5H10.0001M7.50008 15H12.5001" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Update User
            </button>
        </div>    </form>
</div>
@endsection

@push('scripts')
<script>
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
</script>
@endpush
