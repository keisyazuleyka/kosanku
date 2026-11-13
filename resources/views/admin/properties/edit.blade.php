@extends('layouts.dashboard')
@section('title', 'Edit Listing Kost | Kosanku')
@section('heading', 'Edit Listing: ' . $property->name)
@section('subheading', 'Perbarui data listing administratif')
@section('sidebar') @include('partials.sidebar-admin') @endsection

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.kost.show', $property) }}" class="btn-ghost text-xs inline-flex items-center gap-1.5 py-2 px-3">
        <i data-lucide="arrow-left" class="h-3.5 w-3.5"></i>
        <span>Kembali ke Detail Kost</span>
    </a>
</div>

<div class="max-w-4xl">
    <div class="rounded-2xl border border-[#E4E4E7] bg-white p-6 sm:p-8 shadow-sm">
        <form method="POST" action="{{ route('admin.kost.update', $property) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Nama Kost</label>
                    <input class="input text-xs" name="name" value="{{ $property->name }}" required>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Deskripsi Lengkap</label>
                    <textarea class="input text-xs" name="description" rows="4">{{ $property->description }}</textarea>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Kota</label>
                        <input class="input text-xs" name="city" value="{{ $property->city }}">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Kecamatan</label>
                        <input class="input text-xs" name="district" value="{{ $property->district }}">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Alamat Lengkap</label>
                    <input class="input text-xs" name="address" value="{{ $property->address }}">
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Harga Sewa Bulanan (Rp)</label>
                        <input class="input text-xs" type="number" name="monthly_price" value="{{ $property->monthly_price }}">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Deposit (Rp)</label>
                        <input class="input text-xs" type="number" name="deposit" value="{{ $property->deposit }}">
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Total Kamar</label>
                        <input class="input text-xs" type="number" name="rooms_total" value="{{ $property->rooms_total }}">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Kamar Tersedia</label>
                        <input class="input text-xs" type="number" name="rooms_available" value="{{ $property->rooms_available }}">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Biaya Tambahan</label>
                    <textarea class="input text-xs" name="additional_fees" rows="2">{{ $property->additional_fees }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Tata Tertib & Aturan</label>
                    <textarea class="input text-xs" name="rules" rows="2">{{ $property->rules }}</textarea>
                </div>

                <div>
                    <label class="flex items-center gap-2 text-xs font-semibold text-[#18181B] cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" @checked($property->is_featured) class="rounded border-[#E4E4E7] text-[#4263EB] focus:ring-[#4263EB]/20">
                        <span>Jadikan Listing Unggulan (Featured) di Beranda</span>
                    </label>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#18181B] mb-2">Fasilitas Properti</label>
                    <div class="grid gap-2 sm:grid-cols-3">
                        @foreach($facilities as $facility)
                            <label class="flex items-center gap-2 text-xs text-[#18181B] cursor-pointer p-2 rounded-lg border border-[#E4E4E7] hover:bg-[#F5F6F8]/60">
                                <input type="checkbox" name="facilities[]" value="{{ $facility->id }}" @checked($property->facilities->contains($facility->id)) class="rounded border-[#E4E4E7] text-[#4263EB] focus:ring-[#4263EB]/20">
                                <span>{{ $facility->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="pt-5 border-t border-[#E4E4E7] flex justify-end gap-2.5">
                <a href="{{ route('admin.kost.show', $property) }}" class="btn-ghost text-xs py-2 px-4">Batal</a>
                <button type="submit" class="btn-primary text-xs py-2 px-6 font-semibold">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
