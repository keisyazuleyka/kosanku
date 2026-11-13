@php
    $property = $property ?? null;
    $favoriteIds = $favoriteIds ?? (auth()->check() ? auth()->user()->favorites()->pluck('property_id') : collect());
    $isFav = $property && $favoriteIds->contains($property->id);
@endphp
<article class="card overflow-hidden group hover:border-[#405BE8]/40 hover:shadow-md transition duration-200 flex flex-col justify-between">
    <div>
        <a href="{{ route('kost.show', $property->slug) }}" class="block relative aspect-[16/10] overflow-hidden bg-[#F8FAFC]">
            <img src="{{ $property->coverUrl() }}"
                 alt="{{ $property->name }}"
                 class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                 onerror="this.src='{{ asset('images/kost-fallback.svg') }}'">

            <!-- Type Badge Overlay -->
            <div class="absolute top-3 left-3 flex flex-wrap items-center gap-1.5">
                <span class="badge border border-white/60 bg-white/95 text-[#182230] backdrop-blur font-semibold shadow-xs">
                    {{ $property->type->label() }}
                </span>
                @if(isset($property->distance_km))
                    <span class="badge border border-[#405BE8]/30 bg-[#EEF2FF]/95 text-[#405BE8] backdrop-blur font-bold shadow-xs flex items-center gap-1">
                        <i data-lucide="navigation" class="h-3 w-3"></i>
                        {{ number_format((float) $property->distance_km, 1) }} km
                    </span>
                @endif
                @if($property->is_demo)
                    <span class="badge bg-black/60 text-white backdrop-blur text-[10px]">Demo</span>
                @endif
            </div>

            <!-- Favorite toggle button -->
            <div class="absolute top-3 right-3" onclick="event.stopPropagation()">
                @auth
                    <button type="button"
                            onclick="toggleCardFavorite(event, {{ $property->id }}, this)"
                            title="{{ $isFav ? 'Hapus dari favorit' : 'Tambah ke favorit' }}"
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-white/95 {{ $isFav ? 'text-rose-500' : 'text-[#667085]' }} shadow-xs backdrop-blur hover:text-rose-500 hover:scale-110 transition cursor-pointer">
                        <i data-lucide="heart" class="h-4 w-4 {{ $isFav ? 'fill-rose-500 text-rose-500' : '' }}"></i>
                    </button>
                @else
                    <button type="button"
                            onclick="window.openAuthModal('login')"
                            title="Masuk untuk menyimpan favorit"
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-white/95 text-[#667085] shadow-xs backdrop-blur hover:text-rose-500 hover:scale-110 transition cursor-pointer">
                        <i data-lucide="heart" class="h-4 w-4"></i>
                    </button>
                @endauth
            </div>

            <!-- Availability banner if 0 rooms left -->
            @if($property->rooms_available <= 0)
                <div class="absolute inset-x-0 bottom-0 bg-rose-600/90 text-white text-[10px] font-bold py-1 px-3 text-center backdrop-blur-xs">
                    Penuh (0 Kamar Tersedia)
                </div>
            @endif
        </a>

        <div class="p-4 space-y-1.5">
            <div class="flex items-center justify-between text-xs text-[#667085]">
                <span class="flex items-center gap-1 line-clamp-1">
                    <i data-lucide="map-pin" class="h-3.5 w-3.5 text-[#405BE8] shrink-0"></i>
                    {{ $property->district ? $property->district . ', ' : '' }}{{ $property->city }}
                </span>
                <span class="flex items-center gap-1 font-semibold text-amber-500 shrink-0">
                    <i data-lucide="star" class="h-3.5 w-3.5 fill-amber-400 text-amber-400"></i>
                    {{ $property->averageRating() ? number_format($property->averageRating(), 1) : '4.8' }}
                </span>
            </div>

            <h3 class="text-sm font-bold text-[#182230] group-hover:text-[#405BE8] transition line-clamp-1">
                <a href="{{ route('kost.show', $property->slug) }}">
                    {{ $property->name }}
                </a>
            </h3>

            <p class="text-xs text-[#667085] line-clamp-1">
                {{ $property->facilities->take(3)->pluck('name')->join(' · ') }}
            </p>
        </div>
    </div>

    <div class="px-4 pb-4 pt-2 border-t border-[#E4E8EF]/60 flex items-baseline justify-between">
        <div>
            <span class="text-sm font-black text-[#182230]">{{ rupiah($property->monthly_price) }}</span>
            <span class="text-[11px] text-[#667085]">/ bulan</span>
        </div>
        <a href="{{ route('kost.show', $property->slug) }}" class="text-xs font-semibold text-[#405BE8] hover:underline">
            Detail →
        </a>
    </div>
</article>
