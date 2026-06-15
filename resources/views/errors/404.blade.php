<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 Error Page | GudangJateng</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-white dark:bg-gray-900 font-inter antialiased">

  <div class="relative z-1 flex min-h-screen flex-col items-center justify-center overflow-hidden px-6 py-16">

    <!-- Centered Content -->
    <div class="mx-auto w-full max-w-[242px] text-center sm:max-w-[472px]">

      <h1 class="mb-10 text-xl font-semibold text-gray-800 dark:text-white/90">
        halaman yang Anda cari tidak ditemukan !
      </h1>

      <img src="{{ asset('images/404.svg') }}" alt="404" class="mx-auto block mb-10">


      <a href="{{ url('/') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs ring-1 ring-inset ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
        Kembali ke Beranda
      </a>
    </div>

    <!-- Footer -->
    <p class="absolute bottom-6 left-1/2 -translate-x-1/2 text-center text-sm text-gray-500 dark:text-gray-400">
      &copy; <span id="year">2026</span> - GudangJateng
    </p>
  </div>

</body>
</html>
