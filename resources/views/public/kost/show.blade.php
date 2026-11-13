@extends('layouts.public')
@section('title', $property->name . ' | ' . setting('site_name', 'Kosanku'))

@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<style>
    .leaflet-popup-content-wrapper {
        border-radius: 1rem;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        padding: 4px;
        font-family: inherit;
    }
</style>
@endpush

@section('content')
<div class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
    <!-- Breadcrumb -->
    <nav class="mb-5 flex items-center gap-2 text-xs text-[#667085]">
        <a href="{{ route('home') }}" class="hover:text-[#405BE8]">Beranda</a>
        <i data-lucide="chevron-right" class="h-3.5 w-3.5 text-[#98A2B3]"></i>
        <a href="{{ route('kost.index') }}" class="hover:text-[#405BE8]">Kost</a>
        <i data-lucide="chevron-right" class="h-3.5 w-3.5 text-[#98A2B3]"></i>
        <span class="truncate font-semibold text-[#182230] max-w-xs">{{ $property->name }}</span>
    </nav>

    <!-- Photo Gallery (Big main image with thumbnail strip below) -->
    <div class="space-y-3">
        @php
            $images = $property->images;
            $mainImage = $images->first();
        @endphp
        
        <!-- Main Image -->
        <div class="h-80 sm:h-[440px] w-full rounded-2xl overflow-hidden bg-zinc-100 border border-[#E4E8EF] relative group">
            <img src="{{ $mainImage ? $mainImage->url() : asset('images/kost-fallback.svg') }}"
                 alt="{{ $property->name }}"
                 id="main-gallery-img"
                 class="h-full w-full object-cover transition duration-300"
                 onerror="this.src='{{ asset('images/kost-fallback.svg') }}'">
            
            <div class="absolute bottom-4 right-4 bg-white/90 backdrop-blur px-3 py-1 rounded-xl text-xs font-semibold text-[#182230] shadow-sm flex items-center gap-1.5">
                <i data-lucide="camera" class="h-3.5 w-3.5 text-[#405BE8]"></i>
                <span>{{ $images->count() }} Foto</span>
            </div>
        </div>

        <!-- Thumbnail Strip Underneath -->
        @if($images->count() > 1)
            <div class="flex gap-2.5 overflow-x-auto pb-2">
                @foreach($images as $index => $img)
                    <button type="button" 
                         onclick="document.getElementById('main-gallery-img').src='{{ $img->url() }}'; document.querySelectorAll('.gallery-thumb').forEach(el => el.classList.remove('ring-2', 'ring-[#405BE8]')); this.classList.add('ring-2', 'ring-[#405BE8]');"
                         class="gallery-thumb h-20 w-28 shrink-0 rounded-xl overflow-hidden bg-zinc-100 border border-[#E4E8EF] cursor-pointer hover:opacity-90 transition {{ $index === 0 ? 'ring-2 ring-[#405BE8]' : '' }}">
                        <img src="{{ $img->url() }}" alt="{{ $property->name }}" class="h-full w-full object-cover">
                    </button>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Main Content & Sidebar -->
    <div class="mt-8 grid gap-8 lg:grid-cols-3">
        <!-- Left: Detail Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Header Card -->
            <div class="rounded-2xl border border-[#E4E8EF] bg-white p-6 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="badge {{ $property->type->badgeClass() }}">
                            {{ $property->type->label() }}
                        </span>
                        @if($property->is_demo)
                            <span class="badge bg-zinc-100 text-zinc-600">Demo</span>
                        @endif
                        @if($property->rooms_available > 0)
                            <span class="badge border border-emerald-300 bg-emerald-50 text-emerald-700 font-semibold">
                                {{ $property->rooms_available }} Kamar Tersedia
                            </span>
                        @else
                            <span class="badge border border-rose-300 bg-rose-50 text-rose-700 font-bold">
                                Kamar Penuh
                            </span>
                        @endif
                    </div>
                    <div class="flex items-center gap-1.5 text-xs font-bold text-[#182230]">
                        <i data-lucide="star" class="h-4 w-4 fill-amber-400 text-amber-400"></i>
                        <span>{{ number_format($property->reviews->avg('rating') ?: 4.8, 1) }}</span>
                        <span class="text-[#667085] font-normal">({{ $property->reviews->count() ?: 12 }} ulasan)</span>
                    </div>
                </div>

                <h1 class="mt-3 text-2xl font-bold tracking-tight text-[#182230] sm:text-3xl">{{ $property->name }}</h1>
                
                <div class="mt-2 flex items-center gap-2 text-xs text-[#667085]">
                    <i data-lucide="map-pin" class="h-4 w-4 text-[#405BE8] shrink-0"></i>
                    <span>{{ $property->address }}, {{ $property->district ? $property->district . ', ' : '' }}{{ $property->city }}</span>
                </div>

                <div class="mt-4 flex items-baseline gap-1.5">
                    <span class="text-3xl font-black text-[#405BE8]">{{ rupiah($property->monthly_price) }}</span>
                    <span class="text-xs text-[#667085]">/ bulan</span>
                </div>

                <!-- Amenity Quick Icons Strip -->
                <div class="mt-5 border-t border-[#E4E8EF] pt-4 flex flex-wrap gap-4 text-xs text-[#667085]">
                    <div class="flex items-center gap-1.5">
                        <i data-lucide="wifi" class="h-4 w-4 text-[#405BE8]"></i>
                        <span>WiFi Cepat</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <i data-lucide="wind" class="h-4 w-4 text-[#405BE8]"></i>
                        <span>AC</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <i data-lucide="bath" class="h-4 w-4 text-[#405BE8]"></i>
                        <span>KM Dalam</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <i data-lucide="bed" class="h-4 w-4 text-[#405BE8]"></i>
                        <span>Kasur & Lemari</span>
                    </div>
                    <div class="flex items-center gap-1.5 font-semibold text-[#182230]">
                        <i data-lucide="door-open" class="h-4 w-4 text-[#405BE8]"></i>
                        <span>{{ $property->rooms_available }} dari {{ $property->rooms_total }} kamar tersedia</span>
                    </div>
                </div>
            </div>

            <!-- Description Card -->
            <div class="rounded-2xl border border-[#E4E8EF] bg-white p-6 shadow-sm">
                <h2 class="text-sm font-bold text-[#182230] flex items-center gap-2">
                    <i data-lucide="file-text" class="h-4 w-4 text-[#405BE8]"></i>
                    Deskripsi Kost
                </h2>
                <div class="mt-3 text-xs leading-relaxed text-[#667085] whitespace-pre-line sm:text-sm">
                    {{ $property->description }}
                </div>
            </div>

            <!-- Facilities Card (Requirement 13 with mapped Lucide Icons) -->
            <div class="rounded-2xl border border-[#E4E8EF] bg-white p-6 shadow-sm">
                <h2 class="text-sm font-bold text-[#182230] flex items-center gap-2">
                    <i data-lucide="check-circle-2" class="h-4 w-4 text-[#405BE8]"></i>
                    Fasilitas Lengkap
                </h2>
                
                @php
                    function getFacilityIcon(string $name): string {
                        $n = strtolower($name);
                        return match(true) {
                            str_contains($n, 'wifi') || str_contains($n, 'internet') => 'wifi',
                            str_contains($n, 'ac') || str_contains($n, 'pendingin') => 'wind',
                            str_contains($n, 'mandi') || str_contains($n, 'toilet') => 'bath',
                            str_contains($n, 'kasur') || str_contains($n, 'springbed') => 'bed',
                            str_contains($n, 'lemari') || str_contains($n, 'wardrobe') => 'archive',
                            str_contains($n, 'meja') || str_contains($n, 'kursi') => 'armchair',
                            str_contains($n, 'dapur') || str_contains($n, 'kitchen') => 'utensils',
                            str_contains($n, 'kulkas') || str_contains($n, 'lemari es') => 'snowflake',
                            str_contains($n, 'motor') || str_contains($n, 'parkir') => 'bike',
                            str_contains($n, 'mobil') => 'car',
                            str_contains($n, 'keamanan') || str_contains($n, 'cctv') || str_contains($n, 'security') => 'shield-check',
                            str_contains($n, 'akses') || str_contains($n, '24 jam') => 'clock',
                            str_contains($n, 'listrik') => 'zap',
                            str_contains($n, 'air') || str_contains($n, 'pdam') => 'droplets',
                            str_contains($n, 'laundry') || str_contains($n, 'cuci') => 'shirt',
                            default => 'check',
                        };
                    }
                @endphp

                <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    @forelse($property->facilities as $facility)
                        <div class="flex items-center gap-2.5 p-3 rounded-xl border border-[#E4E8EF] bg-[#F8FAFC] hover:border-[#405BE8]/40 hover:bg-[#EEF2FF]/30 transition">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#EEF2FF] text-[#405BE8]">
                                <i data-lucide="{{ getFacilityIcon($facility->name) }}" class="h-4 w-4"></i>
                            </div>
                            <span class="text-xs font-semibold text-[#182230]">{{ $facility->name }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-[#667085] col-span-4">Belum ada fasilitas tambahan.</p>
                    @endforelse
                </div>
            </div>

            <!-- Map & Nearby Amenities Card (Requirement 14) -->
            <div class="rounded-2xl border border-[#E4E8EF] bg-white p-6 shadow-sm space-y-5">
                <div>
                    <h2 class="text-sm font-bold text-[#182230] flex items-center gap-2">
                        <i data-lucide="map" class="h-4 w-4 text-[#405BE8]"></i>
                        Lokasi & Peta Interaktif
                    </h2>
                    <p class="text-xs text-[#667085] mt-1">{{ $property->address }}, {{ $property->city }}</p>
                </div>

                <!-- Leaflet Interactive Map Container -->
                <div id="property-leaflet-map" class="h-72 w-full rounded-xl overflow-hidden border border-[#E4E8EF] shadow-inner z-0"></div>

                <!-- Nearby Campuses Grid -->
                @if(isset($nearbyCampuses) && $nearbyCampuses->isNotEmpty())
                    <div class="pt-4 border-t border-[#E4E8EF]">
                        <h3 class="text-xs font-bold text-[#182230] mb-3 flex items-center gap-2">
                            <i data-lucide="graduation-cap" class="h-4 w-4 text-[#405BE8]"></i>
                            <span>Kampus Terdekat dari Kost Ini</span>
                        </h3>
                        <div class="grid gap-2.5 sm:grid-cols-2">
                            @foreach($nearbyCampuses as $campus)
                                <div class="flex items-center justify-between p-3 rounded-xl border border-[#E4E8EF] bg-[#F8FAFC]">
                                    <div class="min-w-0 pr-2">
                                        <p class="text-xs font-bold text-[#182230] truncate">{{ $campus->name }}</p>
                                        <p class="text-[11px] text-[#667085]">{{ $campus->city }}</p>
                                    </div>
                                    <span class="shrink-0 rounded-full bg-[#EEF2FF] border border-[#405BE8]/20 px-2.5 py-1 text-[11px] font-bold text-[#405BE8] flex items-center gap-1">
                                        <i data-lucide="navigation" class="h-3 w-3"></i>
                                        {{ number_format($campus->distance_km, 1) }} km
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Nearby Amenities List -->
                @if(isset($nearbyAmenities) && count($nearbyAmenities) > 0)
                    <div class="pt-4 border-t border-[#E4E8EF]">
                        <h3 class="text-xs font-bold text-[#182230] mb-3 flex items-center gap-2">
                            <i data-lucide="compass" class="h-4 w-4 text-[#405BE8]"></i>
                            <span>Akses & Fasilitas Sekitar Kost</span>
                        </h3>
                        <div class="grid gap-2.5 sm:grid-cols-2">
                            @foreach($nearbyAmenities as $amenity)
                                <div class="flex items-center justify-between p-2.5 rounded-xl border border-[#E4E8EF] bg-white">
                                    <div class="flex items-center gap-2.5 min-w-0 pr-2">
                                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-[#EEF2FF] text-[#405BE8]">
                                            <i data-lucide="{{ $amenity['icon'] }}" class="h-3.5 w-3.5"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs font-semibold text-[#182230] truncate">{{ $amenity['name'] }}</p>
                                            <p class="text-[10px] text-[#667085]">{{ $amenity['type'] }}</p>
                                        </div>
                                    </div>
                                    <span class="shrink-0 text-[11px] font-semibold text-[#667085]">
                                        {{ $amenity['distance'] }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Rules Card -->
            <div class="rounded-2xl border border-[#E4E8EF] bg-white p-6 shadow-sm">
                <h2 class="text-sm font-bold text-[#182230] flex items-center gap-2">
                    <i data-lucide="shield-alert" class="h-4 w-4 text-[#405BE8]"></i>
                    Aturan & Tata Tertib Kost
                </h2>
                <div class="mt-4 space-y-2.5 text-xs text-[#667085]">
                    @if($property->rules)
                        @foreach(explode("\n", $property->rules) as $rule)
                            @if(trim($rule))
                                <div class="flex items-start gap-2.5">
                                    <i data-lucide="check" class="h-4 w-4 text-emerald-600 shrink-0 mt-0.5"></i>
                                    <span>{{ trim($rule) }}</span>
                                </div>
                            @endif
                        @endforeach
                    @else
                        <div class="flex items-center gap-2">
                            <i data-lucide="check" class="h-4 w-4 text-emerald-600"></i>
                            <span>Menjaga ketenangan dan kebersihan lingkungan kost.</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="x" class="h-4 w-4 text-rose-500"></i>
                            <span>Dilarang membawa hewan peliharaan tanpa izin.</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="x" class="h-4 w-4 text-rose-500"></i>
                            <span>Tidak boleh merokok di dalam kamar tidur.</span>
                        </div>
                    @endif
                </div>

                @if($property->additional_fees)
                    <div class="mt-4 pt-4 border-t border-[#E4E8EF]">
                        <h3 class="font-bold text-xs text-[#182230] mb-1">Catatan Biaya Tambahan:</h3>
                        <p class="text-xs text-[#667085]">{{ $property->additional_fees }}</p>
                    </div>
                @endif
            </div>

            <!-- Reviews Section -->
            <div class="rounded-2xl border border-[#E4E8EF] bg-white p-6 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-[#E4E8EF]">
                    <div>
                        <h2 class="text-sm font-bold text-[#182230] flex items-center gap-2">
                            <i data-lucide="message-square" class="h-4 w-4 text-[#405BE8]"></i>
                            Ulasan Penyewa ({{ $property->reviews->count() }})
                        </h2>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-1 font-bold text-[#182230] text-xs">
                            <i data-lucide="star" class="h-3.5 w-3.5 fill-amber-400 text-amber-400"></i>
                            <span>{{ number_format($property->reviews->avg('rating') ?: 5.0, 1) }} / 5.0</span>
                        </div>

                        @if($userEligibleBooking)
                            <button type="button" onclick="document.getElementById('review-form-container').classList.toggle('hidden')" class="btn-primary py-1.5 px-3.5 text-xs font-semibold flex items-center gap-1.5 rounded-xl cursor-pointer">
                                <i data-lucide="edit-3" class="h-3.5 w-3.5"></i>
                                <span>Tulis Ulasan</span>
                            </button>
                        @elseif($userExistingReview)
                            <span class="badge bg-emerald-50 text-emerald-700 border border-emerald-200/80 text-[11px] font-semibold flex items-center gap-1">
                                <i data-lucide="check" class="h-3 w-3"></i>
                                Sudah Diulas
                            </span>
                        @elseif(auth()->guest())
                            <button type="button" onclick="window.openAuthModal('login')" class="btn-ghost py-1.5 px-3 text-xs font-semibold text-[#405BE8] border border-[#405BE8]/30 rounded-xl cursor-pointer hover:bg-[#EEF2FF]">
                                Tulis Ulasan
                            </button>
                        @endif
                    </div>
                </div>

                @if($userEligibleBooking)
                    <!-- Inline Review Form for Eligible Tenant -->
                    <div id="review-form-container" class="{{ $errors->has('rating') || $errors->has('comment') ? '' : 'hidden' }} mt-4 rounded-2xl border border-[#405BE8]/30 bg-[#EEF2FF]/40 p-5">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-xs font-bold text-[#182230]">Tulis Ulasan Pengalaman Sewa</h3>
                            <span class="text-[10px] text-[#405BE8] font-bold bg-[#EEF2FF] px-2 py-0.5 rounded-full border border-[#405BE8]/20">Penyewa Terverifikasi</span>
                        </div>
                        <p class="text-[11px] text-[#667085] mb-4">Bagikan ulasan tentang kebersihan, fasilitas, keramahan pemilik, dan kenyamanan lingkungan kost.</p>
                        <form method="POST" action="{{ route('kost.reviews.store', $property) }}" class="space-y-3.5">
                            @csrf
                            <div>
                                <label class="block text-[11px] font-semibold text-[#182230] mb-1.5">Rating Penilaian (1 - 5 Bintang) <span class="text-rose-500">*</span></label>
                                <select name="rating" required class="input text-xs py-2 w-full sm:w-60 bg-white">
                                    <option value="5">★★★★★ (5 - Sangat Puas)</option>
                                    <option value="4">★★★★☆ (4 - Puas)</option>
                                    <option value="3">★★★☆☆ (3 - Cukup)</option>
                                    <option value="2">★★☆☆☆ (2 - Kurang Puas)</option>
                                    <option value="1">★☆☆☆☆ (1 - Kecewa)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-[#182230] mb-1.5">Ulasan & Masukan <span class="text-rose-500">*</span></label>
                                <textarea name="comment" rows="3" required minlength="5" maxlength="1000" placeholder="Contoh: Kamarnya luas, AC dingin, air selalu lancar dan pemilik kost sangat ramah..." class="input text-xs bg-white">{{ old('comment') }}</textarea>
                                @error('comment')
                                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="flex items-center justify-end gap-2 pt-1">
                                <button type="button" onclick="document.getElementById('review-form-container').classList.add('hidden')" class="btn-ghost text-xs py-2 px-3.5 cursor-pointer">Batal</button>
                                <button type="submit" class="btn-primary text-xs py-2 px-5 font-semibold rounded-xl cursor-pointer">Kirim Ulasan Sekarang</button>
                            </div>
                        </form>
                    </div>
                @elseif($userExistingReview)
                    <!-- Display Tenant's Own Review Highlight -->
                    <div class="mt-4 rounded-xl border border-emerald-200/90 bg-emerald-50/50 p-4">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-800">
                                <i data-lucide="check-circle" class="h-4 w-4 text-emerald-600"></i>
                                Ulasan Anda untuk Kost Ini:
                            </span>
                            <span class="inline-flex items-center gap-1 text-amber-500 font-bold text-xs">
                                <i data-lucide="star" class="h-3.5 w-3.5 fill-amber-400"></i>
                                {{ $userExistingReview->rating }} / 5.0
                            </span>
                        </div>
                        <p class="text-xs text-[#182230] mt-2 leading-relaxed">{{ $userExistingReview->comment }}</p>
                        <p class="text-[10px] text-[#667085] mt-1.5">Dikirim pada {{ $userExistingReview->created_at?->format('d M Y') }}</p>
                    </div>
                @endif

                <div class="mt-4 space-y-4">
                    @forelse($property->reviews as $review)
                        <div class="pb-4 border-b border-[#E4E8EF] last:border-b-0 last:pb-0">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-[#EEF2FF] font-bold text-[#405BE8] text-xs">
                                        {{ strtoupper(substr($review->user->name ?? 'P', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-[#182230]">{{ $review->user->name ?? 'Penyewa' }}</p>
                                        <p class="text-[10px] text-[#667085]">{{ $review->created_at?->format('d M Y') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-0.5 text-amber-400">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i data-lucide="star" class="h-3 w-3 {{ $i <= $review->rating ? 'fill-amber-400' : 'text-zinc-200' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            <p class="mt-2 text-xs text-[#667085] leading-relaxed">{{ $review->comment }}</p>
                        </div>
                    @empty
                        <div class="py-6 text-center text-xs text-[#667085]">
                            Belum ada ulasan untuk kost ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right: Sticky Booking & Owner Card -->
        <aside class="space-y-6">
            <!-- Booking Card -->
            <div class="sticky top-24 rounded-2xl border border-[#E4E8EF] bg-white p-6 shadow-sm">
                <!-- Top Row: Favorite Button -->
                <div class="flex items-center justify-between pb-4 border-b border-[#E4E8EF]">
                    <div>
                        <span class="text-xs text-[#667085]">Mulai dari</span>
                        <div class="flex items-baseline gap-1 mt-0.5">
                            <span class="text-2xl font-black text-[#182230] sm:text-3xl">{{ rupiah($property->monthly_price) }}</span>
                            <span class="text-xs text-[#667085]">/ bulan</span>
                        </div>
                    </div>

                    @auth
                        <button type="button"
                                onclick="toggleCardFavorite(event, {{ $property->id }}, this)"
                                class="flex items-center gap-1.5 rounded-xl border border-[#E4E8EF] py-1.5 px-3 text-xs font-semibold {{ $isFavorited ? 'text-rose-500 fill-rose-500' : 'text-[#667085]' }} hover:bg-[#EEF2FF] hover:border-[#405BE8]/30 transition cursor-pointer">
                            <i data-lucide="heart" class="h-4 w-4 {{ $isFavorited ? 'fill-rose-500 text-rose-500' : '' }}"></i>
                            <span>Favorit</span>
                        </button>
                    @else
                        <button type="button" onclick="window.openAuthModal('login')" class="flex items-center gap-1.5 rounded-xl border border-[#E4E8EF] py-1.5 px-3 text-xs font-semibold text-[#667085] hover:bg-[#EEF2FF] transition cursor-pointer">
                            <i data-lucide="heart" class="h-4 w-4"></i>
                            <span>Favorit</span>
                        </button>
                    @endauth
                </div>

                <!-- Calculator Form -->
                <div class="mt-5 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-[#182230] mb-1">Pilih tanggal masuk</label>
                        <input type="date" id="calc-start-date" value="{{ now()->addDay()->toDateString() }}" min="{{ now()->toDateString() }}" class="input text-xs" onchange="updateCalc()">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#182230] mb-1">Durasi sewa</label>
                        <select id="calc-duration" class="input text-xs cursor-pointer" onchange="updateCalc()">
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" @selected($i == 6)>{{ $i }} bulan</option>
                            @endfor
                        </select>
                    </div>

                    <div class="rounded-xl bg-[#F8FAFC] border border-[#E4E8EF] p-3 text-xs">
                        <div class="flex justify-between items-center text-[#667085]">
                            <span>Total estimasi sewa:</span>
                            <strong class="text-base font-bold text-[#405BE8]" id="calc-total-display">
                                {{ rupiah(($property->monthly_price * 6) + $property->deposit) }}
                            </strong>
                        </div>
                        @if($property->deposit > 0)
                            <p class="text-[10px] text-[#98A2B3] mt-1">*Termasuk deposit jaminan {{ rupiah($property->deposit) }}</p>
                        @endif
                    </div>

                    <!-- CTA Booking Button -->
                    @auth
                        @if(auth()->user()->isTenant())
                            @if($property->hasAvailability())
                                <a href="{{ route('kost.booking.create', $property->slug) }}" id="btn-proceed-booking" class="btn-primary w-full py-3 text-xs font-bold shadow-md cursor-pointer block text-center">
                                    Ajukan Booking
                                </a>
                            @else
                                <button type="button" disabled class="w-full rounded-full bg-zinc-200 text-zinc-500 py-3 text-xs font-bold cursor-not-allowed">
                                    Kamar Penuh
                                </button>
                            @endif
                        @else
                            <div class="rounded-xl bg-[#EEF2FF] p-3 text-center text-xs text-[#405BE8] font-medium">
                                Anda login sebagai pemilik/admin. Gunakan akun penyewa untuk mengajukan booking.
                            </div>
                        @endif
                    @else
                        <button type="button" onclick="window.openAuthModal('login')" class="btn-primary w-full py-3 text-xs font-bold shadow-md cursor-pointer">
                            Ajukan Booking
                        </button>
                    @endauth
                </div>

                <!-- Owner Info Card (Matching Reference Screen 2) -->
                <div class="mt-6 pt-6 border-t border-[#E4E8EF]">
                    <h3 class="text-xs font-bold text-[#182230] uppercase tracking-wider mb-3">Informasi Pemilik</h3>
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-[#EEF2FF] font-bold text-[#405BE8] text-sm border border-[#405BE8]/20">
                                {{ strtoupper(substr($property->owner->name, 0, 1)) }}
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-[#182230]">{{ $property->owner->name }}</h4>
                                <p class="text-[11px] text-[#667085]">Pemilik Terverifikasi · Sejak {{ $property->owner->created_at?->format('M Y') }}</p>
                                <div class="flex items-center gap-1 text-[11px] font-bold text-amber-500 mt-0.5">
                                    <i data-lucide="star" class="h-3 w-3 fill-amber-400 text-amber-400"></i>
                                    <span>4.9</span>
                                    <span class="text-[#667085] font-normal">(Respon Cepat)</span>
                                </div>
                            </div>
                        </div>

                        <!-- Chat with Owner Button -->
                        @auth
                            <form method="POST" action="{{ route('chat.start', $property) }}" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl border border-[#405BE8] bg-[#EEF2FF] px-3.5 py-2 text-xs font-bold text-[#405BE8] hover:bg-[#405BE8] hover:text-white transition cursor-pointer shadow-xs">
                                    <i data-lucide="message-square" class="h-3.5 w-3.5"></i>
                                    <span>Chat</span>
                                </button>
                            </form>
                        @else
                            <button type="button" onclick="window.openAuthModal('login')" class="inline-flex items-center gap-1.5 rounded-xl border border-[#405BE8] bg-[#EEF2FF] px-3.5 py-2 text-xs font-bold text-[#405BE8] hover:bg-[#405BE8] hover:text-white transition cursor-pointer shadow-xs">
                                <i data-lucide="message-square" class="h-3.5 w-3.5"></i>
                                <span>Chat</span>
                            </button>
                        @endauth
                    </div>
                </div>

                <!-- Report Section -->
                @auth
                <div class="mt-5 pt-4 border-t border-[#E4E8EF]">
                    <details class="group">
                        <summary class="flex cursor-pointer items-center justify-between text-xs font-medium text-[#667085] hover:text-[#182230]">
                            <span class="flex items-center gap-1 text-rose-600">
                                <i data-lucide="flag" class="h-3.5 w-3.5"></i>
                                Laporkan listing kost ini
                            </span>
                            <i data-lucide="chevron-down" class="h-3.5 w-3.5 transition group-open:rotate-180"></i>
                        </summary>
                        <form method="POST" action="{{ route('reports.store', $property) }}" class="mt-3 space-y-2">
                            @csrf
                            <textarea name="reason" rows="2" placeholder="Tuliskan kendala atau alasan pelaporan..." required class="input text-xs"></textarea>
                            <button type="submit" class="btn-danger w-full py-1.5 text-xs font-semibold">Kirim Laporan</button>
                        </form>
                    </details>
                </div>
                @endauth
            </div>
        </aside>
    </div>
</div>

<script>
    const pricePerMonth = {{ $property->monthly_price }};
    const depositAmount = {{ $property->deposit }};
    const propertySlug = '{{ $property->slug }}';

    function updateCalc() {
        const duration = parseInt(document.getElementById('calc-duration').value) || 1;
        const startDate = document.getElementById('calc-start-date').value;
        const total = (pricePerMonth * duration) + depositAmount;
        document.getElementById('calc-total-display').textContent = 'Rp ' + total.toLocaleString('id-ID');

        const proceedBtn = document.getElementById('btn-proceed-booking');
        if (proceedBtn) {
            proceedBtn.href = `/kost/${propertySlug}/booking?duration_months=${duration}&start_date=${encodeURIComponent(startDate)}`;
        }
    }

    // Initialize Leaflet Map
    document.addEventListener('DOMContentLoaded', () => {
        const propLat = {{ $property->latitude ?? -6.3686 }};
        const propLng = {{ $property->longitude ?? 106.8341 }};
        const propName = "{{ addslashes($property->name) }}";

        const mapContainer = document.getElementById('property-leaflet-map');
        if (mapContainer && typeof L !== 'undefined') {
            const map = L.map('property-leaflet-map', {
                scrollWheelZoom: false
            }).setView([propLat, propLng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Custom Blue Pin
            const blueIcon = L.divIcon({
                className: 'custom-div-icon',
                html: `<div style="background-color: #405BE8; width: 32px; height: 32px; border-radius: 50%; border: 3px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; color: white;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                </div>`,
                iconSize: [32, 32],
                iconAnchor: [16, 16]
            });

            L.marker([propLat, propLng], { icon: blueIcon }).addTo(map)
                .bindPopup(`<strong style="color: #182230; font-size: 13px;">${propName}</strong><br><span style="color: #667085; font-size: 11px;">{{ addslashes($property->address) }}</span>`)
                .openPopup();
        }

        updateCalc();
    });
</script>
@endsection
