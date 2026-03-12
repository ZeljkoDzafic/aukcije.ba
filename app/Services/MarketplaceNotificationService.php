<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuctionNotification;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class MarketplaceNotificationService
{
    /**
     * @param array<string, mixed> $data
     */
    public function notify(User $user, string $type, ?string $title = null, ?string $body = null, array $data = []): ?AuctionNotification
    {
        if (! Schema::hasTable('notifications_custom')) {
            return null;
        }

        return AuctionNotification::query()->create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'data' => $data,
        ]);
    }

    public function markAllAsRead(User $user): void
    {
        if (! Schema::hasTable('notifications_custom')) {
            return;
        }

        $user->marketplaceNotifications()->whereNull('read_at')->update(['read_at' => now()]);
    }

    public function markAsRead(User $user, string $notificationId): void
    {
        if (! Schema::hasTable('notifications_custom')) {
            return;
        }

        $user->marketplaceNotifications()->whereKey($notificationId)->update(['read_at' => now()]);
    }

    /**
     * @return Collection<int, AuctionNotification>
     */
    public function latestForUser(User $user, int $limit = 20): Collection
    {
        if (! Schema::hasTable('notifications_custom')) {
            return collect();
        }

        return $user->marketplaceNotifications()
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }
}
