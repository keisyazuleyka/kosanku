@extends('layouts.dashboard')
@section('title', 'Proses Booking #' . $booking->code . ' | Kosanku')
@section('heading', 'Detail Pengajuan Sewa: ' . $booking->code)
@section('subheading', 'Konfirmasi atau tolak pengajuan sewa kamar dari calon penyewa')
@section('sidebar') @include('partials.sidebar-owner') @endsection

@section('content')
<div class="mb-6">
    <a href="{{ route('owner.bookings.index') }}" class="btn-ghost text-xs inline-flex items-center gap-1.5 py-2 px-3">
        <i data-lucide="arrow-left" class="h-3.5 w-3.5"></i>
        <span>Kembali ke Daftar Booking</span>
    </a>
</div>

<div class="grid gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2 space-y-6">
        <!-- Main Details Card -->
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
                    <h3 class="font-bold text-[#18181B] text-xs uppercase tracking-wider mb-2">Unit Kost</h3>
                    <p class="font-bold text-base text-[#18181B]">{{ $booking->property->name }}</p>
                    <p class="text-xs text-[#71717A] mt-0.5">{{ $booking->property->address }}, {{ $booking->property->city }}</p>
                    <div class="mt-3 text-xs space-y-1.5 text-[#71717A]">
                        <p><span class="font-medium text-[#18181B]">Harga Sewa:</span> {{ rupiah($booking->monthly_price_snapshot) }} / bulan</p>
                        <p><span class="font-medium text-[#18181B]">Deposit:</span> {{ rupiah($booking->deposit_snapshot) }}</p>
                        <p><span class="font-medium text-[#18181B]">Ketersediaan:</span> {{ $booking->property->rooms_available }} dari {{ $booking->property->rooms_total }} kamar</p>
                    </div>
                </div>

                <div>
                    <h3 class="font-bold text-[#18181B] text-xs uppercase tracking-wider mb-2">Rincian Permohonan</h3>
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
                <h4 class="text-xs font-bold text-[#71717A] uppercase tracking-wide">Pesan dari Calon Penyewa</h4>
                <p class="mt-1.5 text-xs bg-[#F5F6F8] p-3 rounded-xl text-[#18181B] leading-relaxed">{{ $booking->tenant_notes }}</p>
            </div>
            @endif
        </div>

        <!-- Action Card for Pending -->
        @if($booking->isPending())
        <div class="rounded-2xl border border-amber-200 bg-amber-50/30 p-6 shadow-sm">
            <h3 class="font-bold text-[#18181B] text-sm flex items-center gap-2 mb-2">
                <i data-lucide="alert-circle" class="h-4 w-4 text-amber-600"></i>
                Tindakan Persetujuan Booking
            </h3>
            <p class="text-xs text-[#71717A] mb-4 leading-relaxed">
                Menyetujui booking akan mengurangi ketersediaan kamar secara otomatis. Pembayaran disepakati langsung antara pemilik dan penyewa secara offline saat serah terima kunci.
            </p>

            <div class="grid gap-4 md:grid-cols-2 pt-2">
                <form method="POST" action="{{ route('owner.bookings.approve', $booking) }}" class="space-y-3" data-confirm="Setujui permohonan sewa ini?">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-[#18181B] mb-1">Catatan Persetujuan (Opsional)</label>
                        <textarea class="input text-xs" name="notes" rows="2" placeholder="Contoh: Silakan datang check-in mulai jam 10.00 WIB"></textarea>
                    </div>
                    <button type="submit" class="btn-primary w-full py-2.5 text-xs font-semibold bg-emerald-600 hover:bg-emerald-700">
                        <i data-lucide="check" class="h-3.5 w-3.5"></i>
                        <span>Setujui Pengajuan Sewa</span>
                    </button>
                </form>

                <form method="POST" action="{{ route('owner.bookings.reject', $booking) }}" class="space-y-3" data-confirm="Yakin tolak pengajuan ini?">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-rose-700 mb-1">Alasan Penolakan (Wajib)</label>
                        <textarea class="input text-xs" name="notes" rows="2" placeholder="Contoh: Kamar sudah terisi penuh secara offline"></textarea>
                    </div>
                    <button type="submit" class="w-full rounded-xl border border-rose-200 bg-white py-2.5 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition">
                        Tolak Pengajuan
                    </button>
                </form>
            </div>
        </div>
        @elseif($booking->isApproved())
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50/30 p-6 shadow-sm">
            <h3 class="font-bold text-[#18181B] text-sm flex items-center gap-2 mb-2">
                <i data-lucide="check-circle-2" class="h-4 w-4 text-emerald-600"></i>
                Selesaikan Masa Sewa
            </h3>
            <p class="text-xs text-[#71717A] mb-4">Jika masa sewa penyewa sudah selesai dan serah terima kunci dilakukan, Anda dapat menandai booking ini sebagai Selesai.</p>
            <form method="POST" action="{{ route('owner.bookings.complete', $booking) }}" data-confirm="Tandai masa sewa ini telah selesai?">
                @csrf
                <button type="submit" class="btn-primary text-xs py-2 px-4 bg-emerald-600 hover:bg-emerald-700">
                    Tandai Selesai
                </button>
            </form>
        </div>
        @endif

        <!-- History Timeline -->
        <div class="rounded-2xl border border-[#E4E4E7] bg-white p-6 shadow-sm">
            <h3 class="font-bold text-[#18181B] text-sm mb-4 flex items-center gap-2">
                <i data-lucide="history" class="h-4 w-4 text-[#4263EB]"></i>
                Riwayat Status Pengajuan
            </h3>
            <div class="space-y-4">
                @forelse($booking->histories as $history)
                    <div class="flex items-start gap-3 border-l-2 border-[#4263EB] pl-3 py-1">
                        <div>
                            <span class="badge {{ $history->status->badgeClass() }} text-[11px]">{{ $history->status->label() }}</span>
                            <p class="text-[11px] text-[#71717A] mt-1">
                                Oleh: <strong class="text-[#18181B]">{{ $history->actor?->name ?? 'Sistem' }}</strong> · {{ $history->created_at?->format('d M Y, H:i') }}
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
    </div>

    <!-- Right Sidebar Contact -->
    <div>
        <div class="rounded-2xl border border-[#E4E4E7] bg-white p-6 shadow-sm space-y-4">
            <h3 class="font-bold text-[#18181B] text-sm border-b border-[#E4E4E7] pb-3 flex items-center gap-2">
                <i data-lucide="user-check" class="h-4 w-4 text-[#4263EB]"></i>
                Kontak Calon Penyewa
            </h3>
            <div>
                <p class="text-[11px] text-[#71717A]">Nama Lengkap</p>
                <p class="font-bold text-xs text-[#18181B] mt-0.5">{{ $booking->tenant?->name ?? 'Anonim' }}</p>
            </div>
            <div>
                <p class="text-[11px] text-[#71717A]">Alamat Email</p>
                <p class="text-xs text-[#18181B] mt-0.5">{{ $booking->tenant?->email ?? '-' }}</p>
            </div>
            <div>
                <p class="text-[11px] text-[#71717A]">Nomor WhatsApp / HP</p>
                <p class="text-xs font-bold text-[#18181B] mt-0.5">{{ $booking->tenant?->phone ?: 'Belum diisi' }}</p>
            </div>
            @if($booking->tenant?->phone)
                <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $booking->tenant->phone)) }}" target="_blank" class="btn-primary w-full py-2.5 text-center text-xs font-semibold flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700">
                    <i data-lucide="message-circle" class="h-4 w-4"></i>
                    <span>Hubungi via WhatsApp</span>
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
