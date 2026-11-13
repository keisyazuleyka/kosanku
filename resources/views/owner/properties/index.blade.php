@extends('layouts.dashboard')
@section('title', 'Kost Saya | Kosanku')
@section('heading', 'Kost Saya')
@section('subheading', 'Daftar semua unit kost yang Anda kelola')
@section('sidebar') @include('partials.sidebar-owner') @endsection

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <p class="text-xs text-[#71717A]">Total listing terdaftar: <strong class="text-[#18181B]">{{ $properties->total() }}</strong></p>
    <a href="{{ route('owner.kost.create') }}" class="btn-primary text-xs py-2 px-4 flex items-center gap-1.5 self-start sm:self-auto">
        <i data-lucide="plus" class="h-3.5 w-3.5"></i>
        <span>Tambah Kost Baru</span>
    </a>
</div>

<div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
    @forelse($properties as $property)
        <div class="rounded-2xl border border-[#E4E4E7] bg-white overflow-hidden shadow-sm flex flex-col justify-between hover:shadow-md transition">
            <div>
                <div class="relative h-44 bg-zinc-100">
                    <img src="{{ $property->coverUrl() }}" alt="{{ $property->name }}" class="h-full w-full object-cover" onerror="this.src='{{ asset('images/kost-fallback.svg') }}'">
                    <span class="absolute top-3 left-3 badge {{ $property->status->badgeClass() }}">
                        {{ $property->status->label() }}
                    </span>
                    <span class="absolute top-3 right-3 badge bg-white/90 text-[#18181B] font-semibold backdrop-blur shadow-sm">
                        {{ $property->type->label() }}
                    </span>
                </div>

                <div class="p-5 space-y-2">
                    <h3 class="text-base font-bold text-[#18181B]">
                        <a href="{{ route('kost.show', $property->slug) }}" target="_blank" class="hover:text-[#4263EB] transition">
                            {{ $property->name }}
                        </a>
                    </h3>
                    <p class="text-xs text-[#71717A] flex items-center gap-1">
                        <i data-lucide="map-pin" class="h-3.5 w-3.5 text-[#4263EB]"></i>
                        {{ $property->district ? $property->district . ', ' : '' }}{{ $property->city }}
                    </p>
                    <p class="text-base font-black text-[#18181B]">
                        {{ rupiah($property->monthly_price) }} <span class="text-xs font-normal text-[#71717A]">/ bulan</span>
                    </p>
                    <p class="text-xs text-[#18181B]">
                        Kamar tersedia: <strong class="text-[#4263EB]">{{ $property->rooms_available }}</strong> dari {{ $property->rooms_total }}
                    </p>

                    @if($property->status->value === 'rejected' && $property->rejection_reason)
                        <div class="mt-3 p-3 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-800">
                            <strong>Alasan Penolakan:</strong> {{ $property->rejection_reason }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="p-4 pt-0 border-t border-[#E4E4E7] flex items-center justify-between gap-2 mt-4">
                <a href="{{ route('owner.kost.edit', $property) }}" class="btn-ghost text-xs flex-1 text-center py-2">
                    Edit Kost
                </a>
                <form method="POST" action="{{ route('owner.kost.destroy', $property) }}" data-confirm="Yakin ingin menghapus listing kost ini?">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="confirm" value="HAPUS">
                    <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-700 py-2 px-3">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="col-span-full rounded-2xl border border-[#E4E4E7] bg-white p-12 text-center shadow-sm">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[#EFF2FE] text-[#4263EB]">
                <i data-lucide="building-2" class="h-7 w-7"></i>
            </div>
            <h3 class="mt-4 text-base font-bold text-[#18181B]">Anda belum memiliki listing kost</h3>
            <p class="mt-1 text-xs text-[#71717A] max-w-sm mx-auto">Mulai tayangkan kost Anda untuk menjangkau ribuan calon penyewa.</p>
            <a href="{{ route('owner.kost.create') }}" class="btn-primary mt-5 inline-flex text-xs py-2 px-5">
                Tambah Kost Sekarang
            </a>
        </div>
    @endforelse
</div>

<div class="mt-6">
    {{ $properties->links() }}
</div>
@endsection
