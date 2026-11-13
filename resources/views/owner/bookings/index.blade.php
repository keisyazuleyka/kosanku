@extends('layouts.dashboard')
@section('title', 'Daftar Booking | Kosanku')
@section('heading', 'Daftar Pengajuan Sewa')
@section('subheading', 'Kelola permohonan booking dari calon penyewa')
@section('sidebar') @include('partials.sidebar-owner') @endsection

@section('content')
<div class="rounded-2xl border border-[#E4E4E7] bg-white overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-[#F5F6F8] text-[11px] font-semibold text-[#71717A] uppercase tracking-wide">
                <tr>
                    <th class="p-4">Kode Booking</th>
                    <th>Nama Kost</th>
                    <th>Calon Penyewa</th>
                    <th>Tgl Masuk</th>
                    <th>Durasi</th>
                    <th>Kamar</th>
                    <th>Total Estimasi</th>
                    <th>Status</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E4E4E7]">
            @forelse($bookings as $booking)
                <tr class="hover:bg-[#F5F6F8]/60 transition">
                    <td class="p-4 font-mono font-bold text-[#4263EB]">{{ $booking->code }}</td>
                    <td class="font-semibold text-[#18181B]">{{ $booking->property->name }}</td>
                    <td class="text-[#18181B]">{{ $booking->tenant?->name ?? 'Anonim' }}</td>
                    <td class="text-[#71717A]">{{ $booking->start_date?->format('d M Y') }}</td>
                    <td class="text-[#71717A]">{{ $booking->duration_months }} bulan</td>
                    <td class="text-[#71717A]">{{ $booking->rooms_requested }}</td>
                    <td class="font-bold text-[#18181B]">{{ rupiah($booking->total_estimate) }}</td>
                    <td>
                        <span class="badge {{ $booking->status->badgeClass() }}">{{ $booking->status->label() }}</span>
                    </td>
                    <td class="p-4 text-right">
                        <a class="font-semibold text-[#4263EB] hover:underline" href="{{ route('owner.bookings.show', $booking) }}">
                            Detail & Proses &rarr;
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="p-8 text-center text-xs text-[#71717A]">
                        Belum ada pengajuan booking untuk kost Anda.
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
