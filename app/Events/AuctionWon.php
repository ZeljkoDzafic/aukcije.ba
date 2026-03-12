<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Auction;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AuctionWon
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly User $winner,
        public readonly Auction $auction,
        public readonly float $amount,
    ) {}
}
