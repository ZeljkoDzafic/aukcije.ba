<?php

declare(strict_types=1);

namespace App\Enums;

enum AuctionType: string
{
    case Standard = 'standard';
    case BuyNow = 'buy_now';
    case Dutch = 'dutch';

    public function label(): string
    {
        return match ($this) {
            self::Standard => 'Standardna aukcija',
            self::BuyNow => 'Kupi odmah',
            self::Dutch => 'Holandska aukcija',
        };
    }
}
