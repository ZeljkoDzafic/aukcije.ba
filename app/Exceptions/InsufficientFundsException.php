<?php

namespace App\Exceptions;

use RuntimeException;

class InsufficientFundsException extends RuntimeException
{
    public function __construct(float $required, float $available)
    {
        parent::__construct(
            "Nedovoljno sredstava. Potrebno: {$required} BAM, dostupno: {$available} BAM."
        );
    }
}
