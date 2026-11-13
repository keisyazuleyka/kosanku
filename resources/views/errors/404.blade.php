<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Halaman Tidak Ditemukan (404) | {{ setting('site_name', 'Kosanku') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-[#F5F6F8] p-4 font-sans text-[#18181B] antialiased">
    <div class="w-full max-w-md rounded-2xl border border-[#E4E4E7] bg-white p-8 text-center shadow-lg">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-[#EFF2FE] text-[#4263EB] shadow-inner">
            <i data-lucide="compass" class="h-8 w-8"></i>
        </div>
        <p class="mt-5 text-sm font-bold tracking-widest text-[#4263EB] uppercase">Error 404</p>
        <h1 class="mt-2 text-2xl font-black text-[#18181B]">Halaman Tidak Ditemukan</h1>
        <p class="mt-3 text-xs leading-relaxed text-[#71717A]">
            Halaman atau kost yang kamu cari mungkin sudah dihapus, diubah tautannya, atau sementara tidak tersedia.
        </p>
        <div class="mt-6 flex flex-col gap-2.5">
            <a href="{{ url('/') }}" class="btn-primary w-full py-2.5 text-xs font-semibold">
                Kembali ke Beranda
            </a>
            <a href="{{ route('kost.index') }}" class="btn-ghost w-full py-2.5 text-xs font-semibold">
                Cari Kost Tersedia
            </a>
        </div>
    </div>
</body>
</html>
