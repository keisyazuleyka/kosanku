<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Property;
use App\Notifications\AppNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $conversations = Conversation::query()
            ->where(function ($q) use ($user) {
                $q->where('tenant_id', $user->id)
                    ->orWhere('owner_id', $user->id);
            })
            ->with(['property.images', 'tenant', 'owner', 'latestMessage'])
            ->orderByDesc('last_message_at')
            ->orderByDesc('updated_at')
            ->get();

        $activeConversation = $conversations->first();

        if ($activeConversation) {
            $activeConversation->messages()
                ->where('sender_id', '!=', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true, 'read_at' => now()]);

            $activeConversation->load(['messages.sender']);
        }

        return view('chat.index', compact('conversations', 'activeConversation'));
    }

    public function show(Request $request, Conversation $conversation): View|JsonResponse
    {
        $user = $request->user();
        abort_unless($conversation->tenant_id === $user->id || $conversation->owner_id === $user->id, 403);

        $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        if ($request->expectsJson()) {
            return response()->json([
                'conversation' => $conversation->load(['property', 'tenant', 'owner']),
                'messages' => $conversation->messages()->with('sender')->get(),
            ]);
        }

        $conversations = Conversation::query()
            ->where(function ($q) use ($user) {
                $q->where('tenant_id', $user->id)
                    ->orWhere('owner_id', $user->id);
            })
            ->with(['property.images', 'tenant', 'owner', 'latestMessage'])
            ->orderByDesc('last_message_at')
            ->orderByDesc('updated_at')
            ->get();

        $activeConversation = $conversation->load(['property.images', 'tenant', 'owner', 'messages.sender']);

        return view('chat.index', compact('conversations', 'activeConversation'));
    }

    public function start(Request $request, Property $property): RedirectResponse
    {
        $user = $request->user();

        if ($property->owner_id === $user->id) {
            return back()->with('error', 'Anda tidak dapat memulai chat dengan properti Anda sendiri.');
        }

        $conversation = Conversation::query()->firstOrCreate(
            [
                'property_id' => $property->id,
                'tenant_id' => $user->id,
                'owner_id' => $property->owner_id,
            ],
            [
                'last_message_at' => now(),
            ]
        );

        if ($conversation->wasRecentlyCreated) {
            Message::query()->create([
                'conversation_id' => $conversation->id,
                'sender_id' => $user->id,
                'content' => 'Halo, saya tertarik dengan kost "'.$property->name.'". Apakah kamar masih tersedia?',
                'is_read' => false,
            ]);
        }

        return redirect()->route('chat.show', $conversation);
    }

    public function sendMessage(Request $request, Conversation $conversation): RedirectResponse|JsonResponse
    {
        $user = $request->user();
        abort_unless($conversation->tenant_id === $user->id || $conversation->owner_id === $user->id, 403);

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:2000'],
        ]);

        $message = Message::query()->create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'content' => $validated['content'],
            'is_read' => false,
        ]);

        $conversation->update(['last_message_at' => now()]);

        $recipient = $conversation->otherUser($user);
        if ($recipient) {
            $recipient->notify(new AppNotification(
                title: 'Pesan Baru dari '.$user->name,
                message: Str::limit($message->content, 60),
                url: route('chat.show', $conversation),
                type: 'chat',
                icon: 'message-square'
            ));
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message->load('sender'),
            ]);
        }

        return redirect()->route('chat.show', $conversation);
    }

    public function getMessages(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();
        abort_unless($conversation->tenant_id === $user->id || $conversation->owner_id === $user->id, 403);

        $afterId = $request->integer('after_id');

        $query = $conversation->messages()->with('sender');

        if ($afterId > 0) {
            $query->where('id', '>', $afterId);
        }

        $messages = $query->get();

        // Mark incoming messages as read
        $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json([
            'messages' => $messages,
        ]);
    }
}
