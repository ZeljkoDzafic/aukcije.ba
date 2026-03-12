<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Auction;
use App\Models\User;

class AuctionPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Auction $auction): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['seller', 'verified_seller', 'super_admin'])
            && $user->canCreateAuction();
    }

    public function update(User $user, Auction $auction): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->id === $auction->seller_id
            && in_array($auction->status, ['draft', 'active']);
    }

    public function delete(User $user, Auction $auction): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->id === $auction->seller_id && $auction->status === 'draft';
    }

    public function bid(User $user, Auction $auction): bool
    {
        return $user->id !== $auction->seller_id
            && $auction->status === 'active'
            && $user->hasVerifiedEmail();
    }

    public function moderate(User $user): bool
    {
        return $user->hasAnyRole(['moderator', 'super_admin']);
    }
}
