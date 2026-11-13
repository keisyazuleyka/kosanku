@extends('layouts.public')
@section('title', 'Ajukan Booking: ' . $property->name . ' | ' . setting('site_name', 'Kosanku'))

@section('content')
<div class="mx-auto max-w-2xl px-4 py-8 sm:px-6">
    <!-- Back to detail -->
    <div class="mb-6">
        <a href="{{ route('kost.show', $property->slug) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#667085] hover:text-[#405BE8] transition">
            <i data-lucide="arrow-left" class="h-4 w-4"></i>
            <span>Kembali ke detail kost</span>
        </a>
    </div>

    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-[#182230] sm:text-3xl">Ajukan Booking</h1>
        <p class="mt-1 text-xs text-[#667085] sm:text-sm">Lengkapi informasi berikut untuk mengajukan booking kost.</p>
    </div>

    <!-- Kost Preview Summary Card -->
    <div class="rounded-2xl border border-[#E4E8EF] bg-white p-4 shadow-sm flex items-center gap-4 mb-6">
        <div class="h-20 w-24 shrink-0 rounded-xl overflow-hidden bg-zinc-100 border border-[#E4E8EF]">
            <img src="{{ $property->coverUrl() }}" alt="{{ $property->name }}" class="h-full w-full object-cover" onerror="this.src='{{ asset('images/kost-fallback.svg') }}'">
        </div>
        <div class="flex-1 min-w-0">
            <h2 class="text-sm font-bold text-[#182230] truncate">{{ $property->name }}</h2>
            <p class="text-xs text-[#667085] flex items-center gap-1 mt-0.5">
                <i data-lucide="map-pin" class="h-3 w-3 text-[#405BE8] shrink-0"></i>
                <span class="truncate">{{ $property->district ? $property->district . ', ' : '' }}{{ $property->city }}</span>
            </p>
            <p class="text-xs font-bold text-[#405BE8] mt-1.5">
                {{ rupiah($property->monthly_price) }} <span class="text-[11px] font-normal text-[#667085]">/ bulan</span>
            </p>
        </div>
    </div>

    <!-- Booking Form -->
    <form method="POST" action="{{ route('bookings.store', $property) }}" class="rounded-2xl border border-[#E4E8EF] bg-white p-6 sm:p-8 shadow-sm space-y-6">
        @csrf

        <!-- Data Sewa -->
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-[#182230] mb-3">Data Sewa</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#182230] mb-1.5">Tanggal Masuk <span class="text-rose-500">*</span></label>
                    <input type="date" name="start_date" id="booking-start-date" value="{{ old('start_date', $startDate) }}" min="{{ now()->toDateString() }}" required class="input text-xs" onchange="calculateTotal()">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#182230] mb-1.5">Durasi Sewa <span class="text-rose-500">*</span></label>
                    <select name="duration_months" id="booking-duration" class="input text-xs cursor-pointer" onchange="calculateTotal()">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" @selected(old('duration_months', $duration) == $i)>{{ $i }} Bulan</option>
                        @endfor
                    </select>
                </div>
            </div>

            <input type="hidden" name="rooms_requested" value="{{ $rooms }}">

            <div class="mt-4 rounded-xl bg-[#F8FAFC] border border-[#E4E8EF] p-4 flex items-center justify-between text-xs">
                <span class="text-[#667085]">Total Estimasi Biaya:</span>
                <span class="text-base font-black text-[#405BE8]" id="booking-total-display">
                    {{ rupiah(($property->monthly_price * $duration) + $property->deposit) }}
                </span>
            </div>
            @if($property->deposit > 0)
                <p class="mt-1 text-[11px] text-[#98A2B3] text-right">*Termasuk deposit sebesar {{ rupiah($property->deposit) }}</p>
            @endif
        </div>

        <!-- Data Diri -->
        <div class="border-t border-[#E4E8EF] pt-5">
            <h3 class="text-xs font-bold uppercase tracking-wider text-[#182230] mb-3">Data Diri</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#182230] mb-1.5">Nama Lengkap</label>
                    <input type="text" class="input text-xs bg-zinc-50" value="{{ auth()->user()->name }}" readonly>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#182230] mb-1.5">Nomor Telepon</label>
                    <input type="text" class="input text-xs bg-zinc-50" value="{{ auth()->user()->phone ?: 'Belum diisi di profil' }}" readonly>
                </div>
            </div>
        </div>

        <!-- Pesan (Opsional) -->
        <div class="border-t border-[#E4E8EF] pt-5">
            <label class="block text-xs font-semibold text-[#182230] mb-1.5">Pesan (Opsional)</label>
            <textarea name="tenant_notes" rows="3" placeholder="Tulis pesan untuk pemilik kost..." class="input text-xs">{{ old('tenant_notes') }}</textarea>
        </div>

        <!-- Agreement Checkbox -->
        <div class="border-t border-[#E4E8EF] pt-4">
            <label class="flex items-start gap-2.5 text-xs text-[#667085] cursor-pointer">
                <input type="checkbox" required class="mt-0.5 rounded border-[#E4E8EF] text-[#405BE8] focus:ring-[#405BE8]/20">
                <span>Saya setuju dengan <a href="{{ route('pages.terms') }}" target="_blank" class="text-[#405BE8] hover:underline font-semibold">Syarat dan Ketentuan</a> sewa kost yang berlaku di platform Kosanku.</span>
            </label>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn-primary w-full py-3 text-xs font-bold shadow-md cursor-pointer" @disabled(!$property->hasAvailability())>
            {{ $property->hasAvailability() ? 'Ajukan Booking' : 'Kamar Penuh' }}
        </button>
    </form>
</div>

<script>
    const monthlyPrice = {{ $property->monthly_price }};
    const depositPrice = {{ $property->deposit }};

    function calculateTotal() {
        const duration = parseInt(document.getElementById('booking-duration').value) || 1;
        const total = (monthlyPrice * duration) + depositPrice;
        document.getElementById('booking-total-display').textContent = 'Rp ' + total.toLocaleString('id-ID');
    }
</script>
@endsection

