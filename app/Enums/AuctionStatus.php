<?php

namespace App\Enums;

enum AuctionStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Finished = 'finished';
    case Sold = 'sold';
    case Cancelled = 'cancelled';

    public function canTransitionTo(self $new): bool
    {
        $transitions = [
            self::Draft => [self::Active, self::Cancelled],
            self::Active => [self::Finished, self::Sold, self::Cancelled],
            self::Finished => [self::Sold],
            self::Sold => [],
            self::Cancelled => [],
        ];

        return in_array($new, $transitions[$this] ?? []);
    }

    public function isEditable(): bool
    {
        return $this === self::Draft;
    }

    public function isActive(): bool
    {
        return $this === self::Active;
    }
}
