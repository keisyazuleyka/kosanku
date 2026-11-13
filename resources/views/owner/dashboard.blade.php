@extends('layouts.dashboard')
@section('title', 'Dashboard Pemilik Kost | Kosanku')
@section('heading', 'Dashboard Pemilik Kost')
@section('subheading', 'Ringkasan performa kost dan permohonan booking terbaru')
@section('sidebar') @include('partials.sidebar-owner') @endsection

@section('content')
<!-- Stats Cards Grid -->
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
    <div class="rounded-2xl border border-[#E4E4E7] bg-white p-5 shadow-sm">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-[#71717A] uppercase tracking-wide">Total Listing</p>
                <p class="mt-2 text-2xl font-black text-[#18181B]">{{ $stats['listings'] }}</p>
            </div>
            <div class="rounded-xl bg-[#EFF2FE] p-2.5 text-[#4263EB]">
                <i data-lucide="building-2" class="h-5 w-5"></i>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-[#E4E4E7] bg-white p-5 shadow-sm">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-[#71717A] uppercase tracking-wide">Aktif / Disetujui</p>
                <p class="mt-2 text-2xl font-black text-emerald-600">{{ $stats['approved'] }}</p>
            </div>
            <div class="rounded-xl bg-emerald-50 p-2.5 text-emerald-600">
                <i data-lucide="check-circle-2" class="h-5 w-5"></i>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-[#E4E4E7] bg-white p-5 shadow-sm">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-[#71717A] uppercase tracking-wide">Menunggu Verifikasi</p>
                <p class="mt-2 text-2xl font-black text-amber-600">{{ $stats['pending'] }}</p>
            </div>
            <div class="rounded-xl bg-amber-50 p-2.5 text-amber-600">
                <i data-lucide="clock" class="h-5 w-5"></i>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-[#E4E4E7] bg-white p-5 shadow-sm">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-[#71717A] uppercase tracking-wide">Total Pengajuan</p>
                <p class="mt-2 text-2xl font-black text-[#18181B]">{{ $stats['bookings'] }}</p>
            </div>
            <div class="rounded-xl bg-blue-50 p-2.5 text-blue-600">
                <i data-lucide="calendar-check" class="h-5 w-5"></i>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-[#E4E4E7] bg-white p-5 shadow-sm">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-[#71717A] uppercase tracking-wide">Perlu Respon</p>
                <p class="mt-2 text-2xl font-black text-rose-600">{{ $stats['booking_pending'] }}</p>
            </div>
            <div class="rounded-xl bg-rose-50 p-2.5 text-rose-600">
                <i data-lucide="alert-circle" class="h-5 w-5"></i>
            </div>
        </div>
    </div>
</div>

<!-- Section Header -->
<div class="mt-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h2 class="text-base font-bold text-[#18181B]">Pengajuan Booking Terbaru</h2>
        <p class="text-xs text-[#71717A]">Permohonan sewa kamar yang masuk dari calon penyewa</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('owner.kost.create') }}" class="btn-primary text-xs py-2 px-3.5 flex items-center gap-1.5">
            <i data-lucide="plus" class="h-3.5 w-3.5"></i>
            <span>Tambah Listing Kost</span>
        </a>
    </div>
</div>

<!-- Table Card -->
<div class="mt-4 rounded-2xl border border-[#E4E4E7] bg-white overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-[#F5F6F8] text-[11px] font-semibold text-[#71717A] uppercase tracking-wide">
                <tr>
                    <th class="p-4">Kode Booking</th>
                    <th>Nama Kost</th>
                    <th>Calon Penyewa</th>
                    <th>Tgl Masuk</th>
                    <th>Durasi</th>
                    <th>Total Estimasi</th>
                    <th>Status</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E4E4E7]">
            @forelse($recentBookings as $booking)
                <tr class="hover:bg-[#F5F6F8]/60 transition">
                    <td class="p-4 font-mono font-bold text-[#4263EB]">{{ $booking->code }}</td>
                    <td class="font-semibold text-[#18181B]">{{ $booking->property->name }}</td>
                    <td class="text-[#18181B]">{{ $booking->tenant?->name ?? 'Anonim' }}</td>
                    <td class="text-[#71717A]">{{ $booking->start_date?->format('d M Y') }}</td>
                    <td class="text-[#71717A]">{{ $booking->duration_months }} bulan</td>
                    <td class="font-bold text-[#18181B]">{{ rupiah($booking->total_estimate) }}</td>
                    <td>
                        <span class="badge {{ $booking->status->badgeClass() }}">{{ $booking->status->label() }}</span>
                    </td>
                    <td class="p-4 text-right">
                        <a class="font-semibold text-[#4263EB] hover:underline" href="{{ route('owner.bookings.show', $booking) }}">
                            Proses &rarr;
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="p-8 text-center text-xs text-[#71717A]">
                        Belum ada pengajuan booking yang masuk saat ini.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
