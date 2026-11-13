@extends('layouts.dashboard')
@section('title', 'Periksa Verifikasi: ' . $property->name)
@section('heading', 'Pemeriksaan Verifikasi')
@section('subheading', $property->name . ' — Milik ' . $property->owner->name)
@section('sidebar') @include('partials.sidebar-admin') @endsection

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.verifikasi.index') }}" class="btn-ghost text-xs inline-flex items-center gap-1.5 py-2 px-3">
        <i data-lucide="arrow-left" class="h-3.5 w-3.5"></i>
        <span>Kembali ke Antrian Verifikasi</span>
    </a>
</div>

<!-- Photo Gallery Strip -->
<div class="grid gap-3 sm:grid-cols-3 lg:grid-cols-4">
    @forelse($property->images as $image)
        <div class="h-44 rounded-xl overflow-hidden border border-[#E4E4E7] bg-zinc-100">
            <img src="{{ $image->url() }}" class="h-full w-full object-cover" alt="" onerror="this.src='{{ asset('images/kost-fallback.svg') }}'">
        </div>
    @empty
        <div class="col-span-full rounded-xl bg-zinc-100 p-8 text-center text-xs text-[#71717A]">
            Tidak ada foto yang diunggah oleh pemilik.
        </div>
    @endforelse
</div>

<!-- Main Review Details -->
<div class="mt-6 grid gap-6 lg:grid-cols-3">
    <!-- Left Column: Data Summary -->
    <div class="lg:col-span-2 space-y-6">
        <div class="rounded-2xl border border-[#E4E4E7] bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-[#E4E4E7]">
                <h3 class="text-sm font-bold text-[#18181B] flex items-center gap-2">
                    <i data-lucide="file-check" class="h-4 w-4 text-[#4263EB]"></i>
                    Informasi Listing & Pemilik
                </h3>
                <span class="badge {{ $property->status->badgeClass() }}">{{ $property->status->label() }}</span>
            </div>

            <div class="mt-4 grid gap-4 sm:grid-cols-2 text-xs">
                <div>
                    <span class="text-[#71717A]">Data Pemilik:</span>
                    <p class="font-bold text-[#18181B] text-sm mt-0.5">{{ $property->owner->name }}</p>
                    <p class="text-[#71717A]">{{ $property->owner->email }} · {{ $property->owner->phone ?: 'No telp (-)' }}</p>
                </div>
                <div>
                    <span class="text-[#71717A]">Alamat & Lokasi:</span>
                    <p class="font-bold text-[#18181B] mt-0.5">{{ $property->city }} ({{ $property->district }})</p>
                    <p class="text-[#71717A]">{{ $property->address }}</p>
                </div>
                <div>
                    <span class="text-[#71717A]">Harga & Deposit:</span>
                    <p class="font-bold text-base text-[#18181B] mt-0.5">{{ rupiah($property->monthly_price) }} / bln</p>
                    <p class="text-[#71717A]">Deposit: {{ rupiah($property->deposit) }}</p>
                </div>
                <div>
                    <span class="text-[#71717A]">Kapasitas Kamar:</span>
                    <p class="font-bold text-[#18181B] mt-0.5">{{ $property->rooms_available }} dari {{ $property->rooms_total }} kamar tersedia</p>
                </div>
            </div>

            <div class="mt-4 border-t border-[#E4E4E7] pt-4">
                <span class="text-xs font-semibold text-[#71717A]">Fasilitas:</span>
                <p class="text-xs text-[#18181B] font-medium mt-1">{{ $property->facilities->pluck('name')->join(', ') ?: 'Tidak ada' }}</p>
            </div>

            <div class="mt-4 border-t border-[#E4E4E7] pt-4">
                <span class="text-xs font-semibold text-[#71717A]">Tata Tertib & Aturan:</span>
                <p class="text-xs text-[#71717A] whitespace-pre-line mt-1">{{ $property->rules ?: 'Tidak ada aturan' }}</p>
            </div>
        </div>

        <!-- Decision History -->
        <div class="rounded-2xl border border-[#E4E4E7] bg-white p-6 shadow-sm">
            <h3 class="text-sm font-bold text-[#18181B] mb-4 flex items-center gap-2">
                <i data-lucide="history" class="h-4 w-4 text-[#4263EB]"></i>
                Riwayat Keputusan Admin
            </h3>
            <div class="space-y-3">
                @forelse($history as $log)
                    <div class="flex items-start gap-3 border-l-2 border-[#4263EB] pl-3 py-1 text-xs">
                        <div>
                            <p class="font-semibold text-[#18181B]">{{ $log->action }}</p>
                            <p class="text-[11px] text-[#71717A]">{{ $log->created_at?->format('d M Y, H:i') }} · Oleh: <strong>{{ $log->actor?->name ?? 'Admin' }}</strong></p>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-[#71717A]">Belum ada riwayat keputusan untuk kost ini.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Right Column: Action Decisions -->
    <div class="space-y-4">
        <!-- Approve -->
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50/30 p-5 shadow-sm">
            <h4 class="text-xs font-bold text-emerald-800 uppercase tracking-wide mb-1 flex items-center gap-1.5">
                <i data-lucide="check-circle-2" class="h-4 w-4 text-emerald-600"></i>
                Setujui Listing (Publish)
            </h4>
            <p class="text-xs text-[#71717A] mb-3">Listing akan berstatus Approved dan dapat langsung dicari dan dibooking di halaman publik.</p>
            <form method="POST" action="{{ route('admin.verifikasi.approve', $property) }}" data-confirm="Setujui listing ini untuk tayang ke publik?">
                @csrf
                <button class="btn-primary w-full py-2.5 text-xs font-semibold bg-emerald-600 hover:bg-emerald-700">
                    ✓ Setujui & Publikasikan
                </button>
            </form>
        </div>

        <!-- Revision -->
        <div class="rounded-2xl border border-amber-200 bg-amber-50/30 p-5 shadow-sm">
            <h4 class="text-xs font-bold text-amber-800 uppercase tracking-wide mb-1 flex items-center gap-1.5">
                <i data-lucide="alert-circle" class="h-4 w-4 text-amber-600"></i>
                Minta Revisi
            </h4>
            <p class="text-xs text-[#71717A] mb-3">Kirimkan catatan perbaikan kepada pemilik agar foto atau detail deskripsi dilengkapi.</p>
            <form method="POST" action="{{ route('admin.verifikasi.revision', $property) }}" class="space-y-2">
                @csrf
                <textarea class="input text-xs" name="revision_request" rows="2" placeholder="Tuliskan detail perbaikan yang diminta..." required></textarea>
                <button class="w-full rounded-xl border border-amber-300 bg-white py-2 text-xs font-semibold text-amber-700 hover:bg-amber-50 transition">
                    Kirim Permintaan Revisi
                </button>
            </form>
        </div>

        <!-- Reject -->
        <div class="rounded-2xl border border-rose-200 bg-rose-50/30 p-5 shadow-sm">
            <h4 class="text-xs font-bold text-rose-800 uppercase tracking-wide mb-1 flex items-center gap-1.5">
                <i data-lucide="x-circle" class="h-4 w-4 text-rose-600"></i>
                Tolak Listing
            </h4>
            <p class="text-xs text-[#71717A] mb-3">Listing akan berstatus Rejected dengan alasan yang disampaikan ke pemilik kost.</p>
            <form method="POST" action="{{ route('admin.verifikasi.reject', $property) }}" class="space-y-2">
                @csrf
                <textarea class="input text-xs" name="rejection_reason" rows="2" placeholder="Alasan penolakan listing..." required></textarea>
                <button class="btn-danger w-full py-2 text-xs font-semibold">
                    Tolak Listing Kost
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
