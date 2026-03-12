<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\BidPlaced;
use App\Models\User;
use App\Notifications\OutbidNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOutbidNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'notifications';

    public function handle(BidPlaced $event): void
    {
        // Determine the outbid user from the event's previousWinnerId (captured before the new bid
        // was placed). Fall back to querying the highest non-winning bid by another user.
        $previousUserId = $event->previousWinnerId;

        if (! $previousUserId || $previousUserId === $event->bid->user_id) {
            return;
        }

        $previousLeader = User::find($previousUserId);

        if (! $previousLeader) {
            return;
        }

        if (! $previousLeader->prefersEmailNotification('outbid')) {
            return;
        }

        $previousLeader->notify(
            new OutbidNotification($event->auction, $event->bid->amount)
        );
    }
}
