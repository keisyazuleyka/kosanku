@extends('layouts.public')
@section('title', 'Kost Favorit Saya | ' . setting('site_name', 'Kosanku'))

@section('content')
<div class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#18181B] sm:text-3xl">Kost Favorit Saya</h1>
            <p class="mt-1 text-xs text-[#71717A] sm:text-sm">Daftar kost yang Anda simpan untuk pertimbangan masa depan.</p>
        </div>
        <a href="{{ route('kost.index') }}" class="btn-primary text-xs py-2 px-4">
            <i data-lucide="plus" class="h-3.5 w-3.5"></i>
            <span>Cari Kost Lainnya</span>
        </a>
    </div>

    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($favorites as $favorite)
            @if($favorite->property)
                @include('partials.property-card', ['property' => $favorite->property])
            @endif
        @empty
            <div class="col-span-full rounded-2xl border border-[#E4E4E7] bg-white p-12 text-center shadow-sm">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-rose-50 text-rose-500">
                    <i data-lucide="heart" class="h-7 w-7"></i>
                </div>
                <h3 class="mt-4 text-base font-bold text-[#18181B]">Belum ada kost favorit</h3>
                <p class="mt-1 text-xs text-[#71717A] max-w-sm mx-auto">Klik ikon hati pada listing kost mana pun untuk menyimpannya ke daftar ini.</p>
                <a href="{{ route('kost.index') }}" class="btn-primary mt-5 inline-flex text-xs py-2 px-5">
                    Mulai Jelajahi Kost
                </a>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $favorites->links() }}
    </div>
</div>
@endsection
