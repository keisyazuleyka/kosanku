<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', setting('site_name', 'Kosanku'))</title>

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
<body class="min-h-screen bg-white font-sans text-[#182230] antialiased">
    <div class="grid min-h-screen lg:grid-cols-2">
        <!-- Left Brand Hero Column (Desktop) -->
        <div class="hidden bg-[#405BE8] p-12 text-white lg:flex lg:flex-col lg:justify-between relative overflow-hidden">
            <!-- Background Decorative Blobs -->
            <div class="absolute -top-24 -left-24 h-96 w-96 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -bottom-24 -right-24 h-96 w-96 rounded-full bg-black/10 blur-3xl"></div>

            <div class="relative z-10">
                <a href="{{ route('home') }}" class="inline-block">
                    <img src="{{ asset('images/kosanku-logo-white.png') }}" alt="Kosanku" class="h-10 w-auto object-contain">
                </a>
            </div>

            <div class="relative z-10 max-w-md">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3.5 py-1 text-xs font-semibold text-white backdrop-blur">
                    <i data-lucide="sparkles" class="h-3.5 w-3.5"></i>
                    Modern Kost Living
                </span>
                <h1 class="mt-4 text-4xl font-black leading-tight tracking-tight text-white">
                    Find your space. Feel at home.
                </h1>
                <p class="mt-4 text-sm text-blue-100 leading-relaxed">
                    Satu akun untuk kemudahan mencari tempat tinggal yang nyaman, terverifikasi, dan aman bagi mahasiswa & pekerja di seluruh Indonesia.
                </p>

                <!-- Feature bullet items -->
                <div class="mt-8 space-y-3">
                    <div class="flex items-center gap-3 text-xs text-white/90">
                        <div class="flex h-6 w-6 items-center justify-center rounded-full bg-white/20">
                            <i data-lucide="check" class="h-3.5 w-3.5"></i>
                        </div>
                        <span>100% Listing kost telah diverifikasi keasliannya</span>
                    </div>
                    <div class="flex items-center gap-3 text-xs text-white/90">
                        <div class="flex h-6 w-6 items-center justify-center rounded-full bg-white/20">
                            <i data-lucide="check" class="h-3.5 w-3.5"></i>
                        </div>
                        <span>Pengajuan sewa online tanpa biaya tersembunyi</span>
                    </div>
                    <div class="flex items-center gap-3 text-xs text-white/90">
                        <div class="flex h-6 w-6 items-center justify-center rounded-full bg-white/20">
                            <i data-lucide="check" class="h-3.5 w-3.5"></i>
                        </div>
                        <span>Komunikasi langsung dengan pemilik kost via fitur chat</span>
                    </div>
                </div>
            </div>

            <div class="relative z-10 text-xs text-blue-200">
                &copy; {{ date('Y') }} Kosanku Indonesia · @kosanku
            </div>
        </div>

        <!-- Right Form Column -->
        <div class="flex items-center justify-center bg-[#F8FAFC] p-6 sm:p-12">
            <div class="w-full max-w-md">
                <!-- Mobile Logo Header -->
                <div class="mb-6 lg:hidden text-center">
                    <a href="{{ route('home') }}" class="inline-block">
                        <img src="{{ asset('images/kosanku-logo.png') }}" alt="Kosanku" class="h-9 w-auto object-contain mx-auto">
                    </a>
                </div>

                <!-- Content Slot -->
                <div class="rounded-3xl border border-[#E4E8EF] bg-white p-8 shadow-sm">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
</body>
</html>
