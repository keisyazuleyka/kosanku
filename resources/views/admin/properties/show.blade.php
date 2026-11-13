@extends('layouts.dashboard')
@section('title', 'Detail Kost: ' . $property->name)
@section('heading', $property->name)
@section('subheading', $property->district ? $property->district . ', ' . $property->city : $property->city)
@section('sidebar') @include('partials.sidebar-admin') @endsection

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <a href="{{ route('admin.kost.index') }}" class="btn-ghost text-xs inline-flex items-center gap-1.5 py-2 px-3">
        <i data-lucide="arrow-left" class="h-3.5 w-3.5"></i>
        <span>Kembali ke Daftar Kost</span>
    </a>

    <div class="flex flex-wrap items-center gap-2">
        <a class="btn-ghost text-xs py-2 px-3 inline-flex items-center gap-1" href="{{ route('admin.kost.edit', $property) }}">
            <i data-lucide="edit" class="h-3.5 w-3.5"></i>
            <span>Edit</span>
        </a>

        @if($property->status->value !== 'suspended')
            <form method="POST" action="{{ route('admin.kost.suspend', $property) }}" data-confirm="Yakin ingin menangguhkan listing ini?">
                @csrf
                <button class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-700 hover:bg-amber-100 transition">
                    Tangguhkan
                </button>
            </form>
        @else
            <form method="POST" action="{{ route('admin.kost.activate', $property) }}" data-confirm="Aktifkan kembali listing ini?">
                @csrf
                <button class="btn-primary text-xs py-2 px-3">
                    Aktifkan
                </button>
            </form>
        @endif

        @if($property->status->value !== 'archived')
            <form method="POST" action="{{ route('admin.kost.archive', $property) }}" data-confirm="Arsipkan listing ini? Listing tidak akan tampil di pencarian publik.">
                @csrf
                <button class="rounded-xl border border-zinc-300 bg-zinc-100 px-3 py-2 text-xs font-semibold text-zinc-700 hover:bg-zinc-200 transition">
                    Arsipkan
                </button>
            </form>
        @endif
    </div>
</div>

<div class="grid gap-6 lg:grid-cols-3">
    <!-- Main Info -->
    <div class="lg:col-span-2 space-y-6">
        <div class="rounded-2xl border border-[#E4E4E7] bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-2 pb-4 border-b border-[#E4E4E7]">
                <div>
                    <span class="badge {{ $property->status->badgeClass() }}">{{ $property->status->label() }}</span>
                    <span class="badge bg-[#F5F6F8] text-[#18181B] ml-2">{{ $property->type->label() }}</span>
                    @if($property->is_featured)
                        <span class="badge bg-amber-50 text-amber-700 border border-amber-200 ml-2">Unggulan</span>
                    @endif
                </div>
                <span class="text-xs text-[#71717A]">ID Properti: #{{ $property->id }}</span>
            </div>

            <div class="mt-4 grid gap-4 sm:grid-cols-2 text-xs">
                <div>
                    <span class="text-[#71717A]">Pemilik Kost:</span>
                    <p class="font-bold text-[#18181B] text-sm mt-0.5">{{ $property->owner->name }}</p>
                    <p class="text-[#71717A]">{{ $property->owner->email }} · {{ $property->owner->phone ?: '-' }}</p>
                </div>
                <div>
                    <span class="text-[#71717A]">Tarif Sewa:</span>
                    <p class="font-bold text-base text-[#18181B] mt-0.5">{{ rupiah($property->monthly_price) }} <span class="text-xs font-normal text-[#71717A]">/ bulan</span></p>
                    <p class="text-[#71717A]">Deposit: {{ rupiah($property->deposit) }}</p>
                </div>
                <div>
                    <span class="text-[#71717A]">Ketersediaan Kamar:</span>
                    <p class="font-bold text-[#18181B] mt-0.5">{{ $property->rooms_available }} dari {{ $property->rooms_total }} kamar</p>
                </div>
                <div>
                    <span class="text-[#71717A]">Alamat Lengkap:</span>
                    <p class="font-medium text-[#18181B] mt-0.5">{{ $property->address }}, {{ $property->district }}, {{ $property->city }}</p>
                </div>
            </div>

            <div class="mt-6 border-t border-[#E4E4E7] pt-4">
                <h4 class="text-xs font-bold text-[#18181B] uppercase tracking-wide mb-2">Deskripsi Properti</h4>
                <p class="text-xs text-[#71717A] leading-relaxed whitespace-pre-line">{{ $property->description }}</p>
            </div>
        </div>

        <!-- Facilities & Rules -->
        <div class="rounded-2xl border border-[#E4E4E7] bg-white p-6 shadow-sm">
            <h4 class="text-xs font-bold text-[#18181B] uppercase tracking-wide mb-3">Fasilitas Properti</h4>
            <div class="flex flex-wrap gap-2">
                @forelse($property->facilities as $fac)
                    <span class="badge bg-[#F5F6F8] text-[#18181B] border border-[#E4E4E7]">{{ $fac->name }}</span>
                @empty
                    <span class="text-xs text-[#71717A]">Tidak ada data fasilitas</span>
                @endforelse
            </div>

            <div class="mt-6 border-t border-[#E4E4E7] pt-4">
                <h4 class="text-xs font-bold text-[#18181B] uppercase tracking-wide mb-2">Tata Tertib & Aturan</h4>
                <p class="text-xs text-[#71717A] whitespace-pre-line">{{ $property->rules ?: 'Tidak ada aturan khusus' }}</p>
            </div>
        </div>
    </div>

    <!-- Right Side Actions & Danger Zone -->
    <div class="space-y-6">
        <div class="rounded-2xl border border-[#E4E4E7] bg-white p-5 shadow-sm">
            <h4 class="text-xs font-bold text-[#18181B] uppercase tracking-wide mb-3">Foto Properti ({{ $property->images->count() }})</h4>
            <div class="grid grid-cols-2 gap-2">
                @forelse($property->images as $img)
                    <div class="h-24 rounded-lg overflow-hidden border border-[#E4E4E7] bg-zinc-100">
                        <img src="{{ $img->url() }}" class="h-full w-full object-cover" onerror="this.src='{{ asset('images/kost-fallback.svg') }}'">
                    </div>
                @empty
                    <p class="text-xs text-[#71717A] col-span-2">Tidak ada foto diunggah.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-rose-200 bg-rose-50/20 p-5 shadow-sm">
            <h4 class="text-xs font-bold text-rose-700 uppercase tracking-wide mb-2">Zona Berbahaya</h4>
            <p class="text-[11px] text-[#71717A] mb-4">Menghapus kost akan menghilangkan seluruh data foto dan riwayat yang terkait.</p>
            
            <form method="POST" action="{{ route('admin.kost.destroy', $property) }}" data-confirm="Hapus permanen listing ini? Tindakan tidak dapat dibatalkan.">
                @csrf
                @method('DELETE')
                <input class="input text-xs mb-2" name="confirm" placeholder="Ketik HAPUS untuk konfirmasi" required>
                <button type="submit" class="btn-danger w-full py-2 text-xs font-semibold">
                    Hapus Permanen Listing
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
