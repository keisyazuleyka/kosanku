@extends('layouts.dashboard')
@section('title', 'Riwayat Booking Saya | Kosanku')
@section('heading', 'Pengajuan Sewa Saya')
@section('subheading', 'Daftar semua pengajuan booking kost yang pernah Anda buat')
@section('sidebar') @include('partials.sidebar-tenant') @endsection

@section('content')
<div class="rounded-2xl border border-[#E4E4E7] bg-white overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-[#F5F6F8] text-[11px] font-semibold text-[#71717A] uppercase tracking-wide">
                <tr>
                    <th class="p-4">Kode Booking</th>
                    <th>Nama Kost</th>
                    <th>Tanggal Masuk</th>
                    <th>Durasi</th>
                    <th>Kamar</th>
                    <th>Total Estimasi Biaya</th>
                    <th>Status</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E4E4E7]">
            @forelse($bookings as $booking)
                <tr class="hover:bg-[#F5F6F8]/60 transition">
                    <td class="p-4 font-mono font-bold text-[#4263EB]">{{ $booking->code }}</td>
                    <td>
                        <a href="{{ route('kost.show', $booking->property->slug) }}" class="font-bold text-[#18181B] hover:text-[#4263EB] transition">
                            {{ $booking->property->name }}
                        </a>
                        <span class="block text-[11px] text-[#71717A]">{{ $booking->property->city }}</span>
                    </td>
                    <td class="text-[#71717A]">{{ $booking->start_date?->format('d M Y') }}</td>
                    <td class="text-[#71717A]">{{ $booking->duration_months }} bulan</td>
                    <td class="text-[#71717A]">{{ $booking->rooms_requested }}</td>
                    <td class="font-bold text-[#18181B]">{{ rupiah($booking->total_estimate) }}</td>
                    <td>
                        <span class="badge {{ $booking->status->badgeClass() }}">{{ $booking->status->label() }}</span>
                    </td>
                    <td class="p-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            @if($booking->canBeReviewed())
                                <a href="{{ route('tenant.bookings.show', $booking) }}" class="btn-primary py-1 px-2.5 text-[11px] font-semibold flex items-center gap-1 rounded-lg">
                                    <i data-lucide="star" class="h-3 w-3"></i>
                                    <span>Beri Ulasan</span>
                                </a>
                            @endif
                            <a class="font-semibold text-[#405BE8] hover:underline" href="{{ route('tenant.bookings.show', $booking) }}">
                                Detail &rarr;
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="p-8 text-center text-xs text-[#71717A]">
                        Anda belum pernah membuat pengajuan sewa kost.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $bookings->links() }}
</div>
@endsection
