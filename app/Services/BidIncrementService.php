<?php

declare(strict_types=1);

namespace App\Services;

class BidIncrementService
{
    public function getIncrement(float $currentPrice): float
    {
        $increments = config('auction.bid_increments');
        foreach ($increments as $maxPrice => $increment) {
            if ($maxPrice === null || $currentPrice < $maxPrice) {
                return (float) $increment;
            }
        }

        return 50.00; // fallback
    }

    public function getMinimumBid(float $currentPrice): float
    {
        return round($currentPrice + $this->getIncrement($currentPrice), 2);
    }
}
