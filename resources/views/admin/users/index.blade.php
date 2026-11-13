@extends('layouts.dashboard')
@section('title', 'Manajemen Pengguna | Kosanku')
@section('heading', 'Manajemen Pengguna')
@section('subheading', 'Daftar semua akun pengguna Kosanku')
@section('sidebar') @include('partials.sidebar-admin') @endsection

@section('content')
<!-- Filter Bar -->
<div class="mb-6 rounded-2xl border border-[#E4E4E7] bg-white p-4 shadow-sm">
    <form class="flex flex-col sm:flex-row flex-wrap items-center gap-3" method="GET" action="{{ route('admin.users.index') }}">
        <div class="relative flex-1 min-w-[200px] w-full sm:w-auto">
            <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-[#A1A1AA]"></i>
            <input class="input pl-9 text-xs" name="q" value="{{ request('q') }}" placeholder="Cari nama atau email...">
        </div>

        <select class="input text-xs w-full sm:w-40" name="role">
            <option value="">Semua Role</option>
            @foreach($roles as $role)
                <option value="{{ $role->value }}" @selected(request('role') === $role->value)>{{ $role->label() }}</option>
            @endforeach
        </select>

        <select class="input text-xs w-full sm:w-40" name="status">
            <option value="">Semua Status</option>
            <option value="active" @selected(request('status') === 'active')>Aktif</option>
            <option value="inactive" @selected(request('status') === 'inactive')>Ditangguhkan</option>
        </select>

        <button type="submit" class="btn-primary text-xs py-2 px-4 flex items-center gap-1.5 w-full sm:w-auto justify-center">
            <i data-lucide="filter" class="h-3.5 w-3.5"></i>
            <span>Filter</span>
        </button>

        @if(request()->hasAny(['q', 'role', 'status']))
            <a href="{{ route('admin.users.index') }}" class="text-xs text-[#71717A] hover:text-[#4263EB] px-2">Reset</a>
        @endif
    </form>
</div>

<!-- Table Card -->
<div class="rounded-2xl border border-[#E4E4E7] bg-white overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-[#F5F6F8] text-[11px] font-semibold text-[#71717A] uppercase tracking-wide">
                <tr>
                    <th class="p-4">Nama Lengkap</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Terdaftar Sejak</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E4E4E7]">
            @forelse($users as $user)
                <tr class="hover:bg-[#F5F6F8]/60 transition">
                    <td class="p-4 font-bold text-[#18181B]">{{ $user->name }}</td>
                    <td class="text-[#71717A]">{{ $user->email }}</td>
                    <td>
                        <span class="badge {{ match($user->role->value) {
                            'admin' => 'bg-purple-50 text-purple-700 border border-purple-200',
                            'owner' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                            'tenant' => 'bg-blue-50 text-blue-700 border border-blue-200',
                            default => 'bg-zinc-100 text-zinc-700'
                        } }}">
                            {{ $user->role->label() }}
                        </span>
                    </td>
                    <td>
                        @if($user->is_active)
                            <span class="badge bg-emerald-50 text-emerald-700 border border-emerald-200">Aktif</span>
                        @else
                            <span class="badge bg-rose-50 text-rose-700 border border-rose-200">Ditangguhkan</span>
                        @endif
                    </td>
                    <td class="text-[#71717A]">{{ $user->created_at?->format('d M Y') }}</td>
                    <td class="p-4 text-right space-x-2">
                        <a class="font-semibold text-[#4263EB] hover:underline" href="{{ route('admin.users.show', $user) }}">Detail</a>
                        @if(auth()->id() !== $user->id)
                            <form method="POST" action="{{ route('admin.users.toggle', $user) }}" class="inline" data-confirm="{{ $user->is_active ? 'Yakin tangguhkan akun ini?' : 'Aktifkan kembali akun ini?' }}">
                                @csrf
                                <button type="submit" class="text-xs font-semibold {{ $user->is_active ? 'text-rose-600 hover:text-rose-700' : 'text-emerald-600 hover:text-emerald-700' }}">
                                    {{ $user->is_active ? 'Tangguhkan' : 'Aktifkan' }}
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="p-8 text-center text-xs text-[#71717A]">Tidak ada pengguna yang cocok.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $users->links() }}
</div>
@endsection
