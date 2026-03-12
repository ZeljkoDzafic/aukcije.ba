<?php

namespace App\Enums;

enum UserRole: string
{
    case Buyer           = 'buyer';
    case Seller          = 'seller';
    case VerifiedSeller  = 'verified_seller';
    case Moderator       = 'moderator';
    case SuperAdmin      = 'super_admin';
}
