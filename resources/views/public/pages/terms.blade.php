@extends('layouts.public')
@section('title', 'Syarat dan Ketentuan | ' . setting('site_name', 'Kosanku'))

@section('content')
<div class="mx-auto max-w-4xl px-4 py-12 sm:px-6">
    <div class="rounded-2xl border border-[#E4E4E7] bg-white p-6 sm:p-10 shadow-sm">
        <span class="badge bg-[#EFF2FE] text-[#4263EB] font-semibold text-xs">Ketentuan Layanan</span>
        <h1 class="mt-3 text-2xl font-bold tracking-tight text-[#18181B] sm:text-3xl">Syarat dan Ketentuan</h1>
        <p class="mt-2 text-xs text-[#71717A]">Terakhir diperbarui: 2026</p>

        <div class="mt-8 border-t border-[#E4E4E7] pt-6 space-y-6 text-xs sm:text-sm text-[#71717A] leading-relaxed whitespace-pre-line">
            {{ setting('terms', 'Harap membaca syarat dan ketentuan penggunaan layanan Kosanku secara seksama sebelum menggunakan website.') }}
        </div>
    </div>
</div>
@endsection
