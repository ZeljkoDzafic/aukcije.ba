<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class WalletFrozenException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Wallet is frozen');
    }
}
