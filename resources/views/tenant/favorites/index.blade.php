@extends('layouts.dashboard')
@section('title', 'Kost Favorit Saya | Kosanku')
@section('heading', 'Kost Favorit Saya')
@section('subheading', 'Kelola daftar kost yang Anda simpan untuk perbandingan dan booking')
@section('sidebar') @include('partials.sidebar-tenant') @endsection

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <p class="text-xs text-[#667085]">Menampilkan {{ $favorites->total() }} kost tersimpan</p>
        <a href="{{ route('kost.index') }}" class="btn-primary text-xs py-2 px-4 rounded-xl font-semibold flex items-center gap-1.5 shadow-sm">
            <i data-lucide="search" class="h-3.5 w-3.5"></i>
            <span>Cari Kost Lainnya</span>
        </a>
    </div>

    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($favorites as $fav)
            @php($property = $fav->property)
            @if($property)
                <div class="card overflow-hidden flex flex-col justify-between group hover:shadow-md transition">
                    <div>
                        <!-- Thumbnail Image with Availability Badge -->
                        <div class="relative aspect-video w-full overflow-hidden bg-zinc-100">
                            <img src="{{ $property->coverUrl() }}"
                                 alt="{{ $property->name }}"
                                 class="h-full w-full object-cover transition duration-300 group-hover:scale-105">

                            <!-- Top Badges -->
                            <div class="absolute top-2.5 inset-x-2.5 flex items-center justify-between">
                                <span class="badge {{ $property->type->badgeClass() }} shadow-xs text-[10px]">
                                    {{ $property->type->label() }}
                                </span>

                                <form method="POST" action="{{ route('favorites.toggle', $property) }}" onsubmit="return confirm('Hapus kost ini dari favorit Anda?')">
                                    @csrf
                                    <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-full bg-white/90 text-rose-500 shadow-sm backdrop-blur-xs hover:bg-rose-50 transition cursor-pointer" title="Hapus dari Favorit">
                                        <i data-lucide="heart" class="h-4 w-4 fill-rose-500"></i>
                                    </button>
                                </form>
                            </div>

                            <!-- Availability status overlay badge -->
                            <div class="absolute bottom-2.5 left-2.5">
                                @if($property->rooms_available > 0)
                                    <span class="badge bg-emerald-500/90 text-white font-semibold text-[10px] shadow-sm backdrop-blur-xs">
                                        Tersedia ({{ $property->rooms_available }} kamar)
                                    </span>
                                @else
                                    <span class="badge bg-rose-500/90 text-white font-semibold text-[10px] shadow-sm backdrop-blur-xs">
                                        Kamar Penuh
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Content info -->
                        <div class="p-4">
                            <div class="flex items-center gap-1.5 text-xs text-[#667085] mb-1">
                                <i data-lucide="map-pin" class="h-3.5 w-3.5 shrink-0 text-[#405BE8]"></i>
                                <span class="truncate">{{ $property->city }}{{ $property->district ? ', '.$property->district : '' }}</span>
                            </div>

                            <h3 class="font-bold text-sm text-[#182230] line-clamp-1 group-hover:text-[#405BE8] transition">
                                <a href="{{ route('kost.show', $property->slug) }}">
                                    {{ $property->name }}
                                </a>
                            </h3>

                            <div class="mt-3 flex items-baseline gap-1">
                                <span class="text-base font-extrabold text-[#182230]">{{ rupiah($property->monthly_price) }}</span>
                                <span class="text-[11px] text-[#667085]">/ bulan</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Actions -->
                    <div class="border-t border-[#E4E8EF] px-4 py-3 bg-[#F8FAFC]/50 flex items-center justify-between">
                        <form method="POST" action="{{ route('favorites.toggle', $property) }}">
                            @csrf
                            <button type="submit" class="text-xs text-rose-600 hover:text-rose-700 font-semibold flex items-center gap-1 cursor-pointer">
                                <i data-lucide="trash-2" class="h-3.5 w-3.5"></i>
                                <span>Hapus</span>
                            </button>
                        </form>

                        <a href="{{ route('kost.show', $property->slug) }}" class="btn-primary py-1.5 px-3 text-xs font-semibold rounded-lg flex items-center gap-1">
                            <span>Buka Kost</span>
                            <i data-lucide="arrow-right" class="h-3 w-3"></i>
                        </a>
                    </div>
                </div>
            @endif
        @empty
            <div class="col-span-full rounded-2xl border border-[#E4E8EF] bg-white p-12 text-center shadow-xs">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-rose-50 text-rose-500 mb-3">
                    <i data-lucide="heart" class="h-7 w-7"></i>
                </div>
                <h3 class="text-sm font-bold text-[#182230]">Belum Ada Kost Favorit</h3>
                <p class="text-xs text-[#667085] mt-1 max-w-sm mx-auto">Klik ikon hati pada kamar kost mana pun untuk menyimpannya ke daftar perbandingan ini.</p>
                <a href="{{ route('kost.index') }}" class="btn-primary mt-4 inline-flex text-xs py-2 px-4 rounded-xl font-semibold">
                    Jelajahi Kost Sekarang
                </a>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $favorites->links() }}
    </div>
</div>
@endsection

