@extends('layouts.public')
@section('title', 'Pesan & Chat | ' . setting('site_name', 'Kosanku'))

@section('content')
<div class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
    <div class="mb-5 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#182230]">Pesan & Chat</h1>
            <p class="mt-1 text-xs text-[#667085]">Komunikasi langsung antara penyewa dan pemilik kost</p>
        </div>
        @auth
            <a href="{{ route(auth()->user()->role->dashboardRoute()) }}" class="btn-ghost text-xs py-2 px-3">
                <i data-lucide="layout-dashboard" class="h-3.5 w-3.5 text-[#405BE8]"></i>
                <span>Ke Dashboard</span>
            </a>
        @endauth
    </div>

    <div class="rounded-3xl border border-[#E4E8EF] bg-white shadow-sm overflow-hidden grid grid-cols-1 md:grid-cols-3 min-h-[580px]">
        <!-- Left: Conversations List -->
        <div class="border-b md:border-b-0 md:border-r border-[#E4E8EF] bg-white flex flex-col">
            <div class="p-4 border-b border-[#E4E8EF]">
                <div class="relative">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-[#98A2B3]"></i>
                    <input type="text" id="chat-search" placeholder="Cari percakapan..." class="input pl-9 text-xs py-2" oninput="filterConversations(this.value)">
                </div>
            </div>

            <div class="flex-1 overflow-y-auto divide-y divide-[#E4E8EF]/60" id="conversations-list">
                @forelse($conversations as $conv)
                    @php
                        $other = $conv->otherUser(auth()->user());
                        $isActive = $activeConversation && $activeConversation->id === $conv->id;
                        $unread = $conv->unreadCountFor(auth()->user());
                    @endphp
                    <a href="{{ route('chat.show', $conv) }}" class="flex items-start gap-3 p-4 hover:bg-[#EEF2FF]/40 transition conversation-item {{ $isActive ? 'bg-[#EEF2FF]/60 border-l-4 border-[#405BE8]' : '' }}" data-name="{{ strtolower($other->name) }} {{ strtolower($conv->property?->name ?? '') }}">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#EEF2FF] font-bold text-xs text-[#405BE8] border border-[#405BE8]/20">
                            {{ strtoupper(substr($other->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-bold text-[#182230] truncate">{{ $other->name }}</h3>
                                <span class="text-[10px] text-[#98A2B3] shrink-0">{{ $conv->last_message_at ? $conv->last_message_at->diffForHumans(null, true, true) : '' }}</span>
                            </div>
                            @if($conv->property)
                                <p class="text-[11px] font-medium text-[#405BE8] truncate">{{ $conv->property->name }}</p>
                            @endif
                            <p class="text-xs text-[#667085] truncate mt-0.5">
                                {{ $conv->latestMessage ? $conv->latestMessage->content : 'Mulai obrolan...' }}
                            </p>
                        </div>
                        @if($unread > 0)
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#405BE8] text-[10px] font-bold text-white">
                                {{ $unread }}
                            </span>
                        @endif
                    </a>
                @empty
                    <div class="p-8 text-center text-xs text-[#667085]">
                        <i data-lucide="message-square-off" class="mx-auto h-8 w-8 text-[#98A2B3] mb-2"></i>
                        Belum ada percakapan aktif. Klik tombol "Chat" pada detail kost untuk memulai obrolan dengan pemilik!
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right: Active Chat Window -->
        <div class="md:col-span-2 flex flex-col bg-[#F8FAFC]">
            @if($activeConversation)
                @php
                    $otherUser = $activeConversation->otherUser(auth()->user());
                @endphp
                <!-- Header -->
                <div class="p-4 border-b border-[#E4E8EF] bg-white flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#EEF2FF] font-bold text-sm text-[#405BE8] border border-[#405BE8]/20">
                            {{ strtoupper(substr($otherUser->name, 0, 1)) }}
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-[#182230]">{{ $otherUser->name }}</h2>
                            <p class="text-xs text-[#667085]">
                                {{ $otherUser->isOwner() ? 'Pemilik Kost' : 'Penyewa' }}
                                @if($activeConversation->property)
                                    · <a href="{{ route('kost.show', $activeConversation->property->slug) }}" target="_blank" class="text-[#405BE8] hover:underline font-semibold">{{ $activeConversation->property->name }}</a>
                                @endif
                            </p>
                        </div>
                    </div>
                    @if($activeConversation->property)
                        <a href="{{ route('kost.booking.create', $activeConversation->property->slug) }}" class="btn-primary text-xs py-1.5 px-3">
                            Ajukan Booking
                        </a>
                    @endif
                </div>

                <!-- Messages Thread -->
                <div class="flex-1 p-4 overflow-y-auto space-y-3" id="messages-container">
                    @forelse($activeConversation->messages as $msg)
                        @php($isMe = $msg->sender_id === auth()->id())
                        <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }}" data-message-id="{{ $msg->id }}">
                            <div class="max-w-[75%] rounded-2xl px-4 py-2.5 text-xs leading-relaxed shadow-sm {{ $isMe ? 'bg-[#405BE8] text-white rounded-br-xs' : 'bg-white text-[#182230] border border-[#E4E8EF] rounded-bl-xs' }}">
                                <p class="whitespace-pre-line">{{ $msg->content }}</p>
                            </div>
                            <div class="flex items-center gap-1.5 mt-1 text-[10px] text-[#98A2B3]">
                                <span>{{ $msg->created_at?->format('H:i') }}</span>
                                @if($isMe)
                                    @if($msg->is_read)
                                        <span class="text-[#405BE8] font-bold" title="Dibaca">✓✓</span>
                                    @else
                                        <span>✓</span>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="flex h-full items-center justify-center text-xs text-[#667085]">
                            Mulai obrolan Anda sekarang...
                        </div>
                    @endforelse
                </div>

                <!-- Input Form -->
                <div class="p-3 bg-white border-t border-[#E4E8EF]">
                    <form id="chat-form" onsubmit="sendMessage(event)" class="flex items-center gap-2">
                        @csrf
                        <input type="text" id="message-input" name="content" placeholder="Ketik pesan Anda di sini..." class="input text-xs flex-1 py-2.5" required autocomplete="off">
                        <button type="submit" id="btn-send-message" class="btn-primary py-2.5 px-4 text-xs font-semibold shrink-0 flex items-center gap-1.5 cursor-pointer">
                            <span>Kirim</span>
                            <i data-lucide="send" class="h-3.5 w-3.5"></i>
                        </button>
                    </form>
                </div>
            @else
                <!-- No Conversation Selected -->
                <div class="flex flex-1 flex-col items-center justify-center p-8 text-center text-xs text-[#667085]">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#EEF2FF] text-[#405BE8] mb-3">
                        <i data-lucide="message-square" class="h-7 w-7"></i>
                    </div>
                    <h3 class="text-sm font-bold text-[#182230]">Belum Ada Percakapan yang Dipilih</h3>
                    <p class="mt-1 text-xs text-[#667085] max-w-sm">Pilih salah satu percakapan di sebelah kiri atau buka halaman kost dan klik "Chat" untuk menghubungi pemilik kost.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@if($activeConversation)
<script>
    const conversationId = {{ $activeConversation->id }};
    const currentUserId = {{ auth()->id() }};
    const container = document.getElementById('messages-container');
    const renderedMessageIds = new Set();
    let lastMessageId = 0;

    // Track all existing rendered message IDs to prevent duplicates
    document.querySelectorAll('[data-message-id]').forEach(el => {
        const id = parseInt(el.getAttribute('data-message-id'));
        if (id) {
            renderedMessageIds.add(id);
            if (id > lastMessageId) {
                lastMessageId = id;
            }
        }
    });

    function scrollToBottom() {
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }

    scrollToBottom();

    function filterConversations(val) {
        val = val.toLowerCase().trim();
        document.querySelectorAll('.conversation-item').forEach(el => {
            const name = el.getAttribute('data-name');
            if (!val || name.includes(val)) {
                el.style.display = 'flex';
            } else {
                el.style.display = 'none';
            }
        });
    }

    let isSending = false;
    async function sendMessage(event) {
        event.preventDefault();
        if (isSending) return;

        const input = document.getElementById('message-input');
        const sendBtn = document.getElementById('btn-send-message');
        const content = input.value.trim();
        if (!content) return;

        isSending = true;
        if (sendBtn) sendBtn.disabled = true;

        try {
            const res = await fetch("{{ route('chat.messages.send', $activeConversation) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ content: content })
            });

            const data = await res.json();
            if (data.success && data.message) {
                input.value = '';
                input.focus();
                appendMessage(data.message, true);
                scrollToBottom();
            }
        } catch (err) {
            console.error('Send error:', err);
        } finally {
            isSending = false;
            if (sendBtn) sendBtn.disabled = false;
        }
    }

    function appendMessage(msg, isMe) {
        // STRICT DEDUPLICATION: Never append if ID is already rendered
        if (renderedMessageIds.has(msg.id)) {
            return;
        }
        renderedMessageIds.add(msg.id);
        if (msg.id > lastMessageId) {
            lastMessageId = msg.id;
        }

        const timeStr = new Date(msg.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        const div = document.createElement('div');
        div.className = `flex flex-col ${isMe ? 'items-end' : 'items-start'}`;
        div.setAttribute('data-message-id', msg.id);

        div.innerHTML = `
            <div class="max-w-[75%] rounded-2xl px-4 py-2.5 text-xs leading-relaxed shadow-sm ${isMe ? 'bg-[#405BE8] text-white rounded-br-xs' : 'bg-white text-[#182230] border border-[#E4E8EF] rounded-bl-xs'}">
                <p class="whitespace-pre-line">${escapeHtml(msg.content)}</p>
            </div>
            <div class="flex items-center gap-1.5 mt-1 text-[10px] text-[#98A2B3]">
                <span>${timeStr}</span>
                ${isMe ? '<span class="text-[#405BE8] font-bold">✓</span>' : ''}
            </div>
        `;

        container.appendChild(div);
    }

    function escapeHtml(text) {
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    // Polling for incoming messages with strict deduplication
    async function pollMessages() {
        try {
            const res = await fetch(`/api/chat/${conversationId}/messages?after_id=${lastMessageId}`);
            if (res.ok) {
                const data = await res.json();
                if (data.messages && data.messages.length > 0) {
                    let hasNew = false;
                    data.messages.forEach(msg => {
                        if (!renderedMessageIds.has(msg.id)) {
                            const isMe = msg.sender_id === currentUserId;
                            appendMessage(msg, isMe);
                            hasNew = true;
                        }
                    });
                    if (hasNew) {
                        scrollToBottom();
                    }
                }
            }
        } catch (e) {
            // silent polling retry
        }
    }

    const pollingInterval = setInterval(pollMessages, 3500);
    window.addEventListener('beforeunload', () => clearInterval(pollingInterval));
</script>
@endif
@endsection

