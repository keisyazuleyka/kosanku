@extends('layouts.public')
@section('title', 'Cara Kerja | ' . setting('site_name', 'Kosanku'))

@section('content')
<div class="mx-auto max-w-4xl px-4 py-12 sm:px-6">
    <div class="text-center max-w-2xl mx-auto">
        <span class="badge bg-[#EFF2FE] text-[#4263EB] font-semibold text-xs">Panduan Pengguna</span>
        <h1 class="mt-3 text-3xl font-extrabold tracking-tight text-[#18181B] sm:text-4xl">Cara Kerja Kosanku</h1>
        <p class="mt-3 text-sm text-[#71717A]">
            Platform sewa kost yang menghubungkan pencari kost dengan pemilik properti secara langsung, transparan, dan terpercaya.
        </p>
    </div>

    <!-- Steps for Tenants -->
    <div class="mt-12 rounded-2xl border border-[#E4E4E7] bg-white p-6 sm:p-8 shadow-sm">
        <h2 class="text-lg font-bold text-[#18181B] flex items-center gap-2">
            <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-[#EFF2FE] text-[#4263EB]">
                <i data-lucide="user" class="h-4 w-4"></i>
            </div>
            Untuk Pencari Kost (Penyewa)
        </h2>
        <div class="mt-6 grid gap-6 sm:grid-cols-3">
            <div class="rounded-xl bg-[#F5F6F8] p-5">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-[#4263EB] text-white text-xs font-bold">1</div>
                <h3 class="mt-3 font-bold text-sm text-[#18181B]">Cari & Filter</h3>
                <p class="mt-1.5 text-xs text-[#71717A] leading-relaxed">Telusuri ribuan kost berdasarkan kota, kisaran harga, fasilitas, dan tipe kamar idaman.</p>
            </div>
            <div class="rounded-xl bg-[#F5F6F8] p-5">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-[#4263EB] text-white text-xs font-bold">2</div>
                <h3 class="mt-3 font-bold text-sm text-[#18181B]">Ajukan Booking</h3>
                <p class="mt-1.5 text-xs text-[#71717A] leading-relaxed">Tentukan tanggal masuk dan durasi sewa, lalu kirim pengajuan ke pemilik kost secara online.</p>
            </div>
            <div class="rounded-xl bg-[#F5F6F8] p-5">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-[#4263EB] text-white text-xs font-bold">3</div>
                <h3 class="mt-3 font-bold text-sm text-[#18181B]">Konfirmasi & Huni</h3>
                <p class="mt-1.5 text-xs text-[#71717A] leading-relaxed">Tunggu konfirmasi persetujuan dari pemilik kost, lalu siap untuk check-in dan menempati kamar.</p>
            </div>
        </div>
    </div>

    <!-- Steps for Owners -->
    <div class="mt-8 rounded-2xl border border-[#E4E4E7] bg-white p-6 sm:p-8 shadow-sm">
        <h2 class="text-lg font-bold text-[#18181B] flex items-center gap-2">
            <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                <i data-lucide="building-2" class="h-4 w-4"></i>
            </div>
            Untuk Pemilik Kost
        </h2>
        <div class="mt-6 grid gap-6 sm:grid-cols-3">
            <div class="rounded-xl bg-[#F5F6F8] p-5">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-600 text-white text-xs font-bold">1</div>
                <h3 class="mt-3 font-bold text-sm text-[#18181B]">Daftarkan Properti</h3>
                <p class="mt-1.5 text-xs text-[#71717A] leading-relaxed">Upload foto, tentukan harga, detail fasilitas, serta peraturan kost Anda dengan mudah.</p>
            </div>
            <div class="rounded-xl bg-[#F5F6F8] p-5">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-600 text-white text-xs font-bold">2</div>
                <h3 class="mt-3 font-bold text-sm text-[#18181B]">Verifikasi Cepat</h3>
                <p class="mt-1.5 text-xs text-[#71717A] leading-relaxed">Tim kurasi Kosanku akan memverifikasi keaslian listing Anda dalam 1x24 jam.</p>
            </div>
            <div class="rounded-xl bg-[#F5F6F8] p-5">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-600 text-white text-xs font-bold">3</div>
                <h3 class="mt-3 font-bold text-sm text-[#18181B]">Terima Pengajuan</h3>
                <p class="mt-1.5 text-xs text-[#71717A] leading-relaxed">Pantau calon penyewa lewat dashboard dan setujui calon penyewa yang cocok.</p>
            </div>
        </div>
    </div>

    <!-- Additional Info from DB setting -->
    @if(setting('how_it_works'))
    <div class="mt-8 rounded-2xl border border-[#E4E4E7] bg-white p-6 sm:p-8 shadow-sm text-xs sm:text-sm text-[#71717A] leading-relaxed whitespace-pre-line">
        {{ setting('how_it_works') }}
    </div>
    @endif
</div>
@endsection
