@extends('layouts.auth')
@section('title', 'Atur Kata Sandi Baru | Kosanku')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold tracking-tight text-[#18181B]">Atur Kata Sandi Baru</h2>
    <p class="mt-1 text-xs text-[#71717A]">Silakan buat kata sandi baru yang aman untuk akun Anda.</p>
</div>

<form method="POST" action="{{ route('password.update') }}" class="space-y-4">
    @csrf
    <input type="hidden" name="token" value="{{ $request->route('token') }}">

    <div>
        <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Alamat Email</label>
        <input class="input text-xs" type="email" name="email" value="{{ old('email', $request->email) }}" required readonly>
        @error('email')
            <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Kata Sandi Baru</label>
        <input class="input text-xs" type="password" name="password" placeholder="••••••••" required autofocus>
        @error('password')
            <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-xs font-semibold text-[#18181B] mb-1.5">Konfirmasi Kata Sandi</label>
        <input class="input text-xs" type="password" name="password_confirmation" placeholder="••••••••" required>
    </div>

    <button type="submit" class="btn-primary w-full py-2.5 text-xs font-semibold">
        Simpan Kata Sandi Baru
    </button>
</form>
@endsection
