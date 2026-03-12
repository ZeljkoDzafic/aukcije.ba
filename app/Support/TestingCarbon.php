<?php

namespace App\Support;

use Illuminate\Support\Carbon;

class TestingCarbon extends Carbon
{
    public function diffInMinutes($date = null, bool $absolute = false): float
    {
        return parent::diffInMinutes($date, true);
    }
}
