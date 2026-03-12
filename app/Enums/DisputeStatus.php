<?php

namespace App\Enums;

enum DisputeStatus: string
{
    case Open     = 'open';
    case InReview = 'in_review';
    case Resolved = 'resolved';
    case Closed   = 'closed';
}
