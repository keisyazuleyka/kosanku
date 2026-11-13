@extends('layouts.auth')
@section('title', 'Lupa Kata Sandi | Kosanku')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold tracking-tight text-[#18181B]">Lupa Kata Sandi</h2>
    <p class="mt-1 text-xs text-[#71717A]">Masukkan email terdaftar Anda untuk menerima tautan pemulihan kata sandi.</p>
</div>

@if (session('status'))
    <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-xs text-emerald-700">
        {{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('password.email') }}" class="space-y-4">
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

    <button type="submit" class="btn-primary w-full py-2.5 text-xs font-semibold">
        Kirim Tautan Pemulihan
    </button>
</form>

<div class="mt-6 border-t border-[#E4E4E7] pt-5 text-center text-xs text-[#71717A]">
    Ingat kata sandi Anda?
    <a class="font-bold text-[#4263EB] hover:underline" href="{{ route('login') }}">Kembali ke login</a>
</div>
@endsection
