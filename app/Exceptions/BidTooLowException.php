<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class BidTooLowException extends RuntimeException
{
    public function __construct(float $minimum, float $given)
    {
        parent::__construct(
            "Bid iznos {$given} BAM je premali. Minimalni bid je {$minimum} BAM."
        );
    }
}
