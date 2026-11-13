@extends('layouts.dashboard')
@section('title', 'Ulasan Saya | Kosanku')
@section('heading', 'Ulasan Saya')
@section('subheading', 'Daftar ulasan yang pernah Anda berikan untuk kamar kost')
@section('sidebar') @include('partials.sidebar-tenant') @endsection

@section('content')
<div class="rounded-2xl border border-[#E4E4E7] bg-white overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-[#F5F6F8] text-[11px] font-semibold text-[#71717A] uppercase tracking-wide">
                <tr>
                    <th class="p-4">Tanggal</th>
                    <th>Nama Kost</th>
                    <th>Rating</th>
                    <th>Ulasan</th>
                    <th class="p-4 text-right">Lihat Kost</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E4E4E7]">
            @forelse($reviews as $review)
                <tr class="hover:bg-[#F5F6F8]/60 transition">
                    <td class="p-4 text-[11px] text-[#71717A] whitespace-nowrap">{{ $review->created_at?->format('d M Y') }}</td>
                    <td class="font-bold text-[#18181B]">
                        <a href="{{ route('kost.show', $review->property->slug) }}" class="hover:text-[#4263EB] transition">
                            {{ $review->property->name }}
                        </a>
                    </td>
                    <td>
                        <span class="inline-flex items-center gap-1 font-bold text-amber-500">
                            <i data-lucide="star" class="h-3.5 w-3.5 fill-amber-400 text-amber-400"></i>
                            {{ $review->rating }} / 5
                        </span>
                    </td>
                    <td class="text-[#71717A] max-w-md py-3 leading-relaxed">{{ $review->comment }}</td>
                    <td class="p-4 text-right">
                        <a class="font-semibold text-[#4263EB] hover:underline" href="{{ route('kost.show', $review->property->slug) }}">
                            Buka Kost &rarr;
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-xs text-[#71717A]">
                        Anda belum pernah memberikan ulasan kost.
                    </td>
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
