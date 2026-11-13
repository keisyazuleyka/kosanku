@extends('layouts.dashboard')
@section('title', 'Manajemen Fasilitas | Kosanku')
@section('heading', 'Manajemen Fasilitas Kost')
@section('subheading', 'Daftar fasilitas yang dapat dipilih pemilik saat mendaftarkan kost')
@section('sidebar') @include('partials.sidebar-admin') @endsection

@section('content')
<div class="grid gap-6 lg:grid-cols-3">
    <!-- Left Column: Add Form -->
    <div class="lg:col-span-1">
        <div class="rounded-2xl border border-[#E4E4E7] bg-white p-6 shadow-sm">
            <h3 class="font-bold text-sm text-[#18181B] mb-4 flex items-center gap-2">
                <i data-lucide="plus-circle" class="h-4 w-4 text-[#4263EB]"></i>
                Tambah Fasilitas Baru
            </h3>
            <form method="POST" action="{{ route('admin.facilities.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Nama Fasilitas</label>
                    <input class="input text-xs" type="text" name="name" placeholder="Contoh: AC, Wi-Fi Cepat, Kamar Mandi Dalam" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Nama Ikon / Kode (Opsional)</label>
                    <input class="input text-xs" type="text" name="icon" placeholder="Contoh: wifi, air-vent, bed">
                </div>
                <button type="submit" class="btn-primary w-full py-2.5 text-xs font-semibold">
                    Simpan Fasilitas
                </button>
            </form>
        </div>
    </div>

    <!-- Right Column: Facilities Table -->
    <div class="lg:col-span-2">
        <div class="rounded-2xl border border-[#E4E4E7] bg-white overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#F5F6F8] text-[11px] font-semibold text-[#71717A] uppercase tracking-wide">
                        <tr>
                            <th class="p-4">Nama Fasilitas</th>
                            <th>Slug</th>
                            <th>Kode Ikon</th>
                            <th class="p-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E4E4E7]">
                    @forelse($facilities as $facility)
                        <tr class="hover:bg-[#F5F6F8]/60 transition">
                            <td class="p-4 font-bold text-[#18181B]">{{ $facility->name }}</td>
                            <td class="font-mono text-[11px] text-[#71717A]">{{ $facility->slug }}</td>
                            <td class="text-[#71717A]">{{ $facility->icon ?: '-' }}</td>
                            <td class="p-4 text-right">
                                <form method="POST" action="{{ route('admin.facilities.destroy', $facility) }}" class="inline" data-confirm="Hapus fasilitas ini?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-rose-600 hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-xs text-[#71717A]">Belum ada fasilitas terdaftar.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
