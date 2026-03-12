<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Auction;
use Carbon\Carbon;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AuctionExtended implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly Auction $auction,
        public readonly \DateTimeInterface $newEndsAt,
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel("auction.{$this->auction->id}");
    }

    public function broadcastAs(): string
    {
        return 'AuctionExtended';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'auction_id' => $this->auction->id,
            'new_ends_at' => $this->newEndsAt instanceof Carbon
                ? $this->newEndsAt->toIso8601String()
                : (new Carbon($this->newEndsAt))->toIso8601String(),
            'extension_minutes' => config('auction.extension_minutes', 3),
        ];
    }
}
