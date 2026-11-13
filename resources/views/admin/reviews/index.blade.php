@extends('layouts.dashboard')
@section('title', 'Moderasi Ulasan | Kosanku')
@section('heading', 'Moderasi Ulasan')
@section('subheading', 'Tinjau ulasan dan sembunyikan review yang melanggar ketentuan')
@section('sidebar') @include('partials.sidebar-admin') @endsection

@section('content')
<div class="rounded-2xl border border-[#E4E4E7] bg-white overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-[#F5F6F8] text-[11px] font-semibold text-[#71717A] uppercase tracking-wide">
                <tr>
                    <th class="p-4">Tanggal</th>
                    <th>Pengguna</th>
                    <th>Nama Kost</th>
                    <th>Rating</th>
                    <th>Komentar Ulasan</th>
                    <th>Status Tampil</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E4E4E7]">
            @forelse($reviews as $review)
                <tr class="hover:bg-[#F5F6F8]/60 transition">
                    <td class="p-4 text-[11px] text-[#71717A] whitespace-nowrap">{{ $review->created_at?->format('d M Y') }}</td>
                    <td class="font-bold text-[#18181B]">{{ $review->user->name }}</td>
                    <td>
                        <a href="{{ route('kost.show', $review->property->slug) }}" target="_blank" class="font-semibold text-[#18181B] hover:text-[#4263EB] transition">
                            {{ $review->property->name }}
                        </a>
                    </td>
                    <td>
                        <span class="inline-flex items-center gap-1 font-bold text-amber-500">
                            <i data-lucide="star" class="h-3.5 w-3.5 fill-amber-400 text-amber-400"></i>
                            {{ $review->rating }} / 5
                        </span>
                    </td>
                    <td class="max-w-xs truncate py-3 text-[#71717A]" title="{{ $review->comment }}">{{ $review->comment }}</td>
                    <td>
                        @if($review->is_hidden)
                            <span class="badge bg-rose-50 text-rose-700 border border-rose-200">Disembunyikan</span>
                        @else
                            <span class="badge bg-emerald-50 text-emerald-700 border border-emerald-200">Tampil Publik</span>
                        @endif
                    </td>
                    <td class="p-4 text-right">
                        <form method="POST" action="{{ route('admin.reviews.toggle', $review) }}" class="inline" data-confirm="{{ $review->is_hidden ? 'Tampilkan ulasan ini kembali?' : 'Sembunyikan ulasan ini dari publik?' }}">
                            @csrf
                            <button type="submit" class="text-xs font-semibold {{ $review->is_hidden ? 'text-emerald-600 hover:text-emerald-700' : 'text-rose-600 hover:text-rose-700' }}">
                                {{ $review->is_hidden ? 'Tampilkan' : 'Sembunyikan' }}
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="p-8 text-center text-xs text-[#71717A]">Belum ada ulasan yang masuk.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $reviews->links() }}
</div>
@endsection
