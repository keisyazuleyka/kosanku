@extends('layouts.dashboard')
@section('title', 'Notifikasi | Kosanku')
@section('heading', 'Notifikasi')
@section('subheading', 'Pantau informasi penting terkait booking, listing, dan pesan Anda')
@section('sidebar') @include('partials.sidebar-' . auth()->user()->role->value) @endsection

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <p class="text-xs text-[#667085]">
            Total {{ $notifications->total() }} notifikasi ({{ auth()->user()->unreadNotifications->count() }} belum dibaca)
        </p>

        @if(auth()->user()->unreadNotifications->count() > 0)
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button type="submit" class="btn-ghost text-xs py-1.5 px-3 rounded-xl font-semibold flex items-center gap-1.5 hover:text-[#405BE8]">
                    <i data-lucide="check-check" class="h-4 w-4"></i>
                    <span>Tandai Semua Sudah Dibaca</span>
                </button>
            </form>
        @endif
    </div>

    <div class="card overflow-hidden divide-y divide-[#E4E8EF]">
        @forelse($notifications as $notif)
            @php
                $isUnread = $notif->unread();
                $data = $notif->data;
                $url = route('notifications.read', $notif->id);
            @endphp
            <a href="{{ $url }}" class="flex items-start gap-4 p-4 transition {{ $isUnread ? 'bg-[#EEF2FF]/40 hover:bg-[#EEF2FF]/60' : 'bg-white hover:bg-[#F8FAFC]' }}">
                <!-- Icon Badge -->
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $isUnread ? 'bg-[#405BE8] text-white' : 'bg-[#F8FAFC] text-[#667085] border border-[#E4E8EF]' }}">
                    <i data-lucide="{{ $data['icon'] ?? 'bell' }}" class="h-5 w-5"></i>
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <h3 class="text-xs font-bold text-[#182230] truncate {{ $isUnread ? 'text-[#405BE8]' : '' }}">
                            {{ $data['title'] ?? 'Pemberitahuan' }}
                        </h3>
                        <span class="text-[10px] text-[#98A2B3] shrink-0">
                            {{ $notif->created_at->diffForHumans() }}
                        </span>
                    </div>
                    <p class="text-xs text-[#667085] mt-1 leading-relaxed">
                        {{ $data['message'] ?? '' }}
                    </p>
                </div>

                @if($isUnread)
                    <span class="h-2 w-2 rounded-full bg-[#405BE8] shrink-0 mt-2"></span>
                @endif
            </a>
        @empty
            <div class="p-12 text-center text-xs text-[#667085]">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[#EEF2FF] text-[#405BE8] mb-3">
                    <i data-lucide="bell-off" class="h-7 w-7"></i>
                </div>
                <h3 class="text-sm font-bold text-[#182230]">Belum Ada Notifikasi</h3>
                <p class="mt-1 text-xs text-[#667085] max-w-sm mx-auto">Pemberitahuan aktivitas pemesanan, verifikasi listing, dan pesan akan muncul di sini.</p>
            </div>
        @endforelse
    </div>

    <div>
        {{ $notifications->links() }}
    </div>
</div>
@endsection

