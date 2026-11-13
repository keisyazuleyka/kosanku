@extends('layouts.dashboard')
@section('title', 'Laporan Pengguna | Kosanku')
@section('heading', 'Laporan Pengguna')
@section('subheading', 'Proses dan tindak lanjuti laporan atas listing bermasalah')
@section('sidebar') @include('partials.sidebar-admin') @endsection

@section('content')
<div class="space-y-4">
    @forelse($reports as $report)
        <div class="rounded-2xl border border-[#E4E4E7] bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-[#E4E4E7]">
                <div>
                    <span class="badge {{ match($report->status->value) {
                        'pending' => 'bg-amber-50 text-amber-700 border border-amber-200',
                        'reviewing' => 'bg-blue-50 text-blue-700 border border-blue-200',
                        'resolved' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                        'dismissed' => 'bg-zinc-100 text-zinc-700',
                        default => 'bg-zinc-100 text-zinc-700'
                    } }}">{{ $report->status->label() }}</span>
                    <span class="text-xs text-[#71717A] ml-2">Dilaporkan pada {{ $report->created_at?->format('d M Y, H:i') }}</span>
                </div>
                <div class="text-xs text-[#71717A]">
                    Pelapor: <strong class="text-[#18181B]">{{ $report->reporter?->name ?? 'Anonim' }}</strong> ({{ $report->reporter?->email ?? '-' }})
                </div>
            </div>

            <div class="mt-4 grid gap-6 lg:grid-cols-2">
                <div>
                    <h4 class="text-xs font-bold text-[#71717A] uppercase tracking-wide">Kost yang Dilaporkan</h4>
                    <p class="mt-1 font-bold text-base text-[#18181B]">
                        <a href="{{ route('admin.kost.show', $report->property) }}" class="hover:text-[#4263EB] transition">
                            {{ $report->property->name }}
                        </a>
                    </p>
                    <p class="text-xs text-[#71717A] mt-0.5">{{ $report->property->address }}, {{ $report->property->city }}</p>

                    <h4 class="mt-4 text-xs font-bold text-[#71717A] uppercase tracking-wide">Alasan / Isi Laporan</h4>
                    <div class="mt-1.5 p-3.5 bg-rose-50/40 rounded-xl border border-rose-100 text-xs text-[#18181B] leading-relaxed whitespace-pre-line">
                        {{ $report->reason }}
                    </div>
                </div>

                <div class="bg-[#F5F6F8] p-5 rounded-xl border border-[#E4E4E7]">
                    <h4 class="text-xs font-bold text-[#18181B] uppercase tracking-wide mb-3 flex items-center gap-1.5">
                        <i data-lucide="edit-3" class="h-3.5 w-3.5 text-[#4263EB]"></i>
                        Tindak Lanjut Admin
                    </h4>
                    <form method="POST" action="{{ route('admin.reports.update', $report) }}" class="space-y-3">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Status Laporan</label>
                            <select class="input text-xs" name="status">
                                <option value="pending" @selected($report->status->value === 'pending')>Menunggu</option>
                                <option value="reviewing" @selected($report->status->value === 'reviewing')>Sedang Diproses</option>
                                <option value="resolved" @selected($report->status->value === 'resolved')>Selesai Ditangani</option>
                                <option value="dismissed" @selected($report->status->value === 'dismissed')>Ditolak / Tidak Valid</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Catatan Tindakan</label>
                            <textarea class="input text-xs" name="admin_notes" rows="2" placeholder="Catatan hasil investigasi atau tindakan...">{{ old('admin_notes', $report->admin_notes) }}</textarea>
                        </div>

                        <div class="text-right">
                            <button type="submit" class="btn-primary text-xs py-2 px-4 font-semibold">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="rounded-2xl border border-[#E4E4E7] bg-white p-12 text-center text-xs text-[#71717A] shadow-sm">
            <i data-lucide="check-check" class="mx-auto h-8 w-8 text-[#4263EB] mb-2"></i>
            Tidak ada laporan pengguna saat ini.
        </div>
    @endforelse
</div>

<div class="mt-6">
    {{ $reports->links() }}
</div>
@endsection
