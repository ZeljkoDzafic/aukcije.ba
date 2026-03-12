<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AuctionStatus;
use App\Models\Auction;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * T-1302: Bulk operations on auctions for sellers.
 * Each operation runs inline (small N); for large N dispatch via queue.
 */
class BulkAuctionService
{
    public function __construct(private readonly AuctionService $auctionService) {}

    /**
     * Publish a list of draft auctions.
     *
     * @param string[] $auctionIds
     * @return array{published: int, skipped: int, errors: string[]}
     */
    public function publishDrafts(User $seller, array $auctionIds): array
    {
        $result = ['published' => 0, 'skipped' => 0, 'errors' => []];

        $auctions = Auction::query()
            ->where('seller_id', $seller->id)
            ->whereIn('id', $auctionIds)
            ->where('status', AuctionStatus::Draft->value)
            ->get();

        foreach ($auctions as $auction) {
            try {
                $this->auctionService->publishAuction($auction);
                $result['published']++;
            } catch (\Throwable $e) {
                $result['errors'][] = "Auction {$auction->id}: {$e->getMessage()}";
                Log::warning('Bulk publish failed', ['auction_id' => $auction->id, 'error' => $e->getMessage()]);
            }
        }

        $result['skipped'] = count($auctionIds) - count($auctions) - count($result['errors']);

        return $result;
    }

    /**
     * End a list of active auctions immediately.
     *
     * @param string[] $auctionIds
     * @return array{ended: int, skipped: int, errors: string[]}
     */
    public function endActive(User $seller, array $auctionIds): array
    {
        $result = ['ended' => 0, 'skipped' => 0, 'errors' => []];

        $auctions = Auction::query()
            ->where('seller_id', $seller->id)
            ->whereIn('id', $auctionIds)
            ->where('status', AuctionStatus::Active->value)
            ->get();

        foreach ($auctions as $auction) {
            try {
                $this->auctionService->endAuction($auction);
                $result['ended']++;
            } catch (\Throwable $e) {
                $result['errors'][] = "Auction {$auction->id}: {$e->getMessage()}";
            }
        }

        $result['skipped'] = count($auctionIds) - count($auctions) - count($result['errors']);

        return $result;
    }

    /**
     * Clone a list of auctions back to draft.
     *
     * @param string[] $auctionIds
     * @return array{cloned: int, errors: string[]}
     */
    public function cloneAuctions(User $seller, array $auctionIds): array
    {
        $result = ['cloned' => 0, 'errors' => []];

        Auction::query()
            ->where('seller_id', $seller->id)
            ->whereIn('id', $auctionIds)
            ->each(function (Auction $auction) use ($seller, &$result) {
                try {
                    $data = collect($auction->toArray())
                        ->except(['id', 'status', 'ends_at', 'winner_id', 'ended_at', 'bids_count', 'watchers_count', 'current_price', 'last_bid_at'])
                        ->merge([
                            'start_price'   => $auction->start_price,
                            'duration_days' => 7,
                        ])
                        ->toArray();

                    $this->auctionService->createAuction($seller, $data);
                    $result['cloned']++;
                } catch (\Throwable $e) {
                    $result['errors'][] = "Auction {$auction->id}: {$e->getMessage()}";
                }
            });

        return $result;
    }
}
