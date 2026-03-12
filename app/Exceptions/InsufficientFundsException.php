<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class InsufficientFundsException extends RuntimeException
{
    public function __construct(float $required, float $available)
    {
        parent::__construct(
            "Insufficient balance. Required: {$required} BAM, available: {$available} BAM."
        );
    }
}
