@extends('layouts.dashboard')
@section('title', $property->exists ? 'Edit Kost: ' . $property->name : 'Tambah Kost Baru')
@section('heading', $property->exists ? 'Edit Kost: ' . $property->name : 'Tambah Kost Baru')
@section('subheading', $property->exists ? 'Perbarui informasi kost dan foto' : 'Lengkapi data kost untuk diajukan ke admin')
@section('sidebar') @include('partials.sidebar-owner') @endsection

@section('content')
<div class="mb-6">
    <a href="{{ route('owner.kost.index') }}" class="btn-ghost text-xs inline-flex items-center gap-1.5 py-2 px-3">
        <i data-lucide="arrow-left" class="h-3.5 w-3.5"></i>
        <span>Kembali ke Daftar Kost</span>
    </a>
</div>

<div class="max-w-4xl">
    <div class="rounded-2xl border border-[#E4E4E7] bg-white p-6 sm:p-8 shadow-sm">
        <form method="POST" action="{{ $property->exists ? route('owner.kost.update', $property) : route('owner.kost.store') }}" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @if($property->exists)
                @method('PUT')
            @endif

            <!-- Section 1 -->
            <div>
                <h2 class="text-sm font-bold text-[#18181B] border-b border-[#E4E4E7] pb-3 mb-4 flex items-center gap-2">
                    <i data-lucide="info" class="h-4 w-4 text-[#4263EB]"></i>
                    Informasi Dasar Kost
                </h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Nama Kost <span class="text-rose-500">*</span></label>
                        <input class="input text-xs" type="text" name="name" value="{{ old('name', $property->name) }}" placeholder="Contoh: Kost Melati Eksklusif Dago" required>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Tipe Kost <span class="text-rose-500">*</span></label>
                            <select class="input text-xs" name="type" required>
                                @foreach($types as $type)
                                    <option value="{{ $type->value }}" @selected(old('type', $property->type?->value) === $type->value)>{{ $type->label() }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Kategori</label>
                            <select class="input text-xs" name="category_id">
                                <option value="">Pilih Kategori (Opsional)</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" @selected(old('category_id', $property->category_id) == $cat->id)>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Deskripsi Lengkap <span class="text-rose-500">*</span></label>
                        <textarea class="input text-xs" name="description" rows="4" placeholder="Jelaskan lingkungan, kelebihan, dan kondisi kamar..." required>{{ old('description', $property->description) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Section 2 -->
            <div>
                <h2 class="text-sm font-bold text-[#18181B] border-b border-[#E4E4E7] pb-3 mb-4 flex items-center gap-2">
                    <i data-lucide="map-pin" class="h-4 w-4 text-[#4263EB]"></i>
                    Lokasi Kost
                </h2>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Kota / Kabupaten <span class="text-rose-500">*</span></label>
                        <input class="input text-xs" type="text" name="city" value="{{ old('city', $property->city) }}" placeholder="Contoh: Depok, Jakarta Selatan, Bandung" required>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Kecamatan</label>
                        <input class="input text-xs" type="text" name="district" value="{{ old('district', $property->district) }}" placeholder="Contoh: Beji, Coblong, Setiabudi">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Kelurahan / Desa</label>
                        <input class="input text-xs" type="text" name="village" value="{{ old('village', $property->village) }}" placeholder="Contoh: Pondok Cina, Dago, Karet Semanggi">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Alamat Lengkap <span class="text-rose-500">*</span></label>
                        <textarea class="input text-xs" name="address" rows="2" placeholder="Jalan, nomor rumah, RT/RW, patokan..." required>{{ old('address', $property->address) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Section 3 -->
            <div>
                <h2 class="text-sm font-bold text-[#18181B] border-b border-[#E4E4E7] pb-3 mb-4 flex items-center gap-2">
                    <i data-lucide="door-open" class="h-4 w-4 text-[#4263EB]"></i>
                    Kapasitas & Ketersediaan Kamar
                </h2>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Total Jumlah Kamar <span class="text-rose-500">*</span></label>
                        <input class="input text-xs" type="number" name="rooms_total" value="{{ old('rooms_total', $property->rooms_total ?? 1) }}" min="1" max="200" required>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Kamar Tersedia Saat Ini <span class="text-rose-500">*</span></label>
                        <input class="input text-xs" type="number" name="rooms_available" value="{{ old('rooms_available', $property->rooms_available ?? 1) }}" min="0" required>
                    </div>
                </div>
            </div>

            <!-- Section 4 -->
            <div>
                <h2 class="text-sm font-bold text-[#18181B] border-b border-[#E4E4E7] pb-3 mb-4 flex items-center gap-2">
                    <i data-lucide="dollar-sign" class="h-4 w-4 text-[#4263EB]"></i>
                    Harga, Deposit & Ketentuan Biaya
                </h2>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Harga Sewa Bulanan (Rp) <span class="text-rose-500">*</span></label>
                        <input class="input text-xs" type="number" name="monthly_price" value="{{ old('monthly_price', $property->monthly_price) }}" min="100000" step="50000" required>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Uang Deposit (Rp) <span class="text-rose-500">*</span></label>
                        <input class="input text-xs" type="number" name="deposit" value="{{ old('deposit', $property->deposit ?? 0) }}" min="0" step="50000" required>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Biaya Tambahan Lainnya (Opsional)</label>
                        <input class="input text-xs" type="text" name="additional_fees" value="{{ old('additional_fees', $property->additional_fees) }}" placeholder="Contoh: Listrik token per kamar, parkir mobil Rp 100.000/bulan">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Aturan Kost</label>
                        <textarea class="input text-xs" name="rules" rows="3" placeholder="Contoh: Tamu lawan jenis dilarang menginap, gerbang tutup jam 23.00, tidak boleh merokok">{{ old('rules', $property->rules) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Section 5 -->
            <div>
                <h2 class="text-sm font-bold text-[#18181B] border-b border-[#E4E4E7] pb-3 mb-4 flex items-center gap-2">
                    <i data-lucide="check-circle-2" class="h-4 w-4 text-[#4263EB]"></i>
                    Fasilitas Kost
                </h2>
                <div class="grid gap-3 sm:grid-cols-2 md:grid-cols-3">
                    @php($selectedFacilities = old('facilities', $property->facilities->pluck('id')->all() ?? []))
                    @foreach($facilities as $facility)
                        <label class="flex items-center gap-2.5 p-3 rounded-xl border border-[#E4E4E7] hover:bg-[#F5F6F8]/60 cursor-pointer transition">
                            <input type="checkbox" name="facilities[]" value="{{ $facility->id }}" class="rounded border-[#E4E4E7] text-[#4263EB] focus:ring-[#4263EB]/20" @checked(in_array($facility->id, $selectedFacilities))>
                            <span class="text-xs font-medium text-[#18181B]">{{ $facility->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Section 6 -->
            <div>
                <h2 class="text-sm font-bold text-[#18181B] border-b border-[#E4E4E7] pb-3 mb-4 flex items-center gap-2">
                    <i data-lucide="image" class="h-4 w-4 text-[#4263EB]"></i>
                    Foto Kost
                </h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Unggah Foto Baru (Maks 10 foto, maks 4MB per foto: JPG, PNG, WebP)</label>
                        <input class="input text-xs" type="file" name="images[]" multiple accept="image/*" data-image-input>
                        <div class="mt-3 flex flex-wrap gap-2" data-image-preview></div>
                    </div>

                    @if($property->exists && $property->images->isNotEmpty())
                        <div class="mt-4 pt-4 border-t border-[#E4E4E7]">
                            <h3 class="text-xs font-bold text-[#18181B] uppercase mb-3">Foto yang Sudah Tersimpan ({{ $property->images->count() }})</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                @foreach($property->images as $img)
                                    <div class="relative group rounded-xl overflow-hidden border border-[#E4E4E7] bg-zinc-100">
                                        <img src="{{ $img->url() }}" class="h-28 w-full object-cover" onerror="this.src='{{ asset('images/kost-fallback.svg') }}'">
                                        @if($img->is_cover)
                                            <span class="absolute top-2 left-2 badge bg-[#4263EB] text-white text-[10px]">Sampul</span>
                                        @endif
                                        <div class="p-2 bg-white flex justify-between items-center text-xs">
                                            <span class="text-[#71717A] text-[10px]">#{{ $img->sort_order }}</span>
                                            <button type="submit" form="delete-image-{{ $img->id }}" class="text-rose-600 hover:underline text-[11px] font-semibold">Hapus</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="pt-6 border-t border-[#E4E4E7] flex flex-wrap items-center justify-between gap-4">
                <a href="{{ route('owner.kost.index') }}" class="btn-ghost text-xs py-2 px-4">Batal</a>
                <div class="flex gap-2.5">
                    <button type="submit" name="submit_action" value="draft" class="btn-ghost text-xs py-2 px-4">
                        Simpan sebagai Draft
                    </button>
                    <button type="submit" name="submit_action" value="pending" class="btn-primary text-xs py-2 px-4">
                        Simpan & Ajukan Verifikasi
                    </button>
                </div>
            </div>
        </form>

        @if($property->exists && $property->images->isNotEmpty())
            @foreach($property->images as $img)
                <form id="delete-image-{{ $img->id }}" method="POST" action="{{ route('owner.kost.images.destroy', [$property, $img]) }}" class="hidden" data-confirm="Hapus foto ini?">
                    @csrf
                    @method('DELETE')
                </form>
            @endforeach
        @endif
    </div>
</div>
@endsection
