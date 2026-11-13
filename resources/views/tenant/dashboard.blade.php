@extends('layouts.dashboard')
@section('title', 'Dashboard Penyewa | Kosanku')
@section('heading', 'Dashboard Penyewa')
@section('subheading', 'Pantau status pengajuan sewa dan kost favorit Anda')
@section('sidebar') @include('partials.sidebar-tenant') @endsection

@section('content')
<!-- Stats Cards Grid -->
<div class="grid gap-4 sm:grid-cols-3">
    <div class="rounded-2xl border border-[#E4E4E7] bg-white p-5 shadow-sm">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-[#71717A] uppercase tracking-wide">Total Pengajuan Booking</p>
                <p class="mt-2 text-2xl font-black text-[#18181B]">{{ $bookings }}</p>
            </div>
            <div class="rounded-xl bg-[#EFF2FE] p-2.5 text-[#4263EB]">
                <i data-lucide="calendar-check" class="h-5 w-5"></i>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-[#E4E4E7] bg-white p-5 shadow-sm">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-[#71717A] uppercase tracking-wide">Kost Favorit Disimpan</p>
                <p class="mt-2 text-2xl font-black text-rose-500">{{ $favorites }}</p>
            </div>
            <div class="rounded-xl bg-rose-50 p-2.5 text-rose-500">
                <i data-lucide="heart" class="h-5 w-5"></i>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-[#E4E4E7] bg-white p-5 shadow-sm flex flex-col justify-between">
        <div>
            <p class="text-xs font-semibold text-[#71717A] uppercase tracking-wide">Temukan Hunian Baru</p>
            <p class="mt-1 text-xs text-[#71717A]">Jelajahi pilihan kost strategis di sekitar kampus / kantor.</p>
        </div>
        <a href="{{ route('kost.index') }}" class="btn-primary mt-3 text-center text-xs py-2 px-4 flex items-center justify-center gap-1.5">
            <i data-lucide="search" class="h-3.5 w-3.5"></i>
            <span>Cari Kost Sekarang</span>
        </a>
    </div>
</div>

<!-- Recent Bookings Table -->
<div class="mt-8 flex items-center justify-between">
    <div>
        <h2 class="text-base font-bold text-[#18181B]">Pengajuan Booking Terakhir</h2>
        <p class="text-xs text-[#71717A]">Status terkini pengajuan kamar kost Anda</p>
    </div>
    <a href="{{ route('tenant.bookings.index') }}" class="text-xs font-semibold text-[#4263EB] hover:underline flex items-center gap-1">
        <span>Lihat Semua</span>
        <i data-lucide="chevron-right" class="h-3.5 w-3.5"></i>
    </a>
</div>

<div class="mt-4 rounded-2xl border border-[#E4E4E7] bg-white overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-[#F5F6F8] text-[11px] font-semibold text-[#71717A] uppercase tracking-wide">
                <tr>
                    <th class="p-4">Kode Booking</th>
                    <th>Nama Kost</th>
                    <th>Tanggal Masuk</th>
                    <th>Durasi</th>
                    <th>Total Estimasi</th>
                    <th>Status</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E4E4E7]">
            @forelse($recent as $b)
                <tr class="hover:bg-[#F5F6F8]/60 transition">
                    <td class="p-4 font-mono font-bold text-[#4263EB]">{{ $b->code }}</td>
                    <td class="font-semibold text-[#18181B]">{{ $b->property->name }}</td>
                    <td class="text-[#71717A]">{{ $b->start_date?->format('d M Y') }}</td>
                    <td class="text-[#71717A]">{{ $b->duration_months }} bulan</td>
                    <td class="font-bold text-[#18181B]">{{ rupiah($b->total_estimate) }}</td>
                    <td>
                        <span class="badge {{ $b->status->badgeClass() }}">{{ $b->status->label() }}</span>
                    </td>
                    <td class="p-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            @if($b->canBeReviewed())
                                <a href="{{ route('tenant.bookings.show', $b) }}" class="btn-primary py-1 px-2.5 text-[11px] font-semibold flex items-center gap-1 rounded-lg">
                                    <i data-lucide="star" class="h-3 w-3"></i>
                                    <span>Ulas</span>
                                </a>
                            @endif
                            <a class="font-semibold text-[#405BE8] hover:underline" href="{{ route('tenant.bookings.show', $b) }}">
                                Detail &rarr;
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="p-8 text-center text-xs text-[#71717A]">
                        Belum ada pengajuan sewa. Temukan kost idaman Anda di halaman <a href="{{ route('kost.index') }}" class="font-semibold text-[#4263EB] hover:underline">pencarian kost</a>.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
