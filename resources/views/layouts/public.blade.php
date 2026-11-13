<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', setting('site_name', 'Kosanku'))</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen bg-white font-sans text-[#182230] antialiased selection:bg-[#405BE8]/15 selection:text-[#405BE8]">
    @include('partials.navbar')

    <main>
        @if (session('status'))
            <div class="mx-auto max-w-6xl px-4 {{ request()->routeIs('home') ? 'pt-20' : 'mt-4' }}">
                <div class="flex items-center gap-2.5 rounded-xl border border-emerald-200/80 bg-emerald-50 px-4 py-3 text-xs font-medium text-emerald-800 shadow-sm">
                    <i data-lucide="check-circle" class="h-4 w-4 shrink-0 text-emerald-600"></i>
                    <span>{{ session('status') }}</span>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mx-auto max-w-6xl px-4 {{ request()->routeIs('home') ? 'pt-20' : 'mt-4' }}">
                <div class="flex items-center gap-2.5 rounded-xl border border-rose-200/80 bg-rose-50 px-4 py-3 text-xs font-medium text-rose-800 shadow-sm">
                    <i data-lucide="alert-circle" class="h-4 w-4 shrink-0 text-rose-600"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    @include('partials.footer')

    @guest
        @include('partials.auth-modal')
    @endguest
</body>
</html>
