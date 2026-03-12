<?php

namespace App\Exceptions;

use RuntimeException;

class WalletFrozenException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Vaš novčanik je zamrznut. Kontaktirajte podršku.');
    }
}
