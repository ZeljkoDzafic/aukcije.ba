<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class AuctionNotActiveException extends RuntimeException
{
    public function __construct(string $status = '')
    {
        $msg = 'Aukcija nije aktivna.';
        if ($status) {
            $msg .= " Status: {$status}";
        }
        parent::__construct($msg);
    }
}
