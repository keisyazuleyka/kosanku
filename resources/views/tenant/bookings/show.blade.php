@extends('layouts.dashboard')
@section('title', 'Booking #' . $booking->code . ' | Kosanku')
@section('heading', 'Detail Booking: ' . $booking->code)
@section('subheading', 'Informasi pengajuan sewa, status persetujuan, dan kontak pemilik')
@section('sidebar') @include('partials.sidebar-tenant') @endsection

@section('content')
<div class="mb-6">
    <a href="{{ route('tenant.bookings.index') }}" class="btn-ghost text-xs inline-flex items-center gap-1.5 py-2 px-3">
        <i data-lucide="arrow-left" class="h-3.5 w-3.5"></i>
        <span>Kembali ke Riwayat Booking</span>
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
                    <h3 class="font-bold text-[#18181B] text-xs uppercase tracking-wider mb-2">Kost yang Diajukan</h3>
                    <p class="font-bold text-base text-[#18181B]">
                        <a href="{{ route('kost.show', $booking->property->slug) }}" class="hover:text-[#4263EB] transition">
                            {{ $booking->property->name }}
                        </a>
                    </p>
                    <p class="text-xs text-[#71717A] mt-0.5">{{ $booking->property->address }}, {{ $booking->property->city }}</p>
                    <div class="mt-3 text-xs space-y-1.5 text-[#71717A]">
                        <p><span class="font-medium text-[#18181B]">Harga Sewa:</span> {{ rupiah($booking->monthly_price_snapshot) }} / bulan</p>
                        <p><span class="font-medium text-[#18181B]">Deposit:</span> {{ rupiah($booking->deposit_snapshot) }}</p>
                    </div>
                </div>

                <div>
                    <h3 class="font-bold text-[#18181B] text-xs uppercase tracking-wider mb-2">Rincian Pengajuan</h3>
                    <div class="text-xs space-y-2">
                        <p><span class="text-[#71717A]">Rencana Masuk:</span> <strong class="text-[#18181B]">{{ $booking->start_date?->format('d F Y') }}</strong></p>
                        <p><span class="text-[#71717A]">Durasi Sewa:</span> <strong class="text-[#18181B]">{{ $booking->duration_months }} Bulan</strong></p>
                        <p><span class="text-[#71717A]">Jumlah Kamar:</span> <strong class="text-[#18181B]">{{ $booking->rooms_requested }} Kamar</strong></p>
                        <p><span class="text-[#71717A]">Total Estimasi Biaya:</span> <strong class="text-[#4263EB] text-sm">{{ rupiah($booking->total_estimate) }}</strong></p>
                    </div>
                </div>
            </div>

            @if($booking->tenant_notes)
            <div class="mt-6 border-t border-[#E4E4E7] pt-4">
                <h4 class="text-xs font-bold text-[#71717A] uppercase tracking-wide">Catatan Anda Saat Mengajukan</h4>
                <p class="mt-1.5 text-xs bg-[#F5F6F8] p-3 rounded-xl text-[#18181B] leading-relaxed">{{ $booking->tenant_notes }}</p>
            </div>
            @endif

            @if($booking->owner_notes)
            <div class="mt-6 border-t border-[#E4E4E7] pt-4">
                <h4 class="text-xs font-bold text-[#4263EB] uppercase tracking-wide">Catatan dari Pemilik Kost</h4>
                <p class="mt-1.5 text-xs bg-[#EFF2FE]/60 p-3 rounded-xl text-[#18181B] leading-relaxed">{{ $booking->owner_notes }}</p>
            </div>
            @endif

            <div class="mt-6 border-t border-[#E4E4E7] pt-4 text-xs text-[#71717A]">
                <p><strong>Informasi:</strong> Status ini merupakan pengajuan permohonan sewa. Transaksi pembayaran tidak diproses melalui gateway kartu otomatis di web ini; selesaikan pembayaran langsung dengan pemilik kost sesuai perjanjian.</p>
            </div>
        </div>

        @if($booking->canBeCancelledByTenant())
        <div class="rounded-2xl border border-rose-200 bg-rose-50/20 p-6 shadow-sm">
            <h3 class="font-bold text-rose-700 text-sm mb-1">Batalkan Pengajuan Sewa</h3>
            <p class="text-xs text-[#71717A] mb-4">Pengajuan masih menunggu konfirmasi pemilik dan dapat dibatalkan jika Anda berubah pikiran.</p>
            <form method="POST" action="{{ route('tenant.bookings.cancel', $booking) }}" class="space-y-3" data-confirm="Yakin ingin membatalkan pengajuan sewa ini?">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-[#18181B] mb-1">Alasan Pembatalan <span class="text-rose-500">*</span></label>
                    <textarea class="input text-xs" name="cancel_reason" rows="2" placeholder="Tulis alasan pembatalan (minimal 5 karakter)..." required minlength="5"></textarea>
                </div>
                <button type="submit" class="btn-danger py-2 px-4 text-xs font-semibold">
                    Batalkan Pengajuan
                </button>
            </form>
        </div>
        @endif

        @if($booking->canBeReviewed())
        <div class="rounded-2xl border border-[#4263EB]/20 bg-[#EFF2FE]/30 p-6 shadow-sm">
            <h3 class="font-bold text-[#18181B] text-sm mb-1">Beri Ulasan untuk Kost Ini</h3>
            <p class="text-xs text-[#71717A] mb-4">Masa sewa Anda telah selesai. Bagikan pengalaman tinggal Anda untuk membantu calon penyewa lainnya.</p>
            <form method="POST" action="{{ route('tenant.reviews.store', $booking) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-[#18181B] mb-1">Rating Penilaian (1 - 5) <span class="text-rose-500">*</span></label>
                    <select class="input text-xs" name="rating" required>
                        <option value="5">★★★★★ (5 - Sangat Puas)</option>
                        <option value="4">★★★★☆ (4 - Puas)</option>
                        <option value="3">★★★☆☆ (3 - Cukup)</option>
                        <option value="2">★★☆☆☆ (2 - Kurang)</option>
                        <option value="1">★☆☆☆☆ (1 - Sangat Kecewa)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#18181B] mb-1">Ulasan / Komentar <span class="text-rose-500">*</span></label>
                    <textarea class="input text-xs" name="comment" rows="3" placeholder="Ceritakan kenyamanan kamar, fasilitas, kebersihan, keramahan pemilik..." required minlength="10" maxlength="1000"></textarea>
                </div>
                <button type="submit" class="btn-primary py-2 px-5 text-xs font-semibold">
                    Kirim Ulasan Sekarang
                </button>
            </form>
        </div>
        @elseif($booking->review)
        <div class="rounded-2xl border border-[#E4E4E7] bg-white p-6 shadow-sm">
            <h3 class="font-bold text-[#18181B] text-sm mb-3">Ulasan yang Anda Berikan</h3>
            <div class="p-4 bg-[#F5F6F8] rounded-xl">
                <span class="inline-flex items-center gap-1 text-amber-500 font-bold mb-1 text-xs">
                    <i data-lucide="star" class="h-3.5 w-3.5 fill-amber-400 text-amber-400"></i>
                    {{ $booking->review->rating }} / 5
                </span>
                <p class="text-xs text-[#18181B] leading-relaxed">{{ $booking->review->comment }}</p>
                <p class="text-[11px] text-[#71717A] mt-2">Diberikan pada {{ $booking->review->created_at?->format('d M Y') }}</p>
            </div>
        </div>
        @endif

        <!-- Timeline History -->
        <div class="rounded-2xl border border-[#E4E4E7] bg-white p-6 shadow-sm">
            <h3 class="font-bold text-[#18181B] text-sm mb-4 flex items-center gap-2">
                <i data-lucide="history" class="h-4 w-4 text-[#4263EB]"></i>
                Riwayat Perjalanan Pengajuan
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
                Kontak Pemilik Kost
            </h3>
            <div>
                <p class="text-[11px] text-[#71717A]">Nama Pemilik</p>
                <p class="font-bold text-xs text-[#18181B] mt-0.5">{{ $booking->property->owner?->name ?? 'Pemilik' }}</p>
            </div>
            <div>
                <p class="text-[11px] text-[#71717A]">Kota</p>
                <p class="text-xs text-[#18181B] mt-0.5">{{ $booking->property->owner?->city ?: '-' }}</p>
            </div>
            @if($booking->isApproved() || $booking->isCompleted())
                <div>
                    <p class="text-[11px] text-[#71717A]">Email</p>
                    <p class="text-xs text-[#18181B] mt-0.5">{{ $booking->property->owner?->email }}</p>
                </div>
                <div>
                    <p class="text-[11px] text-[#71717A]">Nomor WhatsApp / HP</p>
                    <p class="text-xs font-bold text-[#18181B] mt-0.5">{{ $booking->property->owner?->phone ?: 'Belum diisi' }}</p>
                </div>
                @if($booking->property->owner?->phone)
                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $booking->property->owner->phone)) }}" target="_blank" class="btn-primary w-full py-2.5 text-center text-xs font-semibold flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700">
                        <i data-lucide="message-circle" class="h-4 w-4"></i>
                        <span>Chat WhatsApp Pemilik</span>
                    </a>
                @endif
            @else
                <p class="text-xs text-[#71717A] bg-[#F5F6F8] p-3 rounded-xl leading-relaxed">
                    Kontak lengkap nomor WhatsApp pemilik akan terbuka setelah pengajuan sewa ini disetujui.
                </p>
            @endif
        </div>
    </div>
</div>
@endsection
