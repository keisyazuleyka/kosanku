@extends('layouts.dashboard')
@section('title', 'Manajemen Kategori | Kosanku')
@section('heading', 'Manajemen Kategori Kost')
@section('subheading', 'Kelola label kategori yang memudahkan pencarian oleh pengguna')
@section('sidebar') @include('partials.sidebar-admin') @endsection

@section('content')
<div class="grid gap-6 lg:grid-cols-3">
    <!-- Left Column: Add Form -->
    <div class="lg:col-span-1">
        <div class="rounded-2xl border border-[#E4E4E7] bg-white p-6 shadow-sm">
            <h3 class="font-bold text-sm text-[#18181B] mb-4 flex items-center gap-2">
                <i data-lucide="plus-circle" class="h-4 w-4 text-[#4263EB]"></i>
                Tambah Kategori Baru
            </h3>
            <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Nama Kategori</label>
                    <input class="input text-xs" type="text" name="name" placeholder="Contoh: Dekat Kampus, Eksklusif" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Deskripsi Singkat</label>
                    <textarea class="input text-xs" name="description" rows="3" placeholder="Penjelasan singkat kategori"></textarea>
                </div>
                <button type="submit" class="btn-primary w-full py-2.5 text-xs font-semibold">
                    Simpan Kategori
                </button>
            </form>
        </div>
    </div>

    <!-- Right Column: Category Table -->
    <div class="lg:col-span-2">
        <div class="rounded-2xl border border-[#E4E4E7] bg-white overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#F5F6F8] text-[11px] font-semibold text-[#71717A] uppercase tracking-wide">
                        <tr>
                            <th class="p-4">Nama Kategori</th>
                            <th>Slug</th>
                            <th>Deskripsi</th>
                            <th>Total Kost</th>
                            <th class="p-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E4E4E7]">
                    @forelse($categories as $category)
                        <tr class="hover:bg-[#F5F6F8]/60 transition">
                            <td class="p-4 font-bold text-[#18181B]">{{ $category->name }}</td>
                            <td class="font-mono text-[11px] text-[#71717A]">{{ $category->slug }}</td>
                            <td class="text-[#71717A] max-w-xs">{{ $category->description ?: '-' }}</td>
                            <td>
                                <span class="badge bg-[#EFF2FE] text-[#4263EB] font-semibold">{{ $category->properties_count }} listing</span>
                            </td>
                            <td class="p-4 text-right">
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline" data-confirm="Hapus kategori ini?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-rose-600 hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-xs text-[#71717A]">Belum ada kategori tersimpan.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
