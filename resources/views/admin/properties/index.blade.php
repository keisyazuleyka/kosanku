@extends('layouts.dashboard')
@section('title', 'Manajemen Kost | Kosanku')
@section('heading', 'Manajemen Kost')
@section('subheading', 'Kelola semua listing kost yang ada di platform')
@section('sidebar') @include('partials.sidebar-admin') @endsection

@section('content')
<!-- Filter Bar -->
<div class="mb-6 rounded-2xl border border-[#E4E4E7] bg-white p-4 shadow-sm">
    <form class="flex flex-col sm:flex-row flex-wrap items-center gap-3" method="GET" action="{{ route('admin.kost.index') }}">
        <div class="relative flex-1 min-w-[200px] w-full sm:w-auto">
            <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-[#A1A1AA]"></i>
            <input class="input pl-9 text-xs" name="q" value="{{ request('q') }}" placeholder="Cari nama kost, lokasi, atau pemilik...">
        </div>

        <select class="input text-xs w-full sm:w-40" name="status">
            <option value="">Semua Status</option>
            @foreach($statuses as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>

        <input class="input text-xs w-full sm:w-36" name="city" value="{{ request('city') }}" placeholder="Semua Kota">

        <button type="submit" class="btn-primary text-xs py-2 px-4 flex items-center gap-1.5 w-full sm:w-auto justify-center">
            <i data-lucide="filter" class="h-3.5 w-3.5"></i>
            <span>Filter</span>
        </button>

        @if(request()->hasAny(['q', 'status', 'city']))
            <a href="{{ route('admin.kost.index') }}" class="text-xs text-[#71717A] hover:text-[#4263EB] px-2">Reset</a>
        @endif
    </form>
</div>

<!-- Table Card -->
<div class="rounded-2xl border border-[#E4E4E7] bg-white overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-[#F5F6F8] text-[11px] font-semibold text-[#71717A] uppercase tracking-wide">
                <tr>
                    <th class="p-4 w-12 text-center">No</th>
                    <th class="w-16">Foto</th>
                    <th>Nama Kost</th>
                    <th>Lokasi</th>
                    <th>Pemilik</th>
                    <th>Harga / Bln</th>
                    <th>Status</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E4E4E7]">
            @forelse($properties as $index => $property)
                <tr class="hover:bg-[#F5F6F8]/60 transition">
                    <td class="p-4 text-center text-[#71717A]">{{ $properties->firstItem() + $index }}</td>
                    <td>
                        <div class="h-10 w-12 rounded-lg overflow-hidden bg-zinc-100 border border-[#E4E4E7]">
                            <img src="{{ $property->coverUrl() }}" alt="{{ $property->name }}" class="h-full w-full object-cover" onerror="this.src='{{ asset('images/kost-fallback.svg') }}'">
                        </div>
                    </td>
                    <td>
                        <a href="{{ route('admin.kost.show', $property) }}" class="font-bold text-[#18181B] hover:text-[#4263EB] transition">
                            {{ $property->name }}
                        </a>
                        <span class="block text-[11px] text-[#71717A]">{{ $property->type->label() }}</span>
                    </td>
                    <td class="text-[#18181B]">{{ $property->city }}</td>
                    <td class="text-[#71717A]">{{ $property->owner->name }}</td>
                    <td class="font-bold text-[#18181B]">{{ rupiah($property->monthly_price) }}</td>
                    <td>
                        <span class="badge {{ $property->status->badgeClass() }}">{{ $property->status->label() }}</span>
                    </td>
                    <td class="p-4 text-right space-x-1.5 whitespace-nowrap">
                        <a class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-[#E4E4E7] text-[#71717A] hover:border-[#4263EB] hover:text-[#4263EB] transition" href="{{ route('admin.kost.show', $property) }}" title="Lihat Detail">
                            <i data-lucide="eye" class="h-3.5 w-3.5"></i>
                        </a>
                        <a class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-[#E4E4E7] text-[#71717A] hover:border-[#4263EB] hover:text-[#4263EB] transition" href="{{ route('admin.kost.edit', $property) }}" title="Edit">
                            <i data-lucide="edit-3" class="h-3.5 w-3.5"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="p-8 text-center text-xs text-[#71717A]">
                        Tidak ada kost yang cocok dengan filter yang dipilih.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-[#71717A]">
    <div>
        Menampilkan {{ $properties->firstItem() ?? 0 }}-{{ $properties->lastItem() ?? 0 }} dari {{ $properties->total() }} data
    </div>
    <div>
        {{ $properties->links() }}
    </div>
</div>
@endsection
