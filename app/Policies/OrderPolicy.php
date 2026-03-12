<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function view(User $user, Order $order): bool
    {
        return $user->id === $order->buyer_id
            || $user->id === $order->seller_id
            || $user->hasAnyRole(['moderator', 'super_admin']);
    }

    public function ship(User $user, Order $order): bool
    {
        return $user->id === $order->seller_id
            && $order->status === 'payment_received';
    }

    public function dispute(User $user, Order $order): bool
    {
        return ($user->id === $order->buyer_id || $user->id === $order->seller_id)
            && in_array($order->status, ['payment_received', 'shipped', 'delivered']);
    }
}
