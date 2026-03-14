<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\Message;
use App\Models\User;
use App\Support\MessageThreadBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Schema;

class MessageController extends Controller
{
    public function __construct(
        private readonly MessageThreadBuilder $threadBuilder = new MessageThreadBuilder,
    ) {}

    public function index(Request $request): JsonResponse
    {
        if (! Schema::hasTable('messages')) {
            return response()->json(['success' => true, 'data' => [], 'selected_thread' => null, 'messages' => []]);
        }

        $messages = Message::query()
            ->with(['sender', 'receiver', 'auction'])
            ->where(function ($query) use ($request) {
                $query->where('sender_id', $request->user()->id)
                    ->orWhere('receiver_id', $request->user()->id);
            })
            ->latest('created_at')
            ->get();

        $threads = $this->threadBuilder->build($messages, $request->user());
        $selectedThread = $threads->firstWhere('thread_key', $request->string('thread')->toString()) ?? $threads->first();
        $threadMessages = $this->threadBuilder->messagesForThread($messages, $request->user(), $selectedThread);

        if ($selectedThread) {
            Message::query()
                ->whereIn('id', $threadMessages->where('receiver_id', $request->user()->id)->where('is_read', false)->pluck('id'))
                ->update(['is_read' => true]);
        }

        return response()->json([
            'success' => true,
            'data' => $threads->values()->all(),
            'selected_thread' => $selectedThread,
            'messages' => $threadMessages->values()->all(),
        ]);
    }

    public function showThread(Request $request, string $otherUserId): JsonResponse
    {
        if (! Schema::hasTable('messages')) {
            return response()->json(['success' => true, 'data' => null, 'messages' => []]);
        }

        abort_unless(User::query()->whereKey($otherUserId)->exists(), 404);

        $auctionId = $request->string('auction_id')->toString() ?: null;
        $threadKey = $this->threadBuilder->threadKey($auctionId, $otherUserId);

        $messages = Message::query()
            ->with(['sender', 'receiver', 'auction'])
            ->where(function ($query) use ($request) {
                $query->where('sender_id', $request->user()->id)
                    ->orWhere('receiver_id', $request->user()->id);
            })
            ->latest('created_at')
            ->get();

        $threads = $this->threadBuilder->build($messages, $request->user());
        $selectedThread = $threads->firstWhere('thread_key', $threadKey);
        abort_if(! $selectedThread, 404);

        $threadMessages = $this->threadBuilder->messagesForThread($messages, $request->user(), $selectedThread);

        Message::query()
            ->whereIn('id', $threadMessages->where('receiver_id', $request->user()->id)->where('is_read', false)->pluck('id'))
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'data' => $selectedThread,
            'messages' => $threadMessages->values()->all(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'receiver_id' => ['required', 'string', Rule::exists(User::class, 'id')],
            'auction_id' => ['nullable', 'string', Rule::exists(Auction::class, 'id')],
            'content' => ['required', 'string', 'max:5000'],
        ]);

        if (! Schema::hasTable('messages')) {
            return response()->json([
                'success' => true,
                'data' => $validated,
            ], 201);
        }

        if ($validated['receiver_id'] === $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Ne možete poslati poruku samom sebi.',
                'errors' => [
                    'receiver_id' => ['Ne možete poslati poruku samom sebi.'],
                ],
            ], 422);
        }

        $message = Message::query()->create([
            'sender_id' => $request->user()->id,
            'receiver_id' => $validated['receiver_id'],
            'auction_id' => $validated['auction_id'] ?? null,
            'content' => $validated['content'],
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'data' => $message->load(['sender', 'receiver']),
        ], 201);
    }

    public function markRead(Request $request, Message $message): JsonResponse
    {
        abort_unless($message->receiver_id === $request->user()->id || $message->sender_id === $request->user()->id, 403);

        if (! $message->is_read && $message->receiver_id === $request->user()->id) {
            $message->update(['is_read' => true]);
        }

        return response()->json([
            'success' => true,
            'data' => $message->fresh(),
        ]);
    }
}
