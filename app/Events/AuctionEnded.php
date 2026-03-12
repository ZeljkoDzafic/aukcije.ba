<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Auction;
use App\Models\Bid;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AuctionEnded implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly Auction $auction,
        public readonly ?Bid $winningBid = null,
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel("auction.{$this->auction->id}");
    }

    public function broadcastAs(): string
    {
        return 'AuctionEnded';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'auction_id' => $this->auction->id,
            'status' => $this->auction->status,
            'final_price' => $this->auction->current_price,
            'ends_at' => $this->auction->ends_at,
            'winner' => $this->winningBid ? [
                'id' => $this->winningBid->user_id,
                'name' => $this->winningBid->user?->name,
            ] : null,
        ];
    }
}
