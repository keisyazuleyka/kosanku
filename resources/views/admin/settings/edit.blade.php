@extends('layouts.dashboard')
@section('title', 'Pengaturan Platform | Kosanku')
@section('heading', 'Pengaturan Platform')
@section('subheading', 'Konfigurasi nama, kontak, aturan harga, dan konten statis')
@section('sidebar') @include('partials.sidebar-admin') @endsection

@section('content')
<div class="max-w-4xl">
    <div class="rounded-2xl border border-[#E4E4E7] bg-white p-6 sm:p-8 shadow-sm">
        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Section 1 -->
            <div>
                <h3 class="text-sm font-bold text-[#18181B] border-b border-[#E4E4E7] pb-3 mb-4 flex items-center gap-2">
                    <i data-lucide="sliders" class="h-4 w-4 text-[#4263EB]"></i>
                    Informasi Umum & Kontak Resmi
                </h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Nama Platform</label>
                        <input class="input text-xs" type="text" name="site_name" value="{{ old('site_name', $settings['site_name'] ?? 'Kosanku') }}" required>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Tagline Platform</label>
                        <input class="input text-xs" type="text" name="tagline" value="{{ old('tagline', $settings['tagline'] ?? 'Find your space. Feel at home.') }}" required>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Email Kontak Resmi</label>
                        <input class="input text-xs" type="email" name="contact_email" value="{{ old('contact_email', $settings['contact_email'] ?? 'halo@kosanku.test') }}" required>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Nomor Telepon / WhatsApp</label>
                        <input class="input text-xs" type="text" name="contact_phone" value="{{ old('contact_phone', $settings['contact_phone'] ?? '081234567890') }}">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Batas Minimum Harga Listing (Rupiah)</label>
                        <input class="input text-xs" type="number" name="min_listing_price" value="{{ old('min_listing_price', $settings['min_listing_price'] ?? 500000) }}" min="0" required>
                    </div>
                </div>
            </div>

            <!-- Section 2 -->
            <div>
                <h3 class="text-sm font-bold text-[#18181B] border-b border-[#E4E4E7] pb-3 mb-4 flex items-center gap-2">
                    <i data-lucide="file-text" class="h-4 w-4 text-[#4263EB]"></i>
                    Konten Halaman Publik
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Konten "Cara Kerja"</label>
                        <textarea class="input text-xs" name="how_it_works" rows="4">{{ old('how_it_works', $settings['how_it_works'] ?? '') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Konten "Syarat dan Ketentuan"</label>
                        <textarea class="input text-xs" name="terms" rows="4">{{ old('terms', $settings['terms'] ?? '') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Konten "Kebijakan Privasi"</label>
                        <textarea class="input text-xs" name="privacy" rows="4">{{ old('privacy', $settings['privacy'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-[#E4E4E7] flex justify-end">
                <button type="submit" class="btn-primary py-2.5 px-6 text-xs font-semibold">
                    Simpan Pengaturan Platform
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
