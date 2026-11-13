@extends('layouts.auth')
@section('title', 'Daftar Akun Baru | Kosanku')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold tracking-tight text-[#18181B]">Daftar Akun Baru</h2>
    <p class="mt-1 text-xs text-[#71717A]">Pilih peran Anda dan mulai gunakan Kosanku.</p>
</div>

@if($errors->any())
    <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs text-rose-700">
        {{ $errors->first() }}
    </div>
@endif

<form method="POST" action="{{ route('register') }}" class="space-y-4">
    @csrf

    <div>
        <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Nama Lengkap</label>
        <div class="relative">
            <i data-lucide="user" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-[#A1A1AA]"></i>
            <input class="input pl-10 text-xs" name="name" value="{{ old('name') }}" placeholder="Contoh: Andi Pratama" required autofocus>
        </div>
    </div>

    <div>
        <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Alamat Email</label>
        <div class="relative">
            <i data-lucide="mail" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-[#A1A1AA]"></i>
            <input class="input pl-10 text-xs" type="email" name="email" value="{{ old('email') }}" placeholder="andi@example.com" required>
        </div>
    </div>

    <div>
        <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Nomor Telepon / WhatsApp</label>
        <div class="relative">
            <i data-lucide="phone" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-[#A1A1AA]"></i>
            <input class="input pl-10 text-xs" name="phone" value="{{ old('phone') }}" placeholder="08123456789">
        </div>
    </div>

    <div>
        <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Daftar Sebagai</label>
        <div class="relative">
            <i data-lucide="users" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-[#A1A1AA]"></i>
            <select class="input pl-10 text-xs" name="role" required>
                <option value="tenant" @selected(old('role')==='tenant')>Penyewa Kost (Mahasiswa / Pekerja)</option>
                <option value="owner" @selected(old('role')==='owner')>Pemilik Properti Kost</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
            <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Kata Sandi</label>
            <input class="input text-xs" type="password" name="password" placeholder="••••••••" required>
        </div>
        <div>
            <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Konfirmasi Sandi</label>
            <input class="input text-xs" type="password" name="password_confirmation" placeholder="••••••••" required>
        </div>
    </div>

    <button type="submit" class="btn-primary w-full py-2.5 text-xs font-semibold">
        Daftar Sekarang
    </button>
</form>

<div class="mt-6 border-t border-[#E4E4E7] pt-5 text-center text-xs text-[#71717A]">
    Sudah memiliki akun?
    <a class="font-bold text-[#4263EB] hover:underline" href="{{ route('login') }}">Masuk di sini</a>
</div>
@endsection
