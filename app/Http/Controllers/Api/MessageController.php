<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class MessageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        if (! Schema::hasTable('messages')) {
            return response()->json(['success' => true, 'data' => []]);
        }

        // TODO: Replace flat list with conversation threads grouped by (other_party, auction_id).
        //       Each thread: other_user{id,name,avatar}, auction{id,title}, last_message,
        //       unread_count, last_message_at. Paginate (20 threads/page).
        // TODO: Add GET /user/messages/{otherUserId}?auction_id= to load a single thread.
        // TODO: Add POST /user/messages/{message}/read to mark a thread read.
        // TODO: Broadcast new messages via private user channel so inbox updates in real-time.
        $messages = Message::query()
            ->with(['sender', 'receiver', 'auction'])
            ->where(function ($query) use ($request) {
                $query->where('sender_id', $request->user()->id)
                    ->orWhere('receiver_id', $request->user()->id);
            })
            ->latest('created_at')
            ->paginate(50);

        return response()->json(['success' => true, 'data' => $messages->items()]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'receiver_id' => ['required', 'string'],
            'auction_id' => ['nullable', 'string'],
            'content' => ['required', 'string', 'max:5000'],
        ]);

        if (! Schema::hasTable('messages')) {
            return response()->json([
                'success' => true,
                'data' => $validated,
            ], 201);
        }

        // TODO: Validate that receiver_id belongs to a real user (exists:users,id).
        // TODO: Prevent users from messaging themselves.
        // TODO: Fire a NewMessageEvent that broadcasts to the receiver's private channel
        //       so the inbox badge updates without a page refresh.
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
}
