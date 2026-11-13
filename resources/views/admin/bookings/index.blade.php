@extends('layouts.dashboard')
@section('title', 'Manajemen Booking | Kosanku')
@section('heading', 'Seluruh Pengajuan Booking')
@section('subheading', 'Pantau seluruh permohonan sewa dan status terkini')
@section('sidebar') @include('partials.sidebar-admin') @endsection

@section('content')
<!-- Filter Bar -->
<div class="mb-6 rounded-2xl border border-[#E4E4E7] bg-white p-4 shadow-sm">
    <form class="flex flex-col sm:flex-row flex-wrap items-center gap-3" method="GET" action="{{ route('admin.bookings.index') }}">
        <select class="input text-xs w-full sm:w-44" name="status">
            <option value="">Semua Status</option>
            @foreach($statuses as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <input class="input text-xs w-full sm:w-36" type="date" name="from" value="{{ request('from') }}" title="Dari Tanggal">
            <span class="text-xs text-[#71717A]">-</span>
            <input class="input text-xs w-full sm:w-36" type="date" name="to" value="{{ request('to') }}" title="Sampai Tanggal">
        </div>

        <button type="submit" class="btn-primary text-xs py-2 px-4 flex items-center gap-1.5 w-full sm:w-auto justify-center">
            <i data-lucide="filter" class="h-3.5 w-3.5"></i>
            <span>Filter Booking</span>
        </button>

        @if(request()->hasAny(['status', 'from', 'to']))
            <a href="{{ route('admin.bookings.index') }}" class="text-xs text-[#71717A] hover:text-[#4263EB] px-2">Reset</a>
        @endif
    </form>
</div>

<!-- Table Card -->
<div class="rounded-2xl border border-[#E4E4E7] bg-white overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-[#F5F6F8] text-[11px] font-semibold text-[#71717A] uppercase tracking-wide">
                <tr>
                    <th class="p-4">Kode Booking</th>
                    <th>Nama Kost</th>
                    <th>Penyewa</th>
                    <th>Pemilik</th>
                    <th>Tgl Masuk</th>
                    <th>Durasi</th>
                    <th>Total Estimasi</th>
                    <th>Status</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E4E4E7]">
            @forelse($bookings as $booking)
                <tr class="hover:bg-[#F5F6F8]/60 transition">
                    <td class="p-4 font-mono font-bold text-[#4263EB]">{{ $booking->code }}</td>
                    <td class="font-bold text-[#18181B]">{{ $booking->property->name }}</td>
                    <td class="text-[#18181B]">{{ $booking->tenant?->name ?? 'Anonim' }}</td>
                    <td class="text-[#71717A]">{{ $booking->owner?->name ?? '-' }}</td>
                    <td class="text-[#71717A]">{{ $booking->start_date?->format('d M Y') }}</td>
                    <td class="text-[#71717A]">{{ $booking->duration_months }} bulan</td>
                    <td class="font-bold text-[#18181B]">{{ rupiah($booking->total_estimate) }}</td>
                    <td>
                        <span class="badge {{ $booking->status->badgeClass() }}">{{ $booking->status->label() }}</span>
                    </td>
                    <td class="p-4 text-right">
                        <a class="font-semibold text-[#4263EB] hover:underline" href="{{ route('admin.bookings.show', $booking) }}">
                            Detail &rarr;
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="p-8 text-center text-xs text-[#71717A]">Tidak ada data booking yang cocok.</td>
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
