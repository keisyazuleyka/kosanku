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
</head>
<body class="min-h-screen bg-[#F8FAFC] font-sans text-[#182230] antialiased">
    <div class="flex min-h-screen">
        <!-- Desktop Sidebar -->
        <aside class="hidden w-64 shrink-0 flex-col justify-between border-r border-[#E4E8EF] bg-white lg:flex">
            <div>
                <!-- Brand Logo -->
                <div class="flex h-16 items-center px-6 border-b border-[#E4E8EF]">
                    <a href="{{ route('home') }}" class="flex items-center">
                        <img src="{{ asset('images/kosanku-logo.png') }}" alt="Kosanku" class="h-9 w-auto object-contain">
                    </a>
                </div>

                <!-- Navigation Links -->
                <nav class="space-y-1 p-4 text-xs font-semibold">
                    @yield('sidebar')
                </nav>
            </div>

            <!-- Bottom User Profile Pill -->
            <div class="border-t border-[#E4E8EF] p-4">
                <a href="{{ route(auth()->user()->role->value . '.profile') }}" class="flex items-center gap-3 rounded-2xl p-2 transition hover:bg-[#EEF2FF]/60">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#EEF2FF] font-bold text-[#405BE8] border border-[#405BE8]/20 text-xs">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-xs font-bold text-[#182230]">{{ auth()->user()->name }}</p>
                        <p class="truncate text-[11px] text-[#667085]">{{ auth()->user()->role->label() }}</p>
                    </div>
                    <i data-lucide="chevron-right" class="h-4 w-4 text-[#98A2B3]"></i>
                </a>
            </div>
        </aside>

        <!-- Mobile Drawer Backdrop & Sidebar -->
        <div class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm lg:hidden hidden" id="mobile-sidebar-backdrop">
            <aside class="fixed inset-y-0 left-0 w-64 flex flex-col justify-between bg-white shadow-xl">
                <div>
                    <div class="flex h-16 items-center justify-between border-b border-[#E4E8EF] px-6">
                        <a href="{{ route('home') }}" class="flex items-center">
                            <img src="{{ asset('images/kosanku-logo.png') }}" alt="Kosanku" class="h-8.5 w-auto object-contain">
                        </a>
                        <button type="button" id="mobile-sidebar-close" class="text-[#667085] hover:text-[#182230]">
                            <i data-lucide="x" class="h-5 w-5"></i>
                        </button>
                    </div>
                    <nav class="space-y-1 p-4 text-xs font-semibold">
                        @yield('sidebar')
                    </nav>
                </div>
                <div class="border-t border-[#E4E8EF] p-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-2 rounded-xl px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50">
                            <i data-lucide="log-out" class="h-4 w-4"></i>
                            Keluar
                        </button>
                    </form>
                </div>
            </aside>
        </div>

        <!-- Main Column -->
        <div class="flex flex-1 flex-col min-w-0">
            <!-- Top Bar -->
            <header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-[#E4E8EF] bg-white px-4 lg:px-8">
                <div class="flex items-center gap-4 flex-1 max-w-lg">
                    <!-- Mobile Hamburger -->
                    <button type="button" id="mobile-sidebar-open" class="lg:hidden text-[#667085] hover:text-[#182230]">
                        <i data-lucide="menu" class="h-5 w-5"></i>
                    </button>

                    <!-- Search Bar -->
                    <div class="relative w-full hidden sm:block">
                        <i data-lucide="search" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#98A2B3]"></i>
                        <input type="text"
                               placeholder="Cari kost, pengguna, atau informasi..."
                               class="w-full rounded-xl border border-[#E4E8EF] bg-[#F8FAFC] py-1.5 pl-9 pr-3 text-xs text-[#182230] placeholder-[#98A2B3] outline-none transition focus:border-[#405BE8] focus:bg-white focus:ring-2 focus:ring-[#405BE8]/15">
                    </div>
                </div>

                <!-- Right Action Icons & Profile -->
                <div class="flex items-center gap-3">
                    <!-- Internal Chat Shortcut -->
                    <a href="{{ route('chat.index') }}" class="relative flex h-9 w-9 items-center justify-center rounded-xl text-[#667085] hover:bg-[#EEF2FF] hover:text-[#405BE8] transition" title="Pesan & Chat">
                        <i data-lucide="message-square" class="h-4 w-4"></i>
                        @php($unread = auth()->user()->unreadMessagesCount())
                        @if($unread > 0)
                            <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[9px] font-bold text-white">
                                {{ $unread > 9 ? '9+' : $unread }}
                            </span>
                        @endif
                    </a>

                    <!-- Notification Bell with Unread Badge -->
                    @php($unreadNotifs = auth()->user()->unreadNotifications->count())
                    <a href="{{ route('notifications.index') }}" class="relative flex h-9 w-9 items-center justify-center rounded-xl text-[#667085] hover:bg-[#EEF2FF] hover:text-[#405BE8] transition" title="Notifikasi">
                        <i data-lucide="bell" class="h-4 w-4"></i>
                        @if($unreadNotifs > 0)
                            <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-[#405BE8] text-[9px] font-bold text-white">
                                {{ $unreadNotifs > 9 ? '9+' : $unreadNotifs }}
                            </span>
                        @endif
                    </a>

                    <div class="h-5 w-px bg-[#E4E8EF]"></div>

                    <!-- Profile Dropdown -->
                    <div class="relative">
                        <button type="button" data-profile-toggle class="flex items-center gap-2.5 rounded-xl p-1.5 hover:bg-[#F8FAFC]">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-[#405BE8] text-xs font-bold text-white">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="hidden text-left sm:block">
                                <p class="text-xs font-semibold leading-tight text-[#182230]">{{ auth()->user()->name }}</p>
                                <p class="text-[10px] text-[#667085]">{{ auth()->user()->role->label() }}</p>
                            </div>
                            <i data-lucide="chevron-down" class="h-3.5 w-3.5 text-[#98A2B3]"></i>
                        </button>

                        <div data-profile-menu class="absolute right-0 mt-2 w-48 rounded-xl border border-[#E4E8EF] bg-white p-1.5 shadow-lg hidden z-30">
                            <a href="{{ route('home') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-medium text-[#182230] hover:bg-[#EEF2FF] hover:text-[#405BE8]">
                                <i data-lucide="external-link" class="h-3.5 w-3.5 text-[#667085]"></i>
                                Halaman Publik
                            </a>
                            <a href="{{ route('chat.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-medium text-[#182230] hover:bg-[#EEF2FF] hover:text-[#405BE8]">
                                <i data-lucide="message-square" class="h-3.5 w-3.5 text-[#667085]"></i>
                                Pesan & Chat
                            </a>
                            <a href="{{ route(auth()->user()->role->value . '.profile') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-medium text-[#182230] hover:bg-[#EEF2FF] hover:text-[#405BE8]">
                                <i data-lucide="user" class="h-3.5 w-3.5 text-[#667085]"></i>
                                Profil Saya
                            </a>
                            <div class="my-1 border-t border-[#E4E8EF]"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-xs font-medium text-rose-600 hover:bg-rose-50">
                                    <i data-lucide="log-out" class="h-3.5 w-3.5"></i>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Body -->
            <main class="flex-1 p-4 lg:p-8">
                <!-- Session Alerts -->
                @if (session('status'))
                    <div class="mb-6 flex items-center gap-2.5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-medium text-emerald-800">
                        <i data-lucide="check-circle" class="h-4 w-4 shrink-0 text-emerald-600"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 flex items-center gap-2.5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs font-medium text-rose-800">
                        <i data-lucide="alert-circle" class="h-4 w-4 shrink-0 text-rose-600"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 flex items-center gap-2.5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs font-medium text-rose-800">
                        <i data-lucide="alert-circle" class="h-4 w-4 shrink-0 text-rose-600"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <!-- Page Header -->
                @hasSection('heading')
                    <div class="mb-6">
                        <h1 class="text-xl font-bold tracking-tight text-[#182230] sm:text-2xl">@yield('heading')</h1>
                        @hasSection('subheading')
                            <p class="mt-1 text-xs text-[#667085]">@yield('subheading')</p>
                        @endif
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
