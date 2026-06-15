@extends('auth.layout')

@section('content')

<div>
    <div class="mb-5 sm:mb-8">
        <h1 class="mb-2 font-semibold text-gray-800 text-title-sm dark:text-white/90 sm:text-title-md">
            Reset Password
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Masukkan password baru Anda
        </p>
    </div>

    @if(session()->has('error'))
    <div class="mb-4 rounded-lg border-l-4 border-l-error-500 bg-error-50 p-4 dark:bg-error-500/15">
        <p class="text-sm text-error-800 dark:text-error-200">{{ session('error') }}</p>
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

    <form method="post" action="{{ route('password.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="space-y-5">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400" for="email">
                    Email
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Masukkan email"
                    value="{{ $email }}"
                    required
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800 @error('email') border-error-500 focus:border-error-500 focus:ring-error-500/10 @enderror"
                />
                @error('email')
                    <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400" for="password">
                    Password Baru
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Masukkan password baru"
                    required
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800 @error('password') border-error-500 focus:border-error-500 focus:ring-error-500/10 @enderror"
                />
                @error('password')
                    <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400" for="password_confirmation">
                    Konfirmasi Password
                </label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    placeholder="Masukkan ulang password baru"
                    required
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                />
            </div>
            <div>
                <button
                    type="submit"
                    class="flex items-center justify-center w-full px-4 py-3 text-sm font-medium text-white transition rounded-lg bg-brand-500 shadow-theme-xs hover:bg-brand-600"
                >
                    Reset Password
                </button>
            </div>
        </div>
    </form>

    <div class="mt-6 text-center">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            <a href="{{ route('login') }}" class="text-brand-500 hover:text-brand-600 font-medium">Kembali ke Login</a>
        </p>
    </div>
</div>

@endsection
