<?php

declare(strict_types=1);

namespace App\Enums;

enum AuctionStatus: string
{
    case Draft = 'draft';
    case Scheduled = 'scheduled'; // T-1303: future start time set
    case Active = 'active';
    case Finished = 'finished';
    case Sold = 'sold';
    case Cancelled = 'cancelled';

    public function canTransitionTo(self $new): bool
    {
        $transitions = [
            self::Draft->value      => [self::Active, self::Scheduled, self::Cancelled],
            self::Scheduled->value  => [self::Active, self::Cancelled],
            self::Active->value     => [self::Finished, self::Sold, self::Cancelled],
            self::Finished->value   => [self::Sold],
            self::Sold->value       => [],
            self::Cancelled->value  => [],
        ];

        return in_array($new, $transitions[$this->value], true);
    }

    public function isEditable(): bool
    {
        return in_array($this, [self::Draft, self::Scheduled], true);
    }

    public function isActive(): bool
    {
        return $this === self::Active;
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Sold, self::Cancelled], true);
    }
}
