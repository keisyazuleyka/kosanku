@extends('layouts.dashboard')
@section('title', 'Detail Pengguna: ' . $user->name)
@section('heading', 'Detail Pengguna')
@section('subheading', 'Informasi akun, listing, dan transaksi terkait')
@section('sidebar') @include('partials.sidebar-admin') @endsection

@section('content')
<div class="mb-6 flex items-center justify-between">
    <a href="{{ route('admin.users.index') }}" class="btn-ghost text-xs inline-flex items-center gap-1.5 py-2 px-3">
        <i data-lucide="arrow-left" class="h-3.5 w-3.5"></i>
        <span>Kembali ke Daftar Pengguna</span>
    </a>
    @if(auth()->id() !== $user->id)
        <form method="POST" action="{{ route('admin.users.toggle', $user) }}" data-confirm="{{ $user->is_active ? 'Tangguhkan akun ini?' : 'Aktifkan akun ini?' }}">
            @csrf
            <button type="submit" class="btn-primary text-xs py-2 px-4 {{ $user->is_active ? 'bg-rose-600 hover:bg-rose-700' : 'bg-emerald-600 hover:bg-emerald-700' }}">
                {{ $user->is_active ? 'Tangguhkan Akun' : 'Aktifkan Akun' }}
            </button>
        </form>
    @endif
</div>

<div class="grid gap-6 lg:grid-cols-3">
    <!-- Left: Profile Card -->
    <div class="rounded-2xl border border-[#E4E4E7] bg-white p-6 shadow-sm lg:col-span-1 space-y-4">
        <div class="flex items-center gap-3.5 pb-4 border-b border-[#E4E4E7]">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#EFF2FE] font-black text-lg text-[#4263EB]">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h3 class="text-sm font-bold text-[#18181B]">{{ $user->name }}</h3>
                <p class="text-xs text-[#71717A]">{{ $user->email }}</p>
            </div>
        </div>

        <div class="space-y-3 text-xs">
            <div>
                <span class="text-[#71717A]">Nomor WhatsApp / HP:</span>
                <p class="font-medium text-[#18181B] mt-0.5">{{ $user->phone ?: '-' }}</p>
            </div>
            <div>
                <span class="text-[#71717A]">Kota Domisili:</span>
                <p class="font-medium text-[#18181B] mt-0.5">{{ $user->city ?: '-' }}</p>
            </div>
            <div>
                <span class="text-[#71717A]">Role Akun:</span>
                <div class="mt-1">
                    <span class="badge {{ match($user->role->value) {
                        'admin' => 'bg-purple-50 text-purple-700 border border-purple-200',
                        'owner' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                        'tenant' => 'bg-blue-50 text-blue-700 border border-blue-200',
                        default => 'bg-zinc-100 text-zinc-700'
                    } }}">
                        {{ $user->role->label() }}
                    </span>
                </div>
            </div>
            <div>
                <span class="text-[#71717A]">Status Akun:</span>
                <div class="mt-1">
                    <span class="badge {{ $user->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                        {{ $user->is_active ? 'Aktif' : 'Ditangguhkan' }}
                    </span>
                </div>
            </div>
            <div>
                <span class="text-[#71717A]">Terdaftar Sejak:</span>
                <p class="font-medium text-[#18181B] mt-0.5">{{ $user->created_at?->format('d F Y, H:i') }}</p>
            </div>
            @if($user->bio)
            <div class="pt-2 border-t border-[#E4E4E7]">
                <span class="text-[#71717A]">Bio:</span>
                <p class="text-[#18181B] mt-1 leading-relaxed">{{ $user->bio }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Right: Properties or Bookings -->
    <div class="lg:col-span-2 space-y-6">
        @if($user->isOwner())
        <div class="rounded-2xl border border-[#E4E4E7] bg-white overflow-hidden shadow-sm">
            <div class="p-4 border-b border-[#E4E4E7]">
                <h3 class="font-bold text-xs text-[#18181B] uppercase tracking-wide">Listing Kost Milik Pengguna ({{ $user->properties->count() }})</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#F5F6F8] text-[11px] font-semibold text-[#71717A] uppercase tracking-wide">
                        <tr>
                            <th class="p-3">Nama Kost</th>
                            <th>Kota</th>
                            <th>Harga</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E4E4E7]">
                    @forelse($user->properties as $property)
                        <tr class="hover:bg-[#F5F6F8]/60 transition">
                            <td class="p-3 font-bold text-[#18181B]">{{ $property->name }}</td>
                            <td class="text-[#71717A]">{{ $property->city }}</td>
                            <td class="font-bold text-[#18181B]">{{ rupiah($property->monthly_price) }}</td>
                            <td><span class="badge {{ $property->status->badgeClass() }}">{{ $property->status->label() }}</span></td>
                            <td class="p-3 text-right"><a class="font-semibold text-[#4263EB] hover:underline" href="{{ route('admin.kost.show', $property) }}">Lihat &rarr;</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-6 text-center text-xs text-[#71717A]">Belum memiliki listing kost.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-2xl border border-[#E4E4E7] bg-white overflow-hidden shadow-sm">
            <div class="p-4 border-b border-[#E4E4E7]">
                <h3 class="font-bold text-xs text-[#18181B] uppercase tracking-wide">Pengajuan Booking Diterima ({{ $user->ownerBookings->count() }})</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#F5F6F8] text-[11px] font-semibold text-[#71717A] uppercase tracking-wide">
                        <tr>
                            <th class="p-3">Kode</th>
                            <th>Kost</th>
                            <th>Penyewa</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E4E4E7]">
                    @forelse($user->ownerBookings as $b)
                        <tr class="hover:bg-[#F5F6F8]/60 transition">
                            <td class="p-3 font-mono font-bold text-[#4263EB]">{{ $b->code }}</td>
                            <td class="font-medium text-[#18181B]">{{ $b->property->name }}</td>
                            <td class="text-[#71717A]">{{ $b->tenant?->name ?? 'Anonim' }}</td>
                            <td><span class="badge {{ $b->status->badgeClass() }}">{{ $b->status->label() }}</span></td>
                            <td class="p-3 text-right"><a class="font-semibold text-[#4263EB] hover:underline" href="{{ route('admin.bookings.show', $b) }}">Lihat &rarr;</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-6 text-center text-xs text-[#71717A]">Belum ada booking yang masuk.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if($user->isTenant())
        <div class="rounded-2xl border border-[#E4E4E7] bg-white overflow-hidden shadow-sm">
            <div class="p-4 border-b border-[#E4E4E7]">
                <h3 class="font-bold text-xs text-[#18181B] uppercase tracking-wide">Riwayat Booking Penyewa ({{ $user->bookings->count() }})</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#F5F6F8] text-[11px] font-semibold text-[#71717A] uppercase tracking-wide">
                        <tr>
                            <th class="p-3">Kode</th>
                            <th>Kost</th>
                            <th>Tgl Masuk</th>
                            <th>Total Estimasi</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E4E4E7]">
                    @forelse($user->bookings as $b)
                        <tr class="hover:bg-[#F5F6F8]/60 transition">
                            <td class="p-3 font-mono font-bold text-[#4263EB]">{{ $b->code }}</td>
                            <td class="font-medium text-[#18181B]">{{ $b->property->name }}</td>
                            <td class="text-[#71717A]">{{ $b->start_date?->format('d M Y') }}</td>
                            <td class="font-bold text-[#18181B]">{{ rupiah($b->total_estimate) }}</td>
                            <td><span class="badge {{ $b->status->badgeClass() }}">{{ $b->status->label() }}</span></td>
                            <td class="p-3 text-right"><a class="font-semibold text-[#4263EB] hover:underline" href="{{ route('admin.bookings.show', $b) }}">Lihat &rarr;</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-6 text-center text-xs text-[#71717A]">Belum pernah mengajukan sewa.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
