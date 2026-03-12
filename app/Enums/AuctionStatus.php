<?php

declare(strict_types=1);

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
            self::Draft->value => [self::Active, self::Cancelled],
            self::Active->value => [self::Finished, self::Sold, self::Cancelled],
            self::Finished->value => [self::Sold],
            self::Sold->value => [],
            self::Cancelled->value => [],
        ];

        return in_array($new, $transitions[$this->value], true);
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
