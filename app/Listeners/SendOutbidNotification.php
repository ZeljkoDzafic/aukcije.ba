<?php

namespace App\Listeners;

use App\Events\BidPlaced;
use App\Notifications\OutbidNotification;
use Illuminate\Support\Facades\Schema;

class SendOutbidNotification
{
    public function handle(BidPlaced $event): void
    {
        $previousLeader = $event->auction->bids()
            ->where('user_id', '!=', $event->bid->user_id)
            ->orderByDesc('amount')
            ->with('user')
            ->first();

        if ($previousLeader && $previousLeader->user && Schema::hasTable('notifications')) {
            $previousLeader->user->notify(
                new OutbidNotification($event->auction, $event->bid->amount)
            );
        }
    }
}
