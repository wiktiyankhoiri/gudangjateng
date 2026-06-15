@extends('auth.layout')

@section('content')

<div>
    <div class="mb-5 sm:mb-8">
        <h1 class="mb-2 font-semibold text-gray-800 text-title-sm dark:text-white/90 sm:text-title-md">
            Masuk
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Masukkan username dan password untuk melanjutkan ke dashboard
        </p>
    </div>

    @if(session()->has('error'))
    <div class="mb-4 rounded-lg border-l-4 border-l-error-500 bg-error-50 p-4 dark:bg-error-500/15">
        <p class="text-sm text-error-800 dark:text-error-200">{{ session('error') }}</p>
    </div>
    @endif

    @if(session()->has('success'))
    <div class="mb-4 rounded-lg border-l-4 border-l-success-500 bg-success-50 p-4 dark:bg-success-500/15">
        <p class="text-sm text-success-800 dark:text-success-200">{{ session('success') }}</p>
    </div>
    @endif

    @if ($errors->any())
    <div class="mb-4 rounded-lg border-l-4 border-l-error-500 bg-error-50 p-4 dark:bg-error-500/15">
        <ul class="text-sm text-error-800 dark:text-error-200 list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="post" action="{{ route('login.submit') }}">
        @csrf
        <div class="space-y-5">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400" for="username">
                    Username
                </label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Masukkan username"
                    value="{{ old('username') }}"
                    required
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800 @error('username') border-error-500 focus:border-error-500 focus:ring-error-500/10 @enderror"
                />
                @error('username')
                    <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400" for="password">
                    Password
                </label>
                <div class="relative">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Masukkan password"
                        required
                        class="password-field dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800 @error('password') border-error-500 focus:border-error-500 focus:ring-error-500/10 @enderror"
                    />
                    <button type="button" class="toggle-password absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="eye-open" width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 4.5C4.5 4.5 1 10 1 10C1 10 4.5 15.5 10 15.5C15.5 15.5 19 10 19 10C19 10 15.5 4.5 10 4.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><circle cx="10" cy="10" r="3" stroke="currentColor" stroke-width="1.5"/></svg>
                        <svg class="eye-closed hidden" width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2 2L18 18M7 7C7.456 6.522 8.182 6 10 6C13 6 15.5 8.5 17.5 10C16.5 11.5 15.364 12.636 14 13.5M7 7L3.5 10.5L7 14M7 7L10.5 10.5M13 13C12.5 13.5 11.5 14 10 14C8 14 6 12 5 10C5.5 9 6.5 8 7.5 7.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <button
                    type="submit"
                    class="flex items-center justify-center w-full px-4 py-3 text-sm font-medium text-white transition rounded-lg bg-brand-500 shadow-theme-xs hover:bg-brand-600"
                >
                    Masuk
                </button>
            </div>
            <div class="text-center">
                <a href="{{ route('password.request') }}" class="text-sm text-brand-500 hover:text-brand-600 font-medium">
                    Lupa Password?
                </a>
            </div>
        </div>
    </form>

</div>

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

@endsection
