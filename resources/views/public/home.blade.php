@extends('layouts.public')
@section('title', setting('site_name', 'Kosanku') . ' | ' . setting('tagline', 'Find your space. Feel at home.'))

@section('content')
<!-- Hero Section with Full-Bleed Background, Vignette & Fade Filter -->
<section class="relative w-full overflow-hidden min-h-[620px] sm:min-h-[680px] lg:min-h-[760px] flex items-center bg-[#0C1322]">
    <!-- Background Hero Image -->
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?auto=format&fit=crop&w=2000&q=80"
             alt="Kamar Kost Modern Kosanku"
             class="h-full w-full object-cover object-center scale-100 transform motion-safe:transition-transform duration-1000">

        <!-- Left-to-Right Seamless Dark Gradient: Deep tone on the left for maximum text readability, transitioning smoothly in the middle, and 100% transparent on the right to preserve vibrant colors and original photo details -->
        <div class="absolute inset-0 pointer-events-none"
             style="background: linear-gradient(to right, rgba(10, 15, 28, 0.88) 0%, rgba(10, 15, 28, 0.78) 30%, rgba(10, 15, 28, 0.40) 52%, rgba(10, 15, 28, 0.08) 72%, transparent 88%);"></div>

        <!-- Top Subtle Soft Vignette for Floating Navbar Readability -->
        <div class="absolute inset-x-0 top-0 h-24 pointer-events-none"
             style="background: linear-gradient(to bottom, rgba(10, 15, 28, 0.50) 0%, rgba(10, 15, 28, 0.10) 65%, transparent 100%);"></div>

        <!-- Clean Soft Bottom Fade (No white fog, preserving natural photo detail) -->
        <div class="absolute inset-x-0 bottom-0 h-12 sm:h-16 pointer-events-none z-10"
             style="background: linear-gradient(to top, #ffffff 0%, rgba(255, 255, 255, 0.25) 50%, transparent 100%);"></div>
    </div>

    <!-- Hero Content -->
    <div class="relative z-20 mx-auto max-w-6xl w-full px-4 sm:px-6 pt-28 pb-20 sm:pt-36 sm:pb-32">
        <div class="max-w-2xl">
            <!-- Pill Badge matching reference -->
            <span class="inline-flex items-center gap-1.5 rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-xs font-semibold text-white backdrop-blur-md shadow-sm">
                <i data-lucide="sparkles" class="h-3.5 w-3.5 text-[#405BE8]"></i>
                <span>#1 Platform Kost Mahasiswa & Pekerja</span>
            </span>

            <!-- Heading matching reference -->
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl sm:leading-[1.15]">
                Cari Kost<br>Impianmu di Sini
            </h1>

            <!-- Subtitle matching reference -->
            <p class="mt-4 text-sm sm:text-base text-zinc-200/90 leading-relaxed font-normal max-w-xl">
                Temukan kost terbaik dengan harga terjangkau, aman dan nyaman. Cocok untuk mahasiswa & pekerja di seluruh Indonesia.
            </p>

            <!-- Interactive 3-Method Search Widget -->
            <div class="mt-8 max-w-2xl">
                <!-- Search Method Tabs -->
                <div class="inline-flex p-1 rounded-2xl bg-black/30 backdrop-blur-md border border-white/15 mb-3 shadow-lg gap-1">
                    <button type="button"
                            id="tab-btn-area"
                            onclick="switchSearchTab('area')"
                            class="flex items-center gap-1.5 px-3.5 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition cursor-pointer bg-[#405BE8] text-white shadow-md">
                        <i data-lucide="search" class="h-4 w-4"></i>
                        <span>Cari Kost</span>
                    </button>
                    <button type="button"
                            id="tab-btn-campus"
                            onclick="switchSearchTab('campus')"
                            class="flex items-center gap-1.5 px-3.5 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition cursor-pointer text-white/80 hover:text-white">
                        <i data-lucide="graduation-cap" class="h-4 w-4"></i>
                        <span>Dekat Kampus</span>
                    </button>
                    <button type="button"
                            id="tab-btn-nearby"
                            onclick="switchSearchTab('nearby')"
                            class="flex items-center gap-1.5 px-3.5 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition cursor-pointer text-white/80 hover:text-white">
                        <i data-lucide="navigation" class="h-4 w-4"></i>
                        <span>Sekitar Saya</span>
                    </button>
                </div>

                <!-- METHOD 1 (DEFAULT): Cari Berdasarkan Kota / Area / Teks Bebas (Matches Mockup 1:1) -->
                <form id="form-search-area" action="{{ route('kost.index') }}" method="GET"
                      class="rounded-3xl sm:rounded-full border border-white/25 bg-white/15 p-2 sm:p-2.5 backdrop-blur-md shadow-2xl flex flex-col sm:flex-row items-center gap-2 sm:gap-3 relative">
                    
                    <!-- Free Text Input with Autocomplete -->
                    <div class="flex-1 w-full relative flex items-center pl-4 pr-2">
                        <i data-lucide="map-pin" class="h-4 w-4 text-white/80 shrink-0"></i>
                        <input type="text"
                               name="city"
                               id="home-location-input"
                               placeholder="Ketik kota, kecamatan, atau kelurahan"
                               autocomplete="off"
                               oninput="handleLocationInput(this.value)"
                               onfocus="showLocationSuggestions()"
                               class="w-full bg-transparent pl-3 pr-2 py-2 text-xs sm:text-sm font-medium text-white placeholder-white/75 outline-none">

                        <!-- Autocomplete Suggestions Dropdown -->
                        <div id="location-suggestions" class="hidden absolute left-0 right-0 top-full mt-3 rounded-2xl border border-[#E4E8EF] bg-white shadow-2xl z-50 overflow-hidden text-xs max-h-56 overflow-y-auto p-1.5 text-[#182230]">
                            <!-- Populated by JS -->
                        </div>
                    </div>

                    <div class="hidden sm:block h-7 w-px bg-white/25 shrink-0"></div>

                    <!-- Type Selector -->
                    <div class="w-full sm:w-44 relative flex items-center px-3">
                        <i data-lucide="home" class="h-4 w-4 text-white/80 shrink-0"></i>
                        <select name="type" class="w-full bg-transparent pl-2.5 pr-6 py-2 text-xs sm:text-sm font-medium text-white outline-none cursor-pointer appearance-none">
                            <option value="" class="text-[#182230] bg-white">Semua Tipe</option>
                            <option value="putra" class="text-[#182230] bg-white">Putra</option>
                            <option value="putri" class="text-[#182230] bg-white">Putri</option>
                            <option value="campur" class="text-[#182230] bg-white">Campur</option>
                        </select>
                        <i data-lucide="chevron-down" class="pointer-events-none absolute right-3 h-3.5 w-3.5 text-white/80"></i>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                            class="w-full sm:w-auto rounded-full bg-[#405BE8] hover:bg-[#3049C9] text-white py-2.5 sm:py-3 px-6 sm:px-7 text-xs sm:text-sm font-semibold shadow-lg shadow-blue-600/30 flex items-center justify-center gap-2 cursor-pointer transition shrink-0">
                        <i data-lucide="search" class="h-4 w-4 text-white"></i>
                        <span>Cari Kost</span>
                    </button>
                </form>

                <!-- METHOD 2: Cari Dekat Kampus (Strict City -> Campus -> Radius) -->
                <form id="form-search-campus" action="{{ route('kost.index') }}" method="GET"
                      class="hidden rounded-3xl sm:rounded-full border border-white/25 bg-white/15 p-2 sm:p-2.5 backdrop-blur-md shadow-2xl flex flex-col sm:flex-row items-center gap-2 sm:gap-3 relative">
                    
                    <!-- 1. Kota Dulu -->
                    <div class="flex-1 w-full relative flex items-center pl-4 pr-2">
                        <i data-lucide="map-pin" class="h-4 w-4 text-white/80 shrink-0"></i>
                        <select name="city" id="hero-campus-city" onchange="loadHeroCampuses(this.value)" required
                                class="w-full bg-transparent pl-2.5 pr-6 py-2 text-xs sm:text-sm font-medium text-white outline-none cursor-pointer appearance-none">
                            <option value="" class="text-[#182230] bg-white">1. Pilih Kota / Daerah...</option>
                            @foreach($cities as $city)
                                <option value="{{ $city }}" class="text-[#182230] bg-white">{{ $city }}</option>
                            @endforeach
                        </select>
                        <i data-lucide="chevron-down" class="pointer-events-none absolute right-3 h-3.5 w-3.5 text-white/80"></i>
                    </div>

                    <div class="hidden sm:block h-7 w-px bg-white/25 shrink-0"></div>

                    <!-- 2. Kampus Dinamis -->
                    <div class="flex-1 w-full relative flex items-center px-3">
                        <i data-lucide="graduation-cap" class="h-4 w-4 text-white/80 shrink-0"></i>
                        <select name="campus_id" id="hero-campus-select" disabled required
                                class="w-full bg-transparent pl-2.5 pr-6 py-2 text-xs sm:text-sm font-medium text-white outline-none cursor-pointer appearance-none disabled:opacity-60 disabled:cursor-not-allowed">
                            <option value="" class="text-[#182230] bg-white">2. Pilih kota terlebih dahulu</option>
                        </select>
                        <i data-lucide="chevron-down" class="pointer-events-none absolute right-3 h-3.5 w-3.5 text-white/80"></i>
                    </div>

                    <div class="hidden sm:block h-7 w-px bg-white/25 shrink-0"></div>

                    <!-- 3. Radius -->
                    <div class="w-full sm:w-28 relative flex items-center px-3">
                        <select name="radius" class="w-full bg-transparent pl-1 pr-5 py-2 text-xs sm:text-sm font-medium text-white outline-none cursor-pointer appearance-none">
                            <option value="1" class="text-[#182230] bg-white">1 km</option>
                            <option value="3" class="text-[#182230] bg-white">3 km</option>
                            <option value="5" selected class="text-[#182230] bg-white">5 km</option>
                            <option value="10" class="text-[#182230] bg-white">10 km</option>
                        </select>
                        <i data-lucide="chevron-down" class="pointer-events-none absolute right-3 h-3.5 w-3.5 text-white/80"></i>
                    </div>

                    <!-- Submit -->
                    <button type="submit" id="hero-campus-submit" disabled
                            class="w-full sm:w-auto rounded-full bg-[#405BE8] hover:bg-[#3049C9] text-white py-2.5 sm:py-3 px-6 sm:px-7 text-xs sm:text-sm font-semibold shadow-lg shadow-blue-600/30 flex items-center justify-center gap-2 cursor-pointer transition shrink-0 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i data-lucide="search" class="h-4 w-4 text-white"></i>
                        <span>Cari</span>
                    </button>
                </form>

                <!-- METHOD 3: Cari Sekitar Saya (GPS Geolocation) -->
                <form id="form-search-nearby" action="{{ route('kost.index') }}" method="GET"
                      class="hidden rounded-3xl sm:rounded-full border border-white/25 bg-white/15 p-2 sm:p-2.5 backdrop-blur-md shadow-2xl flex flex-col sm:flex-row items-center gap-2 sm:gap-3 relative">
                    
                    <input type="hidden" name="lat" id="nearby-lat">
                    <input type="hidden" name="lng" id="nearby-lng">

                    <!-- GPS Status Display & Trigger -->
                    <div class="flex-1 w-full relative flex items-center pl-4 pr-2">
                        <i data-lucide="crosshair" class="h-4 w-4 text-white/80 shrink-0"></i>
                        <div class="pl-3 pr-2 py-2 flex items-center justify-between w-full">
                            <span id="gps-status-text" class="text-xs sm:text-sm font-medium text-white/90">
                                Klik tombol untuk deteksi posisi GPS Anda
                            </span>
                            <button type="button" onclick="detectUserLocation()"
                                    class="shrink-0 rounded-full bg-white/20 hover:bg-white/30 text-white border border-white/30 px-3 py-1 text-xs font-semibold backdrop-blur transition cursor-pointer">
                                Deteksi GPS
                            </button>
                        </div>
                    </div>

                    <div class="hidden sm:block h-7 w-px bg-white/30 shrink-0"></div>

                    <!-- Radius Selector -->
                    <div class="w-full sm:w-32 relative flex items-center px-3">
                        <select name="radius" class="w-full bg-transparent pl-1 pr-5 py-2 text-xs sm:text-sm font-medium text-white outline-none cursor-pointer appearance-none">
                            <option value="1" class="text-[#182230] bg-white">Radius 1 km</option>
                            <option value="3" class="text-[#182230] bg-white">Radius 3 km</option>
                            <option value="5" selected class="text-[#182230] bg-white">Radius 5 km</option>
                            <option value="10" class="text-[#182230] bg-white">Radius 10 km</option>
                            <option value="15" class="text-[#182230] bg-white">Radius 15 km</option>
                            <option value="20" class="text-[#182230] bg-white">Radius 20 km</option>
                        </select>
                        <i data-lucide="chevron-down" class="pointer-events-none absolute right-3 h-3.5 w-3.5 text-white/80"></i>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="nearby-submit-btn" disabled
                            class="w-full sm:w-auto rounded-full bg-[#405BE8] hover:bg-[#3049C9] text-white py-2.5 sm:py-3 px-6 sm:px-7 text-xs sm:text-sm font-semibold shadow-lg shadow-blue-600/30 flex items-center justify-center gap-2 cursor-pointer transition shrink-0 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i data-lucide="navigation" class="h-4 w-4 text-white"></i>
                        <span>Cari di Sekitar</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- 4 Value Props (Matching Reference Image) -->
<section class="mx-auto max-w-6xl px-4 py-4 sm:px-6">
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-[#E4E8EF] bg-white p-4 flex items-center gap-3.5 shadow-2xs hover:shadow-xs transition">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#EEF2FF] text-[#405BE8]">
                <i data-lucide="map-pin" class="h-5 w-5"></i>
            </div>
            <div>
                <h2 class="text-xs font-bold text-[#182230]">Lokasi Strategis</h2>
                <p class="text-[11px] text-[#667085] mt-0.5">Dekat kampus & perkantoran</p>
            </div>
        </div>

        <div class="rounded-2xl border border-[#E4E8EF] bg-white p-4 flex items-center gap-3.5 shadow-2xs hover:shadow-xs transition">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#EEF2FF] text-[#405BE8]">
                <i data-lucide="wifi" class="h-5 w-5"></i>
            </div>
            <div>
                <h2 class="text-xs font-bold text-[#182230]">Fasilitas Lengkap</h2>
                <p class="text-[11px] text-[#667085] mt-0.5">WiFi, AC, Kamar Mandi Dalam</p>
            </div>
        </div>

        <div class="rounded-2xl border border-[#E4E8EF] bg-white p-4 flex items-center gap-3.5 shadow-2xs hover:shadow-xs transition">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#EEF2FF] text-[#405BE8]">
                <i data-lucide="shield-check" class="h-5 w-5"></i>
            </div>
            <div>
                <h2 class="text-xs font-bold text-[#182230]">Keamanan Terjamin</h2>
                <p class="text-[11px] text-[#667085] mt-0.5">Verifikasi pemilik & listing</p>
            </div>
        </div>

        <div class="rounded-2xl border border-[#E4E8EF] bg-white p-4 flex items-center gap-3.5 shadow-2xs hover:shadow-xs transition">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#EEF2FF] text-[#405BE8]">
                <i data-lucide="headphones" class="h-5 w-5"></i>
            </div>
            <div>
                <h2 class="text-xs font-bold text-[#182230]">Support 24/7</h2>
                <p class="text-[11px] text-[#667085] mt-0.5">Siap membantu kapan saja</p>
            </div>
        </div>
    </div>
</section>

<!-- SECTION 1: Paling Direkomendasikan -->
<section class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
    <div class="flex items-end justify-between pb-6">
        <div>
            <div class="flex items-center gap-2">
                <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-[#EEF2FF] text-[#405BE8]">
                    <i data-lucide="badge-check" class="h-4 w-4"></i>
                </span>
                <h2 class="text-xl font-bold tracking-tight text-[#182230]">Paling Direkomendasikan</h2>
            </div>
            <p class="text-xs text-[#667085] mt-1">Kost unggulan terverifikasi dengan kamar siap huni, fasilitas terbaik, dan rating teruji</p>
        </div>
        <a href="{{ route('kost.index', ['sort' => 'rating']) }}" class="text-xs font-semibold text-[#405BE8] hover:underline flex items-center gap-1">
            <span>Lihat semua</span>
            <i data-lucide="chevron-right" class="h-3.5 w-3.5"></i>
        </a>
    </div>

    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        @forelse($recommended as $property)
            @include('partials.property-card', ['property' => $property, 'favoriteIds' => $favoriteIds])
        @empty
            <div class="card col-span-4 p-12 text-center text-xs text-[#667085]">
                Belum ada listing unggulan saat ini.
            </div>
        @endforelse
    </div>
</section>

<!-- SECTION 2: Kamu Mungkin Suka -->
<section class="mx-auto max-w-6xl px-4 py-6 sm:px-6">
    <div class="flex items-end justify-between pb-6">
        <div>
            <div class="flex items-center gap-2">
                <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-[#EEF2FF] text-[#405BE8]">
                    <i data-lucide="sparkles" class="h-4 w-4"></i>
                </span>
                <h2 class="text-xl font-bold tracking-tight text-[#182230]">Kamu Mungkin Suka</h2>
            </div>
            <p class="text-xs text-[#667085] mt-1">Pilihan kost yang relevan dengan minat dan riwayat pencarianmu</p>
        </div>
        <a href="{{ route('kost.index') }}" class="text-xs font-semibold text-[#405BE8] hover:underline flex items-center gap-1">
            <span>Jelajahi kost</span>
            <i data-lucide="chevron-right" class="h-3.5 w-3.5"></i>
        </a>
    </div>

    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        @forelse($personalized as $property)
            @include('partials.property-card', ['property' => $property, 'favoriteIds' => $favoriteIds])
        @empty
            <div class="card col-span-4 p-12 text-center text-xs text-[#667085]">
                Belum ada rekomendasi kost lain saat ini.
            </div>
        @endforelse
    </div>
</section>

<!-- Area Populer -->
<section class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
    <div class="pb-6">
        <h2 class="text-xl font-bold tracking-tight text-[#182230]">Area Populer</h2>
        <p class="text-xs text-[#667085] mt-1">Jelajahi kost di kota-kota favorit mahasiswa dan pekerja</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @php
            $displayAreas = ($popularAreas && $popularAreas->isNotEmpty())
                ? $popularAreas->take(4)->map(fn($a) => ['city' => $a->city, 'count' => $a->total])
                : collect([
                    ['city' => 'Jakarta Selatan', 'count' => 2],
                    ['city' => 'Depok', 'count' => 4],
                    ['city' => 'Bogor', 'count' => 1],
                    ['city' => 'Bandung', 'count' => 2],
                ]);
        @endphp

        @foreach($displayAreas as $area)
            <a href="{{ route('kost.index', ['city' => $area['city']]) }}" class="rounded-2xl border border-[#E4E8EF] bg-white p-4 hover:border-[#405BE8]/50 hover:shadow-md transition flex items-center justify-between group">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#EEF2FF] text-[#405BE8] group-hover:bg-[#405BE8] group-hover:text-white transition">
                        <i data-lucide="building-2" class="h-5 w-5"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-[#182230] group-hover:text-[#405BE8] transition">{{ $area['city'] }}</p>
                        <p class="text-[11px] text-[#667085]">{{ $area['count'] }} kost tersedia</p>
                    </div>
                </div>
                <i data-lucide="chevron-right" class="h-4 w-4 text-[#98A2B3] group-hover:text-[#405BE8] transition"></i>
            </a>
        @endforeach
    </div>
</section>

<!-- CTA Owner Banner -->
<section class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
    <div class="rounded-3xl bg-[#182230] p-8 text-white sm:p-12 relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-6 shadow-xl">
        <div class="relative z-10 max-w-xl">
            <span class="badge bg-[#405BE8] text-white text-[11px] font-semibold">Khusus Pemilik Kost</span>
            <h2 class="mt-3 text-2xl font-bold tracking-tight sm:text-3xl text-white">
                Punya Kost? Jadi Partner Kosanku!
            </h2>
            <p class="mt-2 text-xs sm:text-sm text-zinc-300">
                Kelola kostmu dengan mudah, jangkau lebih banyak calon penyewa mahasiswa & pekerja secara langsung.
            </p>
            <div class="mt-6 flex flex-wrap gap-3">
                <button type="button" onclick="window.openAuthModal('register')" class="btn-primary text-xs py-2.5 px-6 cursor-pointer">
                    Daftar Sekarang
                </button>
                <a href="{{ route('pages.how') }}" class="btn-ghost bg-white/10 border-white/20 text-white hover:bg-white/20 text-xs py-2.5 px-6">
                    Pelajari Caranya
                </a>
            </div>
        </div>

        <div class="relative z-10 hidden lg:block shrink-0">
            <div class="h-36 w-52 rounded-2xl overflow-hidden border border-white/15 shadow-xl">
                <img src="https://images.unsplash.com/photo-1554995207-c18c203602cb?auto=format&fit=crop&w=600&q=80" alt="Partner Kosanku" class="h-full w-full object-cover">
            </div>
        </div>
    </div>
</section>

<script>
    // Tab switching logic for Hero Search
    function switchSearchTab(tab) {
        const tabs = ['campus', 'area', 'nearby'];
        tabs.forEach(t => {
            const btn = document.getElementById(`tab-btn-${t}`);
            const form = document.getElementById(`form-search-${t}`);
            if (t === tab) {
                btn.className = "flex items-center gap-1.5 px-3 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition cursor-pointer bg-[#405BE8] text-white shadow-md";
                form.classList.remove('hidden');
            } else {
                btn.className = "flex items-center gap-1.5 px-3 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition cursor-pointer text-white/80 hover:text-white";
                form.classList.add('hidden');
            }
        });
        if (window.refreshIcons) window.refreshIcons();
    }

    // Method 1: Strict City First -> Fetch Campuses
    async function loadHeroCampuses(city) {
        const campusSelect = document.getElementById('hero-campus-select');
        const submitBtn = document.getElementById('hero-campus-submit');
        
        if (!city) {
            campusSelect.disabled = true;
            campusSelect.innerHTML = '<option value="" class="text-[#182230] bg-white">2. Pilih kota terlebih dahulu</option>';
            submitBtn.disabled = true;
            return;
        }

        campusSelect.disabled = true;
        campusSelect.innerHTML = '<option value="" class="text-[#182230] bg-white">Memuat kampus di ' + city + '...</option>';

        try {
            const res = await fetch(`/api/campuses?city=${encodeURIComponent(city)}`);
            const campuses = await res.json();

            if (campuses.length > 0) {
                campusSelect.innerHTML = '<option value="" class="text-[#182230] bg-white">2. Pilih Kampus (' + campuses.length + ' tersedia)</option>' +
                    campuses.map(c => `<option value="${c.id}" class="text-[#182230] bg-white">${c.name}</option>`).join('');
                campusSelect.disabled = false;
                campusSelect.onchange = () => {
                    submitBtn.disabled = !campusSelect.value;
                };
            } else {
                campusSelect.innerHTML = '<option value="" class="text-[#182230] bg-white">Belum ada data kampus di kota ini</option>';
                submitBtn.disabled = true;
            }
        } catch(e) {
            campusSelect.innerHTML = '<option value="" class="text-[#182230] bg-white">Gagal memuat kampus</option>';
            submitBtn.disabled = true;
        }
    }

    // Method 2: Free Text Autocomplete
    let locationTimer = null;
    const dropdown = document.getElementById('location-suggestions');
    const input = document.getElementById('home-location-input');

    async function handleLocationInput(val) {
        clearTimeout(locationTimer);
        locationTimer = setTimeout(async () => {
            await fetchSuggestions(val);
        }, 200);
    }

    async function showLocationSuggestions() {
        if (!dropdown.children.length) {
            await fetchSuggestions(input.value);
        }
        dropdown.classList.remove('hidden');
    }

    async function fetchSuggestions(query) {
        try {
            const res = await fetch(`/api/locations?q=${encodeURIComponent(query)}`);
            const data = await res.json();
            
            if (data.length > 0) {
                dropdown.innerHTML = data.map(item => `
                    <div onclick="selectLocation('${item.label.replace(/'/g, "\\'")}')" class="p-2.5 hover:bg-[#EEF2FF] cursor-pointer flex items-center justify-between border-b border-[#E4E8EF] last:border-b-0 transition">
                        <span class="font-semibold text-[#182230]">${item.label}</span>
                        <span class="text-[10px] text-[#667085] bg-[#F8FAFC] px-2 py-0.5 rounded">${item.type}</span>
                    </div>
                `).join('');
                dropdown.classList.remove('hidden');
            } else {
                dropdown.classList.add('hidden');
            }
        } catch (e) {
            dropdown.classList.add('hidden');
        }
    }

    function selectLocation(val) {
        input.value = val;
        dropdown.classList.add('hidden');
    }

    // Close on click outside
    document.addEventListener('click', (e) => {
        if (input && !input.contains(e.target) && dropdown && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });

    // Method 3: GPS Geolocation
    function detectUserLocation() {
        const statusText = document.getElementById('gps-status-text');
        const latInput = document.getElementById('nearby-lat');
        const lngInput = document.getElementById('nearby-lng');
        const submitBtn = document.getElementById('nearby-submit-btn');

        if (!navigator.geolocation) {
            statusText.textContent = "Browser tidak mendukung Geolocation GPS.";
            return;
        }

        statusText.textContent = "Mendeteksi koordinat GPS...";
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                latInput.value = lat;
                lngInput.value = lng;
                statusText.innerHTML = `<span class="text-emerald-300 font-semibold">✓ Lokasi terdeteksi</span> (${lat.toFixed(4)}, ${lng.toFixed(4)})`;
                submitBtn.disabled = false;
            },
            (err) => {
                statusText.textContent = "Izin GPS ditolak atau gagal mendeteksi lokasi.";
                submitBtn.disabled = true;
            },
            { timeout: 10000 }
        );
    }
</script>
@endsection
