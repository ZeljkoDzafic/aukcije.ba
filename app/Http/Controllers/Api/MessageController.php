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

        $messages = Message::query()
            ->with(['sender', 'receiver', 'auction'])
            ->where(function ($query) use ($request) {
                $query->where('sender_id', $request->user()->id)
                    ->orWhere('receiver_id', $request->user()->id);
            })
            ->latest('created_at')
            ->get();

        return response()->json(['success' => true, 'data' => $messages]);
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
