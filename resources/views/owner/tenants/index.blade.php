@extends('layouts.dashboard')
@section('title', 'Penyewa Aktif & Riwayat | Kosanku')
@section('heading', 'Daftar Penyewa')
@section('subheading', 'Daftar penyewa yang booking-nya telah disetujui atau selesai')
@section('sidebar') @include('partials.sidebar-owner') @endsection

@section('content')
<div class="rounded-2xl border border-[#E4E4E7] bg-white overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-[#F5F6F8] text-[11px] font-semibold text-[#71717A] uppercase tracking-wide">
                <tr>
                    <th class="p-4">Nama Penyewa</th>
                    <th>Email</th>
                    <th>Nomor WhatsApp / HP</th>
                    <th>Kost yang Disewa</th>
                    <th>Tanggal Masuk</th>
                    <th>Durasi</th>
                    <th>Status Sewa</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E4E4E7]">
            @forelse($tenants as $booking)
                <tr class="hover:bg-[#F5F6F8]/60 transition">
                    <td class="p-4 font-bold text-[#18181B]">{{ $booking->tenant?->name ?? 'Anonim' }}</td>
                    <td class="text-[#71717A]">{{ $booking->tenant?->email ?? '-' }}</td>
                    <td class="text-[#18181B] font-medium">{{ $booking->tenant?->phone ?: '-' }}</td>
                    <td class="font-semibold text-[#18181B]">{{ $booking->property->name }}</td>
                    <td class="text-[#71717A]">{{ $booking->start_date?->format('d M Y') }}</td>
                    <td class="text-[#71717A]">{{ $booking->duration_months }} bulan</td>
                    <td>
                        <span class="badge {{ $booking->status->badgeClass() }}">{{ $booking->status->label() }}</span>
                    </td>
                    <td class="p-4 text-right">
                        <a class="font-semibold text-[#4263EB] hover:underline" href="{{ route('owner.bookings.show', $booking) }}">
                            Detail &rarr;
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="p-8 text-center text-xs text-[#71717A]">
                        Belum ada data penyewa yang disetujui.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $tenants->links() }}
</div>
@endsection
