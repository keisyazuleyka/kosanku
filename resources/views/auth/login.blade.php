@extends('layouts.auth')
@section('title', 'Masuk ke Akun | Kosanku')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold tracking-tight text-[#18181B]">Masuk ke Kosanku</h2>
    <p class="mt-1 text-xs text-[#71717A]">Selamat datang kembali! Masukkan kredensial akun Anda.</p>
</div>

@if (session('status'))
    <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-xs text-emerald-700">
        {{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('login') }}" class="space-y-4">
    @csrf

    <div>
        <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Alamat Email</label>
        <div class="relative">
            <i data-lucide="mail" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-[#A1A1AA]"></i>
            <input class="input pl-10 text-xs" type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus>
        </div>
        @error('email')
            <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <div class="flex items-center justify-between mb-1.5">
            <label class="block text-xs font-semibold text-[#18181B]">Kata Sandi</label>
            <a href="{{ route('password.request') }}" class="text-[11px] font-semibold text-[#4263EB] hover:underline">Lupa sandi?</a>
        </div>
        <div class="relative">
            <i data-lucide="lock" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-[#A1A1AA]"></i>
            <input class="input pl-10 text-xs" type="password" name="password" placeholder="••••••••" required>
        </div>
        @error('password')
            <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-center">
        <label class="flex items-center gap-2 text-xs text-[#71717A] cursor-pointer">
            <input type="checkbox" name="remember" class="rounded border-[#E4E4E7] text-[#4263EB] focus:ring-[#4263EB]/20">
            <span>Ingat sesi masuk saya</span>
        </label>
    </div>

    <button type="submit" class="btn-primary w-full py-2.5 text-xs font-semibold">
        Masuk Sekarang
    </button>
</form>

<div class="mt-6 border-t border-[#E4E4E7] pt-5 text-center text-xs text-[#71717A]">
    Belum memiliki akun?
    <a class="font-bold text-[#4263EB] hover:underline" href="{{ route('register') }}">Daftar di sini</a>
</div>
@endsection
