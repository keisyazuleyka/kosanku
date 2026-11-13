@extends('layouts.dashboard')
@section('title', 'Verifikasi Listing Kost | Kosanku')
@section('heading', 'Antrian Verifikasi Kost')
@section('subheading', 'Periksa keaslian data, foto, fasilitas, dan kelayakan kost')
@section('sidebar') @include('partials.sidebar-admin') @endsection

@section('content')
<!-- Status Tabs -->
<div class="mb-6 flex flex-wrap gap-2">
    @foreach($statuses as $status)
        @php
            $isActive = $currentStatus === $status->value;
        @endphp
        <a class="rounded-xl px-4 py-2 text-xs font-semibold transition border {{ $isActive ? 'bg-[#4263EB] text-white border-[#4263EB] shadow-sm' : 'bg-white text-[#71717A] border-[#E4E4E7] hover:border-[#4263EB] hover:text-[#4263EB]' }}" href="{{ route('admin.verifikasi.index', ['status' => $status->value]) }}">
            {{ $status->label() }}
        </a>
    @endforeach
</div>

<!-- Property Cards -->
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    @forelse($properties as $property)
        <div class="rounded-2xl border border-[#E4E4E7] bg-white p-5 shadow-sm flex flex-col justify-between hover:shadow-md transition">
            <div>
                <div class="flex items-center justify-between gap-2 pb-3 border-b border-[#E4E4E7]">
                    <span class="badge {{ $property->status->badgeClass() }}">{{ $property->status->label() }}</span>
                    <span class="text-[11px] text-[#71717A]">{{ $property->created_at?->diffForHumans() }}</span>
                </div>

                <div class="mt-4 flex gap-3.5">
                    <div class="h-14 w-14 shrink-0 rounded-xl overflow-hidden bg-zinc-100 border border-[#E4E4E7]">
                        <img src="{{ $property->coverUrl() }}" alt="{{ $property->name }}" class="h-full w-full object-cover" onerror="this.src='{{ asset('images/kost-fallback.svg') }}'">
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-[#18181B] leading-snug">{{ $property->name }}</h3>
                        <p class="text-xs text-[#71717A] mt-0.5">{{ $property->city }} · {{ $property->type->label() }}</p>
                    </div>
                </div>

                <div class="mt-4 rounded-xl bg-[#F5F6F8] p-3 text-xs text-[#71717A] space-y-1">
                    <p><span class="font-medium text-[#18181B]">Pemilik:</span> {{ $property->owner->name }}</p>
                    <p><span class="font-medium text-[#18181B]">Harga:</span> {{ rupiah($property->monthly_price) }} / bln</p>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-[#E4E4E7] flex justify-end">
                <a href="{{ route('admin.verifikasi.show', $property) }}" class="btn-primary text-xs py-1.5 px-4 font-semibold inline-flex items-center gap-1.5">
                    <span>Periksa Listing</span>
                    <i data-lucide="arrow-right" class="h-3.5 w-3.5"></i>
                </a>
            </div>
        </div>
    @empty
        <div class="col-span-full rounded-2xl border border-[#E4E4E7] bg-white p-12 text-center shadow-sm">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[#EFF2FE] text-[#4263EB]">
                <i data-lucide="shield-check" class="h-7 w-7"></i>
            </div>
            <h3 class="mt-4 text-base font-bold text-[#18181B]">Tidak ada antrian verifikasi</h3>
            <p class="mt-1 text-xs text-[#71717A]">Semua listing dengan status ini telah diproses.</p>
        </div>
    @endforelse
</div>

<div class="mt-6">
    {{ $properties->links() }}
</div>
@endsection
