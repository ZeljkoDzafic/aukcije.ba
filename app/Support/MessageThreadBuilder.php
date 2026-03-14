<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Collection;

class MessageThreadBuilder
{
    /**
     * @param Collection<int, Message> $messages
     * @return Collection<int, array{
     *     thread_key: string,
     *     other_user_id: ?string,
     *     contact: string,
     *     auction_id: ?string,
     *     auction_title: string,
     *     auction_image: ?string,
     *     last_message: string,
     *     last_message_at: string,
     *     unread_count: int,
     *     message_count: int,
     *     action_required: bool
     * }>
     */
    public function build(Collection $messages, User $user): Collection
    {
        return $messages
            ->groupBy(fn (Message $message): string => $this->threadKeyForMessage($message, $user))
            ->map(function (Collection $conversation, string $threadKey) use ($user): array {
                /** @var Message $latest */
                $latest = $conversation->sortByDesc('created_at')->first();
                $otherParticipant = $latest->sender_id === $user->id ? $latest->receiver : $latest->sender;
                $unreadCount = $conversation
                    ->where('receiver_id', $user->id)
                    ->where('is_read', false)
                    ->count();

                return [
                    'thread_key' => $threadKey,
                    'other_user_id' => $otherParticipant?->id,
                    'contact' => $otherParticipant?->name ?? $otherParticipant?->email ?? 'Marketplace korisnik',
                    'auction_id' => $latest->auction?->id,
                    'auction_title' => $latest->auction?->title ?? 'Opća komunikacija',
                    'auction_image' => $latest->auction?->primaryImage?->url,
                    'last_message' => $latest->content,
                    'last_message_at' => $latest->created_at?->diffForHumans() ?? 'upravo sada',
                    'last_message_at_raw' => $latest->created_at?->toISOString() ?? '',
                    'unread_count' => $unreadCount,
                    'message_count' => $conversation->count(),
                    'action_required' => $unreadCount > 0 && $latest->sender_id !== $user->id,
                ];
            })
            ->sortByDesc(fn (array $thread): string => $thread['last_message_at_raw'])
            ->map(function (array $thread): array {
                unset($thread['last_message_at_raw']);

                return $thread;
            })
            ->values();
    }

    /**
     * @param Collection<int, Message> $messages
     * @return Collection<int, Message>
     */
    public function messagesForThread(Collection $messages, User $user, ?array $selectedThread): Collection
    {
        if (! $selectedThread) {
            return collect();
        }

        return $messages
            ->filter(function (Message $message) use ($selectedThread, $user): bool {
                $otherParticipantId = $message->sender_id === $user->id ? $message->receiver_id : $message->sender_id;

                return (string) ($message->auction_id ?? '') === (string) ($selectedThread['auction_id'] ?? '')
                    && (string) $otherParticipantId === (string) ($selectedThread['other_user_id'] ?? '');
            })
            ->sortBy('created_at')
            ->values();
    }

    public function threadKey(string|null $auctionId, string|null $otherUserId): string
    {
        return implode(':', [
            $auctionId ?: 'general',
            $otherUserId ?: 'guest',
        ]);
    }

    private function threadKeyForMessage(Message $message, User $user): string
    {
        $otherParticipantId = $message->sender_id === $user->id
            ? ($message->receiver_id ?? 'guest')
            : ($message->sender_id ?? 'guest');

        return $this->threadKey($message->auction_id, $otherParticipantId);
    }
}
