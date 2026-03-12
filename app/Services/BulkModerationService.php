<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AuctionStatus;
use App\Models\AdminLog;
use App\Models\Auction;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

/**
 * T-1500: Bulk auction moderation for admins.
 * Handles approve/reject with audit trail and cache invalidation.
 */
class BulkModerationService
{
    /**
     * Approve a batch of pending (draft) auctions.
     *
     * @param string[] $auctionIds
     * @return array{approved: int, skipped: int}
     */
    public function approve(User $admin, array $auctionIds, ?string $note = null): array
    {
        $approved = 0;

        $auctions = Auction::query()
            ->whereIn('id', $auctionIds)
            ->where('status', AuctionStatus::Draft->value)
            ->get();

        foreach ($auctions as $auction) {
            $auction->update(['status' => AuctionStatus::Active->value]);
            $approved++;
        }

        if ($approved > 0) {
            AdminLog::create([
                'admin_id'    => $admin->id,
                'action'      => 'bulk_approve_auctions',
                'target_type' => 'auction',
                'target_id'   => null,
                'metadata'    => [
                    'auction_ids' => $auctionIds,
                    'approved'    => $approved,
                    'note'        => $note,
                ],
            ]);

            Cache::forget('categories:tree');
        }

        return ['approved' => $approved, 'skipped' => count($auctionIds) - $approved];
    }

    /**
     * Reject a batch of auctions (move to cancelled).
     *
     * @param string[] $auctionIds
     * @return array{rejected: int, skipped: int}
     */
    public function reject(User $admin, array $auctionIds, string $reason): array
    {
        $rejected = 0;

        $auctions = Auction::query()
            ->whereIn('id', $auctionIds)
            ->where('status', AuctionStatus::Draft->value)
            ->get();

        foreach ($auctions as $auction) {
            $auction->update(['status' => AuctionStatus::Cancelled->value]);
            $rejected++;
        }

        if ($rejected > 0) {
            AdminLog::create([
                'admin_id'    => $admin->id,
                'action'      => 'bulk_reject_auctions',
                'target_type' => 'auction',
                'target_id'   => null,
                'metadata'    => [
                    'auction_ids' => $auctionIds,
                    'rejected'    => $rejected,
                    'reason'      => $reason,
                ],
            ]);
        }

        return ['rejected' => $rejected, 'skipped' => count($auctionIds) - $rejected];
    }

    /**
     * Feature or un-feature a batch of auctions.
     *
     * @param string[] $auctionIds
     */
    public function setFeatured(User $admin, array $auctionIds, bool $featured): int
    {
        $updated = Auction::query()
            ->whereIn('id', $auctionIds)
            ->update(['is_featured' => $featured]);

        AdminLog::create([
            'admin_id'    => $admin->id,
            'action'      => $featured ? 'bulk_feature_auctions' : 'bulk_unfeature_auctions',
            'target_type' => 'auction',
            'target_id'   => null,
            'metadata'    => ['auction_ids' => $auctionIds, 'count' => $updated],
        ]);

        Cache::forget('homepage:featured');

        return $updated;
    }
}
