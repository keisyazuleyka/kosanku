@extends('layouts.dashboard')
@section('title', 'Detail Booking #' . $booking->code . ' | Kosanku')
@section('heading', 'Detail Booking: ' . $booking->code)
@section('subheading', 'Rincian pengajuan sewa, riwayat status, dan resolusi sengketa')
@section('sidebar') @include('partials.sidebar-admin') @endsection

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.bookings.index') }}" class="btn-ghost text-xs inline-flex items-center gap-1.5 py-2 px-3">
        <i data-lucide="arrow-left" class="h-3.5 w-3.5"></i>
        <span>Kembali ke Daftar Booking</span>
    </a>
</div>

<div class="grid gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2 space-y-6">
        <!-- Main Info Card -->
        <div class="rounded-2xl border border-[#E4E4E7] bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-[#E4E4E7]">
                <div>
                    <span class="text-xs text-[#71717A]">Kode Pengajuan Booking</span>
                    <h2 class="text-xl font-black text-[#4263EB] font-mono">{{ $booking->code }}</h2>
                </div>
                <div>
                    <span class="badge {{ $booking->status->badgeClass() }} text-xs px-3 py-1">{{ $booking->status->label() }}</span>
                </div>
            </div>

            <div class="mt-6 grid gap-6 md:grid-cols-2">
                <div>
                    <h3 class="font-bold text-[#18181B] text-xs uppercase tracking-wider mb-2">Informasi Kost</h3>
                    <p class="font-bold text-base text-[#18181B]">{{ $booking->property->name }}</p>
                    <p class="text-xs text-[#71717A] mt-0.5">{{ $booking->property->address }}, {{ $booking->property->city }}</p>
                    <div class="mt-3 text-xs space-y-1.5 text-[#71717A]">
                        <p><span class="font-medium text-[#18181B]">Tipe Kost:</span> {{ $booking->property->type->label() }}</p>
                        <p><span class="font-medium text-[#18181B]">Tarif / Bln:</span> {{ rupiah($booking->monthly_price_snapshot) }}</p>
                        <p><span class="font-medium text-[#18181B]">Deposit:</span> {{ rupiah($booking->deposit_snapshot) }}</p>
                    </div>
                </div>

                <div>
                    <h3 class="font-bold text-[#18181B] text-xs uppercase tracking-wider mb-2">Rincian Pengajuan</h3>
                    <div class="text-xs space-y-2">
                        <p><span class="text-[#71717A]">Tanggal Rencana Masuk:</span> <strong class="text-[#18181B]">{{ $booking->start_date?->format('d F Y') }}</strong></p>
                        <p><span class="text-[#71717A]">Durasi Sewa:</span> <strong class="text-[#18181B]">{{ $booking->duration_months }} Bulan</strong></p>
                        <p><span class="text-[#71717A]">Jumlah Kamar:</span> <strong class="text-[#18181B]">{{ $booking->rooms_requested }} Kamar</strong></p>
                        <p><span class="text-[#71717A]">Total Estimasi Biaya:</span> <strong class="text-[#4263EB] text-sm">{{ rupiah($booking->total_estimate) }}</strong></p>
                    </div>
                </div>
            </div>

            @if($booking->tenant_notes)
            <div class="mt-6 border-t border-[#E4E4E7] pt-4">
                <h4 class="text-xs font-bold text-[#71717A] uppercase tracking-wide">Pesan dari Penyewa</h4>
                <p class="mt-1.5 text-xs bg-[#F5F6F8] p-3 rounded-xl text-[#18181B] leading-relaxed">{{ $booking->tenant_notes }}</p>
            </div>
            @endif
        </div>

        <!-- History Timeline -->
        <div class="rounded-2xl border border-[#E4E4E7] bg-white p-6 shadow-sm">
            <h3 class="font-bold text-[#18181B] text-sm mb-4 flex items-center gap-2">
                <i data-lucide="history" class="h-4 w-4 text-[#4263EB]"></i>
                Riwayat Perubahan Status
            </h3>
            <div class="space-y-4">
                @forelse($booking->histories as $history)
                    <div class="flex items-start gap-3 border-l-2 border-[#4263EB] pl-3 py-1">
                        <div>
                            <span class="badge {{ $history->status->badgeClass() }} text-[11px]">{{ $history->status->label() }}</span>
                            <p class="text-[11px] text-[#71717A] mt-1">
                                Oleh: <strong class="text-[#18181B]">{{ $history->actor?->name ?? 'Sistem' }}</strong> ({{ $history->actor?->role?->label() ?? '-' }}) · {{ $history->created_at?->format('d M Y, H:i') }}
                            </p>
                            @if($history->notes)
                                <p class="text-xs mt-1 bg-[#F5F6F8] p-2 rounded-lg text-[#18181B]">{{ $history->notes }}</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-[#71717A]">Belum ada riwayat tercatat.</p>
                @endforelse
            </div>
        </div>

        <!-- Admin Notes / Moderation -->
        <div class="rounded-2xl border border-[#E4E4E7] bg-white p-6 shadow-sm">
            <h3 class="font-bold text-[#18181B] text-sm mb-1 flex items-center gap-2">
                <i data-lucide="file-text" class="h-4 w-4 text-[#4263EB]"></i>
                Catatan Sengketa / Moderasi Admin
            </h3>
            <p class="text-xs text-[#71717A] mb-4">Tambahkan catatan administratif terkait sengketa ini. Catatan akan disimpan secara aman ke audit log.</p>
            <form method="POST" action="{{ route('admin.bookings.note', $booking) }}" class="space-y-3">
                @csrf
                <textarea class="input text-xs" name="admin_notes" rows="3" placeholder="Tulis catatan sengketa atau penanganan booking..." required></textarea>
                <div class="flex justify-end">
                    <button type="submit" class="btn-primary text-xs py-2 px-5 font-semibold">
                        Simpan Catatan ke Audit Log
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Right Side Information -->
    <div class="space-y-6">
        <div class="rounded-2xl border border-[#E4E4E7] bg-white p-6 shadow-sm space-y-3 text-xs">
            <h3 class="font-bold text-[#18181B] text-sm border-b border-[#E4E4E7] pb-3 flex items-center gap-2">
                <i data-lucide="user" class="h-4 w-4 text-[#4263EB]"></i>
                Penyewa
            </h3>
            <p class="font-bold text-xs text-[#18181B]">{{ $booking->tenant?->name }}</p>
            <p class="text-[#71717A]">{{ $booking->tenant?->email }}</p>
            <p class="text-[#71717A]">{{ $booking->tenant?->phone ?: 'No HP belum diisi' }}</p>
            @if($booking->tenant)
                <a href="{{ route('admin.users.show', $booking->tenant) }}" class="btn-ghost w-full text-center text-xs block py-1.5 mt-2">Buka Profil Penyewa</a>
            @endif
        </div>

        <div class="rounded-2xl border border-[#E4E4E7] bg-white p-6 shadow-sm space-y-3 text-xs">
            <h3 class="font-bold text-[#18181B] text-sm border-b border-[#E4E4E7] pb-3 flex items-center gap-2">
                <i data-lucide="building-2" class="h-4 w-4 text-[#4263EB]"></i>
                Pemilik Kost
            </h3>
            <p class="font-bold text-xs text-[#18181B]">{{ $booking->owner?->name }}</p>
            <p class="text-[#71717A]">{{ $booking->owner?->email }}</p>
            <p class="text-[#71717A]">{{ $booking->owner?->phone ?: 'No HP belum diisi' }}</p>
            @if($booking->owner)
                <a href="{{ route('admin.users.show', $booking->owner) }}" class="btn-ghost w-full text-center text-xs block py-1.5 mt-2">Buka Profil Pemilik</a>
            @endif
        </div>

        @if($reports->isNotEmpty())
        <div class="rounded-2xl border border-rose-200 bg-rose-50/20 p-5 shadow-sm">
            <h3 class="font-bold text-rose-800 text-xs uppercase tracking-wide mb-3 flex items-center gap-1.5">
                <i data-lucide="flag" class="h-3.5 w-3.5 text-rose-600"></i>
                Laporan Terkait ({{ $reports->count() }})
            </h3>
            <div class="space-y-2.5">
                @foreach($reports as $rep)
                    <div class="p-3 bg-white rounded-xl border border-rose-100 text-xs">
                        <span class="text-[10px] text-[#71717A]">{{ $rep->created_at?->format('d M Y') }}</span>
                        <p class="mt-1 font-semibold text-[#18181B]">{{ $rep->reason }}</p>
                        <p class="mt-1 text-[11px] text-[#71717A]">Status: {{ $rep->status->label() }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
