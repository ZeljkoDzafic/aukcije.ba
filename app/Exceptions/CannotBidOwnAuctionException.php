<?php

namespace App\Exceptions;

use RuntimeException;

class CannotBidOwnAuctionException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Ne možete licitirati na vašoj vlastitoj aukciji.');
    }
}
