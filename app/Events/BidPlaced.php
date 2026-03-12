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

class BidPlaced implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly Auction $auction,
        public readonly Bid $bid,
        public readonly ?string $previousWinnerId = null,
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel("auction.{$this->auction->id}");
    }

    public function broadcastAs(): string
    {
        return 'BidPlaced';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'auction_id' => $this->auction->id,
            'current_price' => $this->auction->current_price,
            'bids_count' => $this->auction->bids_count,
            'bid' => [
                'id' => $this->bid->id,
                'amount' => $this->bid->amount,
                'is_proxy' => $this->bid->is_proxy,
                'created_at' => $this->bid->created_at,
            ],
            'bidder' => [
                'id' => $this->bid->user_id,
                'name' => $this->bid->user?->name,
            ],
        ];
    }
}
