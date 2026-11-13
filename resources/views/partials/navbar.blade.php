@php($isHome = request()->routeIs('home'))
<header id="main-navbar"
        data-is-home="{{ $isHome ? 'true' : 'false' }}"
        class="{{ $isHome ? 'fixed top-0 inset-x-0 z-40 transition-all duration-300 bg-transparent border-b border-transparent text-white' : 'sticky top-0 z-40 border-b border-[#E4E8EF] bg-white/95 backdrop-blur-md text-[#182230]' }}">
    <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6">
        <!-- Brand Logo -->
        <a href="{{ route('home') }}" class="flex items-center shrink-0">
            <img src="{{ $isHome ? asset('images/kosanku-logo-darkmode.png') : asset('images/kosanku-logo.png') }}"
                 alt="Kosanku"
                 id="nav-logo-img"
                 data-light-src="{{ asset('images/kosanku-logo.png') }}"
                 data-dark-src="{{ asset('images/kosanku-logo-darkmode.png') }}"
                 class="h-9 sm:h-10 w-auto object-contain transition-all duration-300">
        </a>

        <!-- Desktop Navigation -->
        <nav id="nav-links" class="hidden items-center gap-7 text-xs font-semibold md:flex {{ $isHome ? 'text-white' : 'text-[#667085]' }}">
            <a href="{{ route('home') }}" id="nav-link-home" class="{{ $isHome ? 'text-white font-bold' : (request()->routeIs('home') ? 'text-[#405BE8]' : 'hover:text-[#182230]') }} transition">Beranda</a>
            <a href="{{ route('kost.index') }}" class="{{ request()->routeIs('kost.*') ? 'text-[#405BE8]' : ($isHome ? 'hover:text-white/80' : 'hover:text-[#182230]') }} transition">Kost</a>
            <a href="{{ route('pages.how') }}" class="{{ request()->routeIs('pages.how') ? 'text-[#405BE8]' : ($isHome ? 'hover:text-white/80' : 'hover:text-[#182230]') }} transition">Cara Kerja</a>
            <a href="{{ route('pages.terms') }}" class="{{ request()->routeIs('pages.terms') ? 'text-[#405BE8]' : ($isHome ? 'hover:text-white/80' : 'hover:text-[#182230]') }} transition">Tentang</a>
        </nav>

        <!-- Right CTA / Auth -->
        <div class="hidden items-center gap-3 md:flex">
            @auth
                <!-- Notification Bell Icon -->
                <a href="{{ route('notifications.index') }}" class="relative flex h-9 w-9 items-center justify-center rounded-xl {{ $isHome ? 'text-white hover:bg-white/10' : 'text-[#667085] hover:bg-[#EEF2FF] hover:text-[#405BE8]' }} transition" id="nav-notif-icon" title="Notifikasi">
                    <i data-lucide="bell" class="h-4 w-4"></i>
                    @php($unreadNotifs = auth()->user()->unreadNotifications->count())
                    @if($unreadNotifs > 0)
                        <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-[#405BE8] text-[9px] font-bold text-white">
                            {{ $unreadNotifs > 9 ? '9+' : $unreadNotifs }}
                        </span>
                    @endif
                </a>

                <!-- Chat Notification Icon -->
                <a href="{{ route('chat.index') }}" class="relative flex h-9 w-9 items-center justify-center rounded-xl {{ $isHome ? 'text-white hover:bg-white/10' : 'text-[#667085] hover:bg-[#EEF2FF] hover:text-[#405BE8]' }} transition" id="nav-chat-icon" title="Pesan Chat">
                    <i data-lucide="message-square" class="h-4 w-4"></i>
                    @php($unreadCount = auth()->user()->unreadMessagesCount())
                    @if($unreadCount > 0)
                        <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                    @endif
                </a>

                <a href="{{ route(auth()->user()->role->dashboardRoute()) }}" id="nav-dashboard-btn" class="{{ $isHome ? 'rounded-xl border border-white/30 bg-white/10 backdrop-blur-xs text-white hover:bg-white/20 text-xs py-2 px-3.5 flex items-center gap-1.5 transition' : 'btn-ghost text-xs py-2 px-3.5 flex items-center gap-1.5' }}">
                    <i data-lucide="layout-dashboard" class="h-3.5 w-3.5 {{ $isHome ? 'text-white' : 'text-[#405BE8]' }}"></i>
                    <span>Dashboard</span>
                </a>

                <!-- User avatar -->
                <div class="flex items-center gap-2">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#EEF2FF] font-bold text-xs text-[#405BE8] border border-[#405BE8]/20">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" id="nav-logout-btn" class="flex h-9 w-9 items-center justify-center rounded-xl {{ $isHome ? 'text-white hover:bg-white/15' : 'text-[#667085] hover:bg-rose-50 hover:text-rose-600' }} transition" title="Keluar">
                            <i data-lucide="log-out" class="h-4 w-4"></i>
                        </button>
                    </form>
                </div>
            @else
                <button type="button" id="nav-login-btn" onclick="window.openAuthModal('login')" class="text-xs font-semibold {{ $isHome ? 'text-white hover:text-white/80' : 'text-[#182230] hover:text-[#405BE8]' }} px-3 py-2 cursor-pointer transition">
                    Masuk
                </button>
                <button type="button" onclick="window.openAuthModal('register')" class="btn-primary text-xs py-2 px-4 rounded-xl font-semibold cursor-pointer shadow-md shadow-blue-600/20">
                    Daftar
                </button>
            @endauth
        </div>

        <!-- Mobile Menu Toggle -->
        <button id="nav-toggle-btn" class="md:hidden flex h-9 w-9 items-center justify-center rounded-xl {{ $isHome ? 'text-white hover:bg-white/15' : 'text-[#667085] hover:bg-[#EEF2FF]' }}" type="button" data-nav-toggle aria-label="Menu">
            <i data-lucide="menu" class="h-5 w-5"></i>
        </button>
    </div>

    <!-- Mobile Navigation Panel -->
    <div class="hidden border-t border-[#E4E8EF] bg-white px-4 py-4 md:hidden text-[#182230]" data-nav-panel>
        <div class="flex flex-col gap-3 text-xs font-semibold">
            <a href="{{ route('home') }}" class="py-1 text-[#182230]">Beranda</a>
            <a href="{{ route('kost.index') }}" class="py-1 text-[#182230]">Kost</a>
            <a href="{{ route('pages.how') }}" class="py-1 text-[#182230]">Cara Kerja</a>
            <a href="{{ route('pages.terms') }}" class="py-1 text-[#182230]">Tentang</a>
            <div class="border-t border-[#E4E8EF] pt-3 flex flex-col gap-2">
                @auth
                    <a href="{{ route('chat.index') }}" class="btn-ghost text-xs text-center">Chat & Pesan</a>
                    <a href="{{ route(auth()->user()->role->dashboardRoute()) }}" class="btn-primary text-xs text-center">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn-ghost w-full text-xs text-rose-600">Keluar</button>
                    </form>
                @else
                    <button type="button" onclick="window.openAuthModal('login')" class="btn-ghost text-xs text-center">Masuk</button>
                    <button type="button" onclick="window.openAuthModal('register')" class="btn-primary text-xs text-center">Daftar</button>
                @endauth
            </div>
        </div>
    </div>
</header>

@if($isHome)
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const navbar = document.getElementById('main-navbar');
        if (!navbar || navbar.dataset.isHome !== 'true') return;

        const navLinks = document.getElementById('nav-links');
        const loginBtn = document.getElementById('nav-login-btn');
        const logoImg = document.getElementById('nav-logo-img');
        const toggleBtn = document.getElementById('nav-toggle-btn');
        const activeLink = document.getElementById('nav-link-home');
        const notifIcon = document.getElementById('nav-notif-icon');
        const chatIcon = document.getElementById('nav-chat-icon');
        const dashBtn = document.getElementById('nav-dashboard-btn');
        const logoutBtn = document.getElementById('nav-logout-btn');

        const onScroll = () => {
            const isScrolled = window.scrollY > 40;
            if (isScrolled) {
                navbar.className = 'fixed top-0 inset-x-0 z-40 transition-all duration-300 bg-white/95 backdrop-blur-md border-b border-[#E4E8EF] shadow-xs text-[#182230]';
                navLinks?.classList.remove('text-white');
                navLinks?.classList.add('text-[#667085]');
                if (activeLink) {
                    activeLink.classList.remove('text-white');
                    activeLink.classList.add('text-[#405BE8]');
                }
                if (loginBtn) {
                    loginBtn.classList.remove('text-white');
                    loginBtn.classList.add('text-[#182230]');
                }
                if (logoImg && logoImg.dataset.lightSrc) {
                    logoImg.src = logoImg.dataset.lightSrc;
                }
                if (toggleBtn) {
                    toggleBtn.classList.remove('text-white');
                    toggleBtn.classList.add('text-[#667085]');
                }
                if (notifIcon) {
                    notifIcon.classList.remove('text-white', 'hover:bg-white/10');
                    notifIcon.classList.add('text-[#667085]', 'hover:bg-[#EEF2FF]', 'hover:text-[#405BE8]');
                }
                if (chatIcon) {
                    chatIcon.classList.remove('text-white', 'hover:bg-white/10');
                    chatIcon.classList.add('text-[#667085]', 'hover:bg-[#EEF2FF]', 'hover:text-[#405BE8]');
                }
                if (dashBtn) {
                    dashBtn.className = 'btn-ghost text-xs py-2 px-3.5 flex items-center gap-1.5';
                }
                if (logoutBtn) {
                    logoutBtn.classList.remove('text-white', 'hover:bg-white/15');
                    logoutBtn.classList.add('text-[#667085]', 'hover:bg-rose-50', 'hover:text-rose-600');
                }
            } else {
                navbar.className = 'fixed top-0 inset-x-0 z-40 transition-all duration-300 bg-transparent border-b border-transparent text-white';
                navLinks?.classList.remove('text-[#667085]');
                navLinks?.classList.add('text-white');
                if (activeLink) {
                    activeLink.classList.remove('text-[#405BE8]');
                    activeLink.classList.add('text-white');
                }
                if (loginBtn) {
                    loginBtn.classList.remove('text-[#182230]');
                    loginBtn.classList.add('text-white');
                }
                if (logoImg && logoImg.dataset.darkSrc) {
                    logoImg.src = logoImg.dataset.darkSrc;
                }
                if (toggleBtn) {
                    toggleBtn.classList.add('text-white');
                    toggleBtn.classList.remove('text-[#667085]');
                }
                if (notifIcon) {
                    notifIcon.classList.add('text-white', 'hover:bg-white/10');
                    notifIcon.classList.remove('text-[#667085]', 'hover:bg-[#EEF2FF]', 'hover:text-[#405BE8]');
                }
                if (chatIcon) {
                    chatIcon.classList.add('text-white', 'hover:bg-white/10');
                    chatIcon.classList.remove('text-[#667085]', 'hover:bg-[#EEF2FF]', 'hover:text-[#405BE8]');
                }
                if (dashBtn) {
                    dashBtn.className = 'rounded-xl border border-white/30 bg-white/10 backdrop-blur-xs text-white hover:bg-white/20 text-xs py-2 px-3.5 flex items-center gap-1.5 transition';
                }
                if (logoutBtn) {
                    logoutBtn.classList.add('text-white', 'hover:bg-white/15');
                    logoutBtn.classList.remove('text-[#667085]', 'hover:bg-rose-50', 'hover:text-rose-600');
                }
            }
        };

        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    });
</script>
@endif
