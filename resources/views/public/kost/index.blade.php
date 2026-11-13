@extends('layouts.public')
@section('title', 'Cari Kost | ' . setting('site_name', 'Kosanku'))

@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<style>
    /* Pulsing user radar beacon */
    .user-gps-pulse {
        position: relative;
        width: 24px;
        height: 24px;
    }
    .user-gps-pulse::before {
        content: '';
        position: absolute;
        inset: -10px;
        border-radius: 9999px;
        background: rgba(64, 91, 232, 0.3);
        animation: pulse-ring 2s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
    }
    .user-gps-pulse-dot {
        position: absolute;
        inset: 2px;
        border-radius: 9999px;
        background: #405BE8;
        border: 3px solid #ffffff;
        box-shadow: 0 4px 12px rgba(64, 91, 232, 0.5);
    }
    @keyframes pulse-ring {
        0% { transform: scale(0.6); opacity: 1; }
        100% { transform: scale(2.4); opacity: 0; }
    }
    /* Custom Kost Pin Badge */
    .kost-map-pin {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #405BE8;
        color: white;
        padding: 5px 9px;
        border-radius: 9999px;
        font-size: 11px;
        font-weight: 700;
        box-shadow: 0 4px 14px rgba(24, 34, 48, 0.25);
        border: 2px solid #ffffff;
        white-space: nowrap;
        cursor: pointer;
        transition: transform 0.2s ease, background-color 0.2s ease;
    }
    .kost-map-pin:hover, .kost-map-pin.active {
        transform: scale(1.1);
        background: #3049C9;
        z-index: 1000 !important;
    }
    .leaflet-popup-content-wrapper {
        border-radius: 1rem;
        padding: 0;
        overflow: hidden;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
    }
    .leaflet-popup-content {
        margin: 0 !important;
        line-height: inherit;
    }
</style>
@endpush

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6">
    <!-- Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#182230] sm:text-3xl">Cari Kost Impianmu</h1>
            <p class="mt-1 text-xs text-[#667085] sm:text-sm">Temukan kost terverifikasi dengan fasilitas lengkap dan lokasi paling strategis.</p>
        </div>

        @if($selectedCampus || ($userLat && $userLng) || request('city') || request('q'))
            <a href="{{ route('kost.index') }}" class="inline-flex items-center gap-1.5 self-start text-xs font-semibold text-rose-600 hover:text-rose-700 bg-rose-50 border border-rose-200 px-3 py-1.5 rounded-full transition">
                <i data-lucide="x" class="h-3.5 w-3.5"></i>
                <span>Hapus Semua Filter</span>
            </a>
        @endif
    </div>

    <!-- Active Search Mode Indicator Banner -->
    @if($selectedCampus)
        <div class="mb-6 rounded-2xl border border-[#405BE8]/30 bg-[#EEF2FF] p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#405BE8] text-white">
                    <i data-lucide="graduation-cap" class="h-5 w-5"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-[#182230]">{{ $selectedCampus->name }}</span>
                        <span class="text-[10px] font-semibold text-[#405BE8] bg-white px-2 py-0.5 rounded-full border border-[#405BE8]/20">
                            Radius {{ $selectedRadius }} km
                        </span>
                    </div>
                    <p class="text-[11px] text-[#667085] mt-0.5">{{ $selectedCampus->city }} &middot; Menampilkan kost terdekat diurutkan berdasarkan jarak ke kampus</p>
                </div>
            </div>
            <a href="{{ route('kost.index') }}" class="text-xs font-semibold text-[#405BE8] hover:underline">
                Ganti Lokasi
            </a>
        </div>
    @elseif($userLat && $userLng)
        <div class="mb-6 rounded-2xl border border-[#405BE8]/30 bg-[#EEF2FF]/60 p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#405BE8] text-white shadow-sm">
                    <i data-lucide="navigation" class="h-5 w-5"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-bold text-[#182230]">Kost di Sekitar Lokasi GPS Anda</span>
                        <span class="text-[10px] font-bold text-[#405BE8] bg-white px-2.5 py-0.5 rounded-full border border-[#405BE8]/30">
                            Radius {{ $selectedRadius }} km
                        </span>
                        <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                            ✓ GPS Aktif
                        </span>
                    </div>
                    <p class="text-[11px] text-[#667085] mt-0.5">
                        Koordinat: {{ number_format($userLat, 4) }}, {{ number_format($userLng, 4) }} &middot; Diurutkan dari yang paling dekat
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" onclick="centerMapOnUser()" class="btn-ghost text-xs py-1.5 px-3 bg-white border border-[#E4E8EF] hover:border-[#405BE8]/40 flex items-center gap-1.5">
                    <i data-lucide="crosshair" class="h-3.5 w-3.5 text-[#405BE8]"></i>
                    <span>Pusatkan Peta</span>
                </button>
                <a href="{{ route('kost.index') }}" class="text-xs font-semibold text-rose-600 hover:underline">
                    Hapus GPS
                </a>
            </div>
        </div>
    @endif

    <div class="flex flex-col gap-8 lg:flex-row">
        <!-- Sidebar Filter (Desktop & Mobile) -->
        <aside class="w-full shrink-0 lg:w-80">
            <div class="sticky top-24 rounded-2xl border border-[#E4E8EF] bg-white p-5 shadow-sm space-y-6">
                <!-- Filter Header -->
                <div class="flex items-center justify-between pb-3 border-b border-[#E4E8EF]">
                    <div class="flex items-center gap-2">
                        <i data-lucide="sliders-horizontal" class="h-4 w-4 text-[#405BE8]"></i>
                        <h2 class="text-sm font-bold text-[#182230]">Filter Pencarian</h2>
                    </div>
                    @if(request()->hasAny(['q', 'city', 'campus_id', 'lat', 'lng', 'type', 'sort', 'min_price', 'max_price', 'category', 'facilities']))
                        <a href="{{ route('kost.index') }}" class="text-xs font-semibold text-[#405BE8] hover:underline">Reset Semua</a>
                    @endif
                </div>

                <!-- Main Filter Form -->
                <form method="GET" action="{{ route('kost.index') }}" id="main-filter-form" class="space-y-5">
                    
                    <!-- 3 Metode Pencarian Accordion/Tabs in Sidebar -->
                    <div class="rounded-xl border border-[#E4E8EF] p-3 bg-[#F8FAFC]">
                        <label class="block text-xs font-bold text-[#182230] mb-2">Metode Pencarian Lokasi</label>
                        
                        <!-- Toggle Buttons -->
                        <div class="grid grid-cols-3 gap-1 p-1 bg-white rounded-lg border border-[#E4E8EF] mb-3 text-[11px] font-semibold">
                            <button type="button" id="side-tab-campus" onclick="setSideSearchMode('campus')"
                                    class="py-1 px-1.5 rounded text-center transition {{ ($selectedCampus || request('mode') === 'campus') ? 'bg-[#405BE8] text-white shadow-xs' : 'text-[#667085] hover:text-[#182230]' }}">
                                Kampus
                            </button>
                            <button type="button" id="side-tab-area" onclick="setSideSearchMode('area')"
                                    class="py-1 px-1.5 rounded text-center transition {{ (! $selectedCampus && ! ($userLat && $userLng)) ? 'bg-[#405BE8] text-white shadow-xs' : 'text-[#667085] hover:text-[#182230]' }}">
                                Kota/Area
                            </button>
                            <button type="button" id="side-tab-nearby" onclick="setSideSearchMode('nearby')"
                                    class="py-1 px-1.5 rounded text-center transition {{ ($userLat && $userLng) ? 'bg-[#405BE8] text-white shadow-xs' : 'text-[#667085] hover:text-[#182230]' }}">
                                GPS Saya
                            </button>
                        </div>

                        <!-- PANEL 1: Kampus (Kota Dulu -> Kampus -> Radius) -->
                        <div id="side-panel-campus" class="{{ ($selectedCampus || request('mode') === 'campus') ? '' : 'hidden' }} space-y-2.5">
                            <div>
                                <label class="block text-[11px] font-semibold text-[#667085] mb-1">1. Pilih Kota / Daerah</label>
                                <select id="side-campus-city" onchange="loadSideCampuses(this.value)" class="input text-xs">
                                    <option value="">Pilih Kota...</option>
                                    @foreach($cities as $c)
                                        <option value="{{ $c }}" @selected($selectedCity === $c)>{{ $c }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-[#667085] mb-1">2. Pilih Kampus</label>
                                <select name="campus_id" id="side-campus-select" class="input text-xs" {{ $campusesInCity->isEmpty() ? 'disabled' : '' }}>
                                    <option value="">{{ $campusesInCity->isEmpty() ? 'Pilih kota dulu' : 'Pilih Kampus' }}</option>
                                    @foreach($campusesInCity as $camp)
                                        <option value="{{ $camp->id }}" @selected(request('campus_id') == $camp->id)>{{ $camp->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-[#667085] mb-1">3. Radius Jarak</label>
                                <select name="radius" class="input text-xs">
                                    <option value="1" @selected($selectedRadius == 1)>Radius 1 km</option>
                                    <option value="3" @selected($selectedRadius == 3)>Radius 3 km</option>
                                    <option value="5" @selected($selectedRadius == 5)>Radius 5 km</option>
                                    <option value="10" @selected($selectedRadius == 10)>Radius 10 km</option>
                                    <option value="15" @selected($selectedRadius == 15)>Radius 15 km</option>
                                    <option value="20" @selected($selectedRadius == 20)>Radius 20 km</option>
                                </select>
                            </div>
                        </div>

                        <!-- PANEL 2: Kota / Area Bebas -->
                        <div id="side-panel-area" class="{{ (! $selectedCampus && ! ($userLat && $userLng)) ? '' : 'hidden' }} space-y-2.5">
                            <div class="relative">
                                <label class="block text-[11px] font-semibold text-[#667085] mb-1">Ketik Kota / Kecamatan</label>
                                <div class="relative">
                                    <i data-lucide="map-pin" class="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-[#98A2B3]"></i>
                                    <input type="text"
                                           name="city"
                                           id="listing-location-input"
                                           value="{{ request('city') ?: request('location') }}"
                                           placeholder="Depok, Bandung, dll..."
                                           autocomplete="off"
                                           oninput="handleListingLocationInput(this.value)"
                                           class="input pl-9 text-xs">
                                </div>
                                <div id="listing-location-dropdown" class="hidden absolute left-0 right-0 top-full mt-1 rounded-xl border border-[#E4E8EF] bg-white shadow-xl z-50 overflow-hidden text-xs max-h-48 overflow-y-auto">
                                </div>
                            </div>
                        </div>

                        <!-- PANEL 3: Lokasi GPS Saya (Interactive Map Search) -->
                        <div id="side-panel-nearby" class="{{ ($userLat && $userLng) ? '' : 'hidden' }} space-y-3">
                            <input type="hidden" name="lat" id="side-lat" value="{{ $userLat }}">
                            <input type="hidden" name="lng" id="side-lng" value="{{ $userLng }}">

                            <div class="rounded-xl border border-[#E4E8EF] bg-white p-3 space-y-2.5 shadow-2xs">
                                <button type="button" onclick="detectSideGps()" id="btn-detect-gps"
                                        class="btn-primary w-full py-2.5 text-xs font-semibold flex items-center justify-center gap-2 cursor-pointer shadow-sm shadow-blue-600/20">
                                    <i data-lucide="crosshair" class="h-4 w-4"></i>
                                    <span>Ambil Posisi GPS Saya</span>
                                </button>

                                <div id="side-gps-status" class="rounded-lg p-2 text-center text-xs leading-tight transition">
                                    @if($userLat && $userLng)
                                        <div class="text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-md p-1.5 font-medium">
                                            <span class="font-bold">✓ Lokasi Berhasil Ditemukan</span>
                                            <span class="block text-[10px] text-emerald-600 mt-0.5">({{ number_format($userLat, 4) }}, {{ number_format($userLng, 4) }})</span>
                                        </div>
                                    @else
                                        <span class="text-[#667085]">Klik tombol di atas untuk mengaktifkan pencarian peta dan kost di sekitar Anda.</span>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-[#667085] mb-1">Pilih Radius Jarak</label>
                                <select name="radius" id="side-radius-select" onchange="onSideRadiusChange(this.value)" class="input text-xs cursor-pointer">
                                    <option value="1" @selected($selectedRadius == 1)>Radius 1 km</option>
                                    <option value="3" @selected($selectedRadius == 3)>Radius 3 km</option>
                                    <option value="5" @selected($selectedRadius == 5)>Radius 5 km</option>
                                    <option value="10" @selected($selectedRadius == 10)>Radius 10 km</option>
                                    <option value="15" @selected($selectedRadius == 15)>Radius 15 km</option>
                                    <option value="20" @selected($selectedRadius == 20)>Radius 20 km</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Search Keyword -->
                    <div>
                        <label class="block text-xs font-medium text-[#667085] mb-1.5">Kata Kunci Nama Kost</label>
                        <div class="relative">
                            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-[#98A2B3]"></i>
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama kost..." class="input pl-9 text-xs">
                        </div>
                    </div>

                    <!-- Property Type -->
                    <div>
                        <label class="block text-xs font-medium text-[#667085] mb-1.5">Tipe Kost</label>
                        <select name="type" class="input text-xs cursor-pointer">
                            <option value="">Semua Tipe (Putra, Putri, Campur)</option>
                            @foreach($types as $type)
                                <option value="{{ $type->value }}" @selected(request('type') === $type->value)>{{ $type->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Price Range (Formatted Rupiah) -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-medium text-[#667085]">Rentang Harga Bulanan</label>
                            @if(request('min_price') || request('max_price'))
                                <button type="button" onclick="resetPriceFilter()" class="text-[11px] text-[#405BE8] hover:underline">Reset Harga</button>
                            @endif
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <span class="text-[10px] text-[#98A2B3] block mb-0.5">Minimum</span>
                                <input type="number"
                                       name="min_price"
                                       id="min-price-input"
                                       value="{{ request('min_price') }}"
                                       placeholder="Rp500.000"
                                       step="50000"
                                       class="input text-xs">
                            </div>
                            <div>
                                <span class="text-[10px] text-[#98A2B3] block mb-0.5">Maksimum</span>
                                <input type="number"
                                       name="max_price"
                                       id="max-price-input"
                                       value="{{ request('max_price') }}"
                                       placeholder="Rp3.000.000"
                                       step="50000"
                                       class="input text-xs">
                            </div>
                        </div>
                    </div>

                    <!-- Category -->
                    @if(isset($categories) && $categories->isNotEmpty())
                    <div>
                        <label class="block text-xs font-medium text-[#667085] mb-1.5">Kategori Kost</label>
                        <select name="category" class="input text-xs cursor-pointer">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <!-- Facilities -->
                    @if(isset($facilities) && $facilities->isNotEmpty())
                    <div class="pt-2 border-t border-[#E4E8EF]">
                        <label class="block text-xs font-medium text-[#667085] mb-2">Fasilitas Kost</label>
                        <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                            @foreach($facilities as $facility)
                                <label class="flex items-center gap-2 text-xs text-[#182230] cursor-pointer hover:text-[#405BE8]">
                                    <input type="checkbox" name="facilities[]" value="{{ $facility->id }}"
                                        @checked(in_array($facility->id, (array) request('facilities')))
                                        class="rounded border-[#E4E8EF] text-[#405BE8] focus:ring-[#405BE8]/20">
                                    <span>{{ $facility->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Submit Button -->
                    <button type="submit" class="btn-primary w-full py-2.5 font-semibold flex items-center justify-center gap-1.5 cursor-pointer shadow-md shadow-blue-600/20">
                        <i data-lucide="filter" class="h-3.5 w-3.5"></i>
                        <span>Terapkan Filter</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Kost Grid, Interactive Map & Sorting -->
        <main class="flex-1 min-w-0">

            <!-- INTERACTIVE GPS MAP SECTION (Visible when in GPS mode or activated) -->
            <div id="gps-interactive-map-section" class="{{ ($userLat && $userLng) ? '' : 'hidden' }} mb-6 rounded-2xl border border-[#E4E8EF] bg-white p-4 sm:p-5 shadow-sm space-y-3">
                <!-- Header Controls Bar -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#EEF2FF] text-[#405BE8]">
                            <i data-lucide="map" class="h-5 w-5"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-[#182230] flex items-center gap-2 flex-wrap">
                                <span>Peta Interaktif Kost Sekitar Saya</span>
                                <span id="map-kost-count-badge" class="badge border border-[#405BE8]/30 bg-[#EEF2FF] text-[#405BE8] font-bold text-[10px]">
                                    {{ $properties->total() }} Kost Ditemukan
                                </span>
                            </h3>
                            <p id="map-status-subtitle" class="text-xs text-[#667085]">
                                Menampilkan kost dalam radius <strong id="current-radius-label" class="text-[#405BE8]">{{ $selectedRadius }} km</strong> dari posisi GPS Anda
                            </p>
                        </div>
                    </div>

                    <!-- Map Quick Controls -->
                    <div class="flex flex-wrap items-center gap-2">
                        <!-- Radius Selector Pills / Dropdown -->
                        <div class="flex items-center gap-1.5 bg-[#F8FAFC] p-1 rounded-xl border border-[#E4E8EF] text-xs">
                            <span class="text-[11px] font-semibold text-[#667085] pl-1.5">Radius:</span>
                            <select id="map-radius-select" onchange="onMapRadiusChange(this.value)" class="bg-transparent font-bold text-[#405BE8] text-xs outline-none cursor-pointer pr-1">
                                <option value="1" @selected($selectedRadius == 1)>1 km</option>
                                <option value="3" @selected($selectedRadius == 3)>3 km</option>
                                <option value="5" @selected($selectedRadius == 5)>5 km</option>
                                <option value="10" @selected($selectedRadius == 10)>10 km</option>
                                <option value="15" @selected($selectedRadius == 15)>15 km</option>
                                <option value="20" @selected($selectedRadius == 20)>20 km</option>
                            </select>
                        </div>

                        <!-- Re-center to user GPS button -->
                        <button type="button" onclick="centerMapOnUser()" class="btn-ghost border border-[#E4E8EF] hover:border-[#405BE8]/40 py-1.5 px-3 text-xs font-semibold flex items-center gap-1.5 rounded-xl transition cursor-pointer">
                            <i data-lucide="crosshair" class="h-3.5 w-3.5 text-[#405BE8]"></i>
                            <span>Ke Lokasi Saya</span>
                        </button>

                        <!-- Fit bounds button -->
                        <button type="button" onclick="fitMapBounds()" class="btn-ghost border border-[#E4E8EF] hover:border-[#405BE8]/40 py-1.5 px-3 text-xs font-semibold flex items-center gap-1.5 rounded-xl transition cursor-pointer">
                            <i data-lucide="maximize" class="h-3.5 w-3.5 text-[#405BE8]"></i>
                            <span>Tampilkan Semua</span>
                        </button>
                    </div>
                </div>

                <!-- The Leaflet Map Container -->
                <div class="relative h-[360px] sm:h-[440px] w-full rounded-xl overflow-hidden border border-[#E4E8EF] bg-zinc-100 z-0">
                    <div id="interactive-gps-map" class="h-full w-full"></div>
                    
                    <!-- Live Loading Spinner Overlay (when updating radius or GPS) -->
                    <div id="map-loading-overlay" class="hidden absolute inset-0 bg-white/70 backdrop-blur-xs flex items-center justify-center z-[1000]">
                        <div class="flex items-center gap-2.5 bg-white border border-[#E4E8EF] shadow-lg px-4 py-2 rounded-2xl text-xs font-bold text-[#182230]">
                            <div class="h-4 w-4 rounded-full border-2 border-[#405BE8] border-t-transparent animate-spin"></div>
                            <span>Mencari kost di sekitar lokasi...</span>
                        </div>
                    </div>
                </div>

                <!-- Map Info Legend Footer -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between text-[11px] text-[#667085] gap-2 pt-1 border-t border-[#E4E8EF]">
                    <div class="flex items-center gap-4 flex-wrap">
                        <span class="flex items-center gap-1.5">
                            <span class="inline-block h-3 w-3 rounded-full bg-[#405BE8] ring-4 ring-[#405BE8]/20"></span>
                            <span>Posisi Anda (GPS)</span>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="inline-flex items-center justify-center h-4 w-4 rounded-full bg-[#405BE8] text-white text-[9px] font-bold">🏠</span>
                            <span>Lokasi Kost</span>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="inline-block h-2 w-5 border-t-2 border-dashed border-[#405BE8]"></span>
                            <span>Batas Radius</span>
                        </span>
                    </div>
                    <div>
                        <span>Klik marker kost untuk info singkat & link detail</span>
                    </div>
                </div>
            </div>

            <!-- Results Header & Sorting Bar -->
            <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between rounded-2xl border border-[#E4E8EF] bg-white p-4 shadow-sm">
                <div class="text-xs text-[#667085]" id="results-count-text">
                    Menampilkan <span class="font-bold text-[#182230]" id="results-count-number">{{ $properties->total() }}</span> kost ditemukan
                    @if($userLat && $userLng)
                        <span class="text-[#405BE8] font-semibold">(diurutkan dari terdekat)</span>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-[#667085] shrink-0">Urutkan:</span>
                    <form method="GET" action="{{ route('kost.index') }}" class="inline">
                        @foreach(request()->except('sort', 'page') as $key => $val)
                            @if(is_array($val))
                                @foreach($val as $item)
                                    <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                                @endforeach
                            @else
                                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                            @endif
                        @endforeach
                        <select name="sort" onchange="this.form.submit()" class="rounded-xl border border-[#E4E8EF] bg-[#F8FAFC] px-3 py-1.5 text-xs font-semibold text-[#182230] outline-none focus:border-[#405BE8] focus:bg-white transition cursor-pointer">
                            <option value="newest" @selected(request('sort', 'newest') === 'newest')>Terbaru</option>
                            <option value="price_asc" @selected(request('sort') === 'price_asc')>Harga Terendah</option>
                            <option value="price_desc" @selected(request('sort') === 'price_desc')>Harga Tertinggi</option>
                            <option value="rating" @selected(request('sort') === 'rating')>Rating Tertinggi</option>
                        </select>
                    </form>
                </div>
            </div>

            <!-- Kost Cards Grid Container -->
            <div id="kost-results-container">
                @if($properties->isEmpty())
                    <div class="rounded-2xl border border-[#E4E8EF] bg-white p-12 text-center shadow-sm">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[#EEF2FF] text-[#405BE8]">
                            <i data-lucide="search-x" class="h-7 w-7"></i>
                        </div>
                        <h3 class="mt-4 text-base font-bold text-[#182230]">Belum ada kost yang cocok</h3>
                        <p class="mt-1 text-xs text-[#667085] max-w-sm mx-auto">
                            @if($userLat && $userLng)
                                Tidak ada kost dalam radius {{ $selectedRadius }} km. Coba perbesar radius pencarian menjadi 10 km, 15 km, atau 20 km.
                            @else
                                Coba sesuaikan radius pencarian, ubah kata kunci lokasi, atau reset filter harga.
                            @endif
                        </p>
                        @if($userLat && $userLng && $selectedRadius < 20)
                            <button type="button" onclick="onMapRadiusChange(20)" class="btn-primary mt-5 inline-flex text-xs py-2 px-5 cursor-pointer">
                                Perbesar Radius ke 20 km
                            </button>
                        @else
                            <a href="{{ route('kost.index') }}" class="btn-primary mt-5 inline-flex text-xs py-2 px-5">
                                Lihat Semua Kost
                            </a>
                        @endif
                    </div>
                @else
                    <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3" id="kost-cards-grid">
                        @foreach($properties as $property)
                            @include('partials.property-card', ['property' => $property, 'favoriteIds' => $favoriteIds])
                        @endforeach
                    </div>

                    <div class="mt-8" id="kost-pagination">
                        {{ $properties->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>
</div>

<script>
    // State Variables for Map & Search
    window.currentUserLat = {{ $userLat !== null ? (float) $userLat : 'null' }};
    window.currentUserLng = {{ $userLng !== null ? (float) $userLng : 'null' }};
    window.currentRadius = {{ (float) $selectedRadius }};
    window.initialMapProperties = @json($mapProperties ?? []);

    let leafletMap = null;
    let userMarker = null;
    let radiusCircle = null;
    let propertyMarkersGroup = null;
    let propertyMarkersMap = {};

    document.addEventListener('DOMContentLoaded', () => {
        if (window.currentUserLat !== null && window.currentUserLng !== null) {
            initLeafletMap(window.currentUserLat, window.currentUserLng, window.currentRadius, window.initialMapProperties);
        }
    });

    // Initialize or re-render Leaflet Map
    function initLeafletMap(lat, lng, radiusKm, properties) {
        const container = document.getElementById('interactive-gps-map');
        if (!container || typeof L === 'undefined') return;

        if (!leafletMap) {
            leafletMap = L.map('interactive-gps-map', {
                scrollWheelZoom: true,
                zoomControl: true,
            }).setView([lat, lng], getZoomForRadius(radiusKm));

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19,
            }).addTo(leafletMap);

            propertyMarkersGroup = L.featureGroup().addTo(leafletMap);
        } else {
            leafletMap.setView([lat, lng], getZoomForRadius(radiusKm));
        }

        renderMapUserAndCircle(lat, lng, radiusKm);
        renderMapProperties(properties, lat, lng);
        setTimeout(() => leafletMap.invalidateSize(), 200);
    }

    function getZoomForRadius(radiusKm) {
        if (radiusKm <= 1) return 15;
        if (radiusKm <= 3) return 14;
        if (radiusKm <= 5) return 13;
        if (radiusKm <= 10) return 12;
        if (radiusKm <= 15) return 11;
        return 10;
    }

    function renderMapUserAndCircle(lat, lng, radiusKm) {
        if (!leafletMap) return;

        // 1. User Pulse Marker
        if (userMarker) {
            userMarker.setLatLng([lat, lng]);
        } else {
            const userIcon = L.divIcon({
                className: 'custom-user-icon',
                html: `<div class="user-gps-pulse"><div class="user-gps-pulse-dot"></div></div>`,
                iconSize: [24, 24],
                iconAnchor: [12, 12],
            });
            userMarker = L.marker([lat, lng], { icon: userIcon, zIndexOffset: 2000 }).addTo(leafletMap);
            userMarker.bindPopup(`
                <div class="p-3 text-center text-xs font-sans">
                    <p class="font-bold text-[#182230] text-sm">📍 Posisi Anda (GPS)</p>
                    <p class="text-[11px] text-[#667085] mt-1">Pusat radius pencarian kost (${radiusKm} km)</p>
                </div>
            `);
        }

        // 2. Search Radius Circle
        const radiusMeters = radiusKm * 1000;
        if (radiusCircle) {
            radiusCircle.setLatLng([lat, lng]);
            radiusCircle.setRadius(radiusMeters);
        } else {
            radiusCircle = L.circle([lat, lng], {
                radius: radiusMeters,
                color: '#405BE8',
                fillColor: '#405BE8',
                fillOpacity: 0.08,
                weight: 2,
                dashArray: '6, 6',
            }).addTo(leafletMap);
        }
    }

    function renderMapProperties(properties, userLat, userLng) {
        if (!propertyMarkersGroup) return;

        propertyMarkersGroup.clearLayers();
        propertyMarkersMap = {};

        if (!properties || properties.length === 0) return;

        properties.forEach(p => {
            if (!p.lat || !p.lng) return;

            // Formatted price snippet (e.g. 1.2jt)
            const priceClean = p.monthly_price ? 'Rp ' + (p.monthly_price >= 1000000 ? (p.monthly_price / 1000000).toFixed(1).replace('.0', '') + 'jt' : (p.monthly_price / 1000) + 'rb') : p.price;

            const pinIcon = L.divIcon({
                className: 'custom-kost-icon',
                html: `<div class="kost-map-pin" id="pin-property-${p.id}">
                    <span>🏠</span>
                    <span>${priceClean}</span>
                </div>`,
                iconSize: [85, 28],
                iconAnchor: [42, 14],
            });

            const marker = L.marker([p.lat, p.lng], { icon: pinIcon });

            // Popup card matching specification
            const popupContent = `
                <div class="w-64 p-3 bg-white font-sans text-[#182230]">
                    <div class="relative h-28 w-full rounded-xl overflow-hidden bg-zinc-100 mb-2.5">
                        <img src="${p.cover_url}" alt="${p.name}" class="h-full w-full object-cover" onerror="this.src='/images/kost-fallback.svg'">
                        <span class="absolute top-2 left-2 rounded-md bg-white/95 px-2 py-0.5 text-[10px] font-bold text-[#182230] shadow-xs">${p.type}</span>
                        ${p.distance_text ? `<span class="absolute top-2 right-2 rounded-md bg-[#405BE8] text-white px-2 py-0.5 text-[10px] font-bold shadow-xs flex items-center gap-1">📍 ${p.distance_text}</span>` : ''}
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-[#667085] mb-1">
                        <span class="truncate max-w-[140px]">${p.address}</span>
                        <span class="flex items-center gap-0.5 text-amber-500 font-bold shrink-0">★ ${p.rating}</span>
                    </div>
                    <h4 class="font-bold text-xs text-[#182230] line-clamp-1 mb-1">${p.name}</h4>
                    <div class="flex items-baseline justify-between mt-2 pt-2 border-t border-[#E4E8EF]">
                        <div>
                            <span class="text-xs font-black text-[#405BE8]">${p.price}</span>
                            <span class="text-[10px] text-[#667085]">/ bln</span>
                        </div>
                        <a href="${p.detail_url}" class="rounded-lg bg-[#405BE8] hover:bg-[#3049C9] text-white px-2.5 py-1 text-[11px] font-bold transition">
                            Lihat Detail →
                        </a>
                    </div>
                </div>
            `;

            marker.bindPopup(popupContent);
            propertyMarkersGroup.addLayer(marker);
            propertyMarkersMap[p.id] = marker;
        });
    }

    function centerMapOnUser() {
        if (!leafletMap || window.currentUserLat === null || window.currentUserLng === null) return;
        leafletMap.flyTo([window.currentUserLat, window.currentUserLng], getZoomForRadius(window.currentRadius), {
            duration: 1.2
        });
        if (userMarker) userMarker.openPopup();
    }

    function fitMapBounds() {
        if (!leafletMap) return;
        if (radiusCircle) {
            leafletMap.fitBounds(radiusCircle.getBounds(), { padding: [30, 30] });
        } else if (propertyMarkersGroup && propertyMarkersGroup.getLayers().length > 0) {
            leafletMap.fitBounds(propertyMarkersGroup.getBounds(), { padding: [40, 40] });
        }
    }

    function showMapLoading(show) {
        const overlay = document.getElementById('map-loading-overlay');
        if (overlay) {
            if (show) overlay.classList.remove('hidden');
            else overlay.classList.add('hidden');
        }
    }

    // Geolocation detection handler
    function detectSideGps() {
        const status = document.getElementById('side-gps-status');
        const latInput = document.getElementById('side-lat');
        const lngInput = document.getElementById('side-lng');
        const detectBtn = document.getElementById('btn-detect-gps');

        if (!navigator.geolocation) {
            status.innerHTML = `<span class="text-rose-600 font-semibold">Browser Anda tidak mendukung Geolocation GPS.</span>`;
            return;
        }

        if (detectBtn) {
            detectBtn.disabled = true;
            detectBtn.classList.add('opacity-75');
        }

        status.innerHTML = `
            <div class="flex items-center justify-center gap-2 text-[#405BE8] py-1">
                <div class="h-3.5 w-3.5 border-2 border-[#405BE8] border-t-transparent rounded-full animate-spin"></div>
                <span class="text-xs font-semibold">Sedang membaca koordinat GPS...</span>
            </div>
        `;

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                latInput.value = lat;
                lngInput.value = lng;
                window.currentUserLat = lat;
                window.currentUserLng = lng;

                status.innerHTML = `
                    <div class="text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-md p-1.5 font-medium text-xs">
                        <span class="font-bold">✓ Lokasi Berhasil Ditemukan</span>
                        <span class="block text-[10px] text-emerald-600 mt-0.5">(${lat.toFixed(4)}, ${lng.toFixed(4)})</span>
                    </div>
                `;

                if (detectBtn) {
                    detectBtn.disabled = false;
                    detectBtn.classList.remove('opacity-75');
                }

                // Show Map Section
                const mapSection = document.getElementById('gps-interactive-map-section');
                if (mapSection) mapSection.classList.remove('hidden');

                const radius = parseFloat(document.getElementById('side-radius-select')?.value || window.currentRadius || 5);
                window.currentRadius = radius;

                // Sync radius select on map
                const mapRadiusSelect = document.getElementById('map-radius-select');
                if (mapRadiusSelect) mapRadiusSelect.value = radius;

                // Initialize or update map
                if (!leafletMap) {
                    initLeafletMap(lat, lng, radius, []);
                } else {
                    renderMapUserAndCircle(lat, lng, radius);
                    centerMapOnUser();
                }

                updateGpsSearch(lat, lng, radius);
            },
            (err) => {
                if (detectBtn) {
                    detectBtn.disabled = false;
                    detectBtn.classList.remove('opacity-75');
                }
                let msg = 'Izin akses lokasi GPS ditolak oleh browser.';
                if (err.code === 2) msg = 'Posisi GPS tidak tersedia saat ini.';
                if (err.code === 3) msg = 'Waktu permintaan GPS habis.';
                status.innerHTML = `
                    <div class="text-rose-700 bg-rose-50 border border-rose-200 rounded-md p-1.5 font-medium text-xs">
                        <span class="font-bold">⚠️ ${msg}</span>
                        <span class="block text-[10px] text-rose-500 mt-0.5">Aktifkan izin lokasi pada browser Anda untuk mencari via GPS.</span>
                    </div>
                `;
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    }

    // Radius change handler from Map Bar
    function onMapRadiusChange(val) {
        const radius = parseFloat(val);
        window.currentRadius = radius;
        const sideSelect = document.getElementById('side-radius-select');
        if (sideSelect) sideSelect.value = radius;

        if (window.currentUserLat !== null && window.currentUserLng !== null) {
            updateGpsSearch(window.currentUserLat, window.currentUserLng, radius);
        } else {
            // Trigger GPS detection if not yet available
            detectSideGps();
        }
    }

    // Radius change handler from Sidebar
    function onSideRadiusChange(val) {
        const radius = parseFloat(val);
        window.currentRadius = radius;
        const mapSelect = document.getElementById('map-radius-select');
        if (mapSelect) mapSelect.value = radius;

        if (window.currentUserLat !== null && window.currentUserLng !== null) {
            updateGpsSearch(window.currentUserLat, window.currentUserLng, radius);
        }
    }

    // Dynamic AJAX search update
    async function updateGpsSearch(lat, lng, radius) {
        showMapLoading(true);

        const radiusLabel = document.getElementById('current-radius-label');
        if (radiusLabel) radiusLabel.textContent = `${radius} km`;

        renderMapUserAndCircle(lat, lng, radius);
        leafletMap?.setView([lat, lng], getZoomForRadius(radius));

        try {
            const res = await fetch(`/api/kost/nearby?lat=${lat}&lng=${lng}&radius=${radius}`);
            const data = await res.json();

            if (data.success) {
                renderMapProperties(data.properties, lat, lng);
                renderPropertyCards(data.properties, radius);

                const countBadge = document.getElementById('map-kost-count-badge');
                if (countBadge) countBadge.textContent = `${data.count} Kost Ditemukan`;

                const countNumber = document.getElementById('results-count-number');
                if (countNumber) countNumber.textContent = data.count;

                // Update URL parameter without full reload
                const url = new URL(window.location.href);
                url.searchParams.set('lat', lat);
                url.searchParams.set('lng', lng);
                url.searchParams.set('radius', radius);
                window.history.replaceState({}, '', url.toString());
            }
        } catch (e) {
            console.error('Gagal memperbarui kost di sekitar:', e);
        } finally {
            showMapLoading(false);
            if (window.refreshIcons) window.refreshIcons();
        }
    }

    // Dynamically render property cards below map
    function renderPropertyCards(properties, radius) {
        const container = document.getElementById('kost-results-container');
        if (!container) return;

        if (!properties || properties.length === 0) {
            container.innerHTML = `
                <div class="rounded-2xl border border-[#E4E8EF] bg-white p-12 text-center shadow-sm">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[#EEF2FF] text-[#405BE8]">
                        <i data-lucide="search-x" class="h-7 w-7"></i>
                    </div>
                    <h3 class="mt-4 text-base font-bold text-[#182230]">Belum ada kost dalam radius ${radius} km</h3>
                    <p class="mt-1 text-xs text-[#667085] max-w-sm mx-auto">
                        Coba perbesar radius pencarian menjadi 10 km, 15 km, atau 20 km untuk menemukan lebih banyak pilihan kost.
                    </p>
                    <div class="mt-5 flex items-center justify-center gap-2 flex-wrap">
                        ${radius < 10 ? `<button type="button" onclick="onMapRadiusChange(10)" class="btn-primary text-xs py-2 px-4 cursor-pointer">Radius 10 km</button>` : ''}
                        ${radius < 15 ? `<button type="button" onclick="onMapRadiusChange(15)" class="btn-primary text-xs py-2 px-4 cursor-pointer">Radius 15 km</button>` : ''}
                        ${radius < 20 ? `<button type="button" onclick="onMapRadiusChange(20)" class="btn-primary text-xs py-2 px-4 cursor-pointer">Radius 20 km</button>` : ''}
                    </div>
                </div>
            `;
            if (window.refreshIcons) window.refreshIcons();
            return;
        }

        const cardsHtml = properties.map(p => `
            <article class="card overflow-hidden group hover:border-[#405BE8]/40 hover:shadow-md transition duration-200 flex flex-col justify-between">
                <div>
                    <a href="${p.detail_url}" class="block relative aspect-[16/10] overflow-hidden bg-[#F8FAFC]">
                        <img src="${p.cover_url}"
                             alt="${p.name}"
                             class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                             onerror="this.src='/images/kost-fallback.svg'">

                        <div class="absolute top-3 left-3 flex flex-wrap items-center gap-1.5">
                            <span class="badge border border-white/60 bg-white/95 text-[#182230] backdrop-blur font-semibold shadow-xs">
                                ${p.type}
                            </span>
                            ${p.distance_text ? `
                                <span class="badge border border-[#405BE8]/30 bg-[#EEF2FF]/95 text-[#405BE8] backdrop-blur font-bold shadow-xs flex items-center gap-1">
                                    <i data-lucide="navigation" class="h-3 w-3"></i>
                                    ${p.distance_text}
                                </span>
                            ` : ''}
                        </div>
                    </a>

                    <div class="p-4 space-y-1.5">
                        <div class="flex items-center justify-between text-xs text-[#667085]">
                            <span class="flex items-center gap-1 line-clamp-1">
                                <i data-lucide="map-pin" class="h-3.5 w-3.5 text-[#405BE8] shrink-0"></i>
                                ${p.address}
                            </span>
                            <span class="flex items-center gap-1 font-semibold text-amber-500 shrink-0">
                                <i data-lucide="star" class="h-3.5 w-3.5 fill-amber-400 text-amber-400"></i>
                                ${p.rating}
                            </span>
                        </div>

                        <h3 class="text-sm font-bold text-[#182230] group-hover:text-[#405BE8] transition line-clamp-1">
                            <a href="${p.detail_url}">
                                ${p.name}
                            </a>
                        </h3>
                    </div>
                </div>

                <div class="px-4 pb-4 pt-2 border-t border-[#E4E8EF]/60 flex items-baseline justify-between">
                    <div>
                        <span class="text-sm font-black text-[#182230]">${p.price}</span>
                        <span class="text-[11px] text-[#667085]">/ bulan</span>
                    </div>
                    <a href="${p.detail_url}" class="text-xs font-semibold text-[#405BE8] hover:underline">
                        Lihat Detail →
                    </a>
                </div>
            </article>
        `).join('');

        container.innerHTML = `
            <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3" id="kost-cards-grid">
                ${cardsHtml}
            </div>
        `;

        if (window.refreshIcons) window.refreshIcons();
    }

    // Tab switching for sidebar location methods
    function setSideSearchMode(mode) {
        const modes = ['campus', 'area', 'nearby'];
        modes.forEach(m => {
            const tab = document.getElementById(`side-tab-${m}`);
            const panel = document.getElementById(`side-panel-${m}`);
            if (m === mode) {
                tab.className = 'py-1 px-1.5 rounded text-center transition bg-[#405BE8] text-white shadow-xs font-semibold';
                panel.classList.remove('hidden');
            } else {
                tab.className = 'py-1 px-1.5 rounded text-center transition text-[#667085] hover:text-[#182230]';
                panel.classList.add('hidden');
            }
        });

        const mapSection = document.getElementById('gps-interactive-map-section');

        if (mode === 'campus') {
            document.getElementById('listing-location-input').value = '';
            document.getElementById('side-lat').value = '';
            document.getElementById('side-lng').value = '';
            if (mapSection) mapSection.classList.add('hidden');
        } else if (mode === 'area') {
            document.getElementById('side-campus-select').value = '';
            document.getElementById('side-lat').value = '';
            document.getElementById('side-lng').value = '';
            if (mapSection) mapSection.classList.add('hidden');
        } else if (mode === 'nearby') {
            document.getElementById('listing-location-input').value = '';
            document.getElementById('side-campus-select').value = '';
            if (mapSection) mapSection.classList.remove('hidden');

            if (window.currentUserLat !== null && window.currentUserLng !== null) {
                if (!leafletMap) {
                    initLeafletMap(window.currentUserLat, window.currentUserLng, window.currentRadius, window.initialMapProperties);
                } else {
                    leafletMap.invalidateSize();
                }
            } else {
                detectSideGps();
            }
        }
    }

    async function loadSideCampuses(city) {
        const select = document.getElementById('side-campus-select');
        if (!city) {
            select.disabled = true;
            select.innerHTML = '<option value="">Pilih kota dulu</option>';
            return;
        }

        select.disabled = true;
        select.innerHTML = '<option value="">Memuat kampus di ' + city + '...</option>';

        try {
            const res = await fetch(`/api/campuses?city=${encodeURIComponent(city)}`);
            const campuses = await res.json();
            if (campuses.length > 0) {
                select.innerHTML = '<option value="">Pilih Kampus (' + campuses.length + ')</option>' +
                    campuses.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
                select.disabled = false;
            } else {
                select.innerHTML = '<option value="">Tidak ada kampus terdaftar</option>';
            }
        } catch(e) {
            select.innerHTML = '<option value="">Gagal memuat kampus</option>';
        }
    }

    function resetPriceFilter() {
        document.getElementById('min-price-input').value = '';
        document.getElementById('max-price-input').value = '';
        document.getElementById('main-filter-form').submit();
    }

    // Autocomplete for listing location input
    let listingLocTimer = null;
    const listingDropdown = document.getElementById('listing-location-dropdown');
    const listingInput = document.getElementById('listing-location-input');

    function handleListingLocationInput(val) {
        clearTimeout(listingLocTimer);
        listingLocTimer = setTimeout(async () => {
            if (!val.trim()) {
                listingDropdown.classList.add('hidden');
                return;
            }
            try {
                const res = await fetch(`/api/locations?q=${encodeURIComponent(val)}`);
                const data = await res.json();
                if (data.length > 0) {
                    listingDropdown.innerHTML = data.map(item => `
                        <div onclick="selectListingLocation('${item.label.replace(/'/g, "\\'")}')" class="p-2 hover:bg-[#EEF2FF] cursor-pointer flex items-center justify-between border-b border-[#E4E8EF] last:border-b-0 transition">
                            <span class="font-semibold text-[#182230]">${item.label}</span>
                            <span class="text-[9px] text-[#667085] bg-[#F8FAFC] px-1.5 py-0.5 rounded">${item.type}</span>
                        </div>
                    `).join('');
                    listingDropdown.classList.remove('hidden');
                } else {
                    listingDropdown.classList.add('hidden');
                }
            } catch (e) {
                listingDropdown.classList.add('hidden');
            }
        }, 200);
    }

    function selectListingLocation(val) {
        listingInput.value = val;
        listingDropdown.classList.add('hidden');
    }

    document.addEventListener('click', (e) => {
        if (listingInput && listingDropdown && !listingInput.contains(e.target) && !listingDropdown.contains(e.target)) {
            listingDropdown.classList.add('hidden');
        }
    });
</script>
@endsection
