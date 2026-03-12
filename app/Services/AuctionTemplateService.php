<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Auction;
use App\Models\AuctionTemplate;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * T-1301: Auction templates — save an auction as a reusable template.
 */
class AuctionTemplateService
{
    private const TEMPLATE_FIELDS = [
        'title', 'description', 'category_id', 'start_price', 'buy_now_price',
        'reserve_price', 'type', 'condition', 'auto_extension', 'extension_minutes',
        'shipping_methods', 'is_reserve_public',
    ];

    /**
     * Create a template from an existing auction or a raw data array.
     *
     * @param array<string, mixed>|null $data If null, uses the auction's own fields.
     */
    public function createFromAuction(User $seller, Auction $auction, string $name, ?array $data = null): AuctionTemplate
    {
        $payload = collect($data ?? $auction->toArray())
            ->only(self::TEMPLATE_FIELDS)
            ->toArray();

        return AuctionTemplate::create([
            'seller_id' => $seller->id,
            'name'      => $name,
            'data'      => $payload,
        ]);
    }

    /**
     * Create a new auction from a template.
     *
     * @param array<string, mixed> $overrides Fields to override from the template.
     */
    public function createAuction(AuctionTemplate $template, array $overrides, AuctionService $auctionService): Auction
    {
        $data = array_merge($template->data, $overrides);

        $seller = $template->seller;

        return $auctionService->createAuction($seller, $data);
    }

    /**
     * @return Collection<int, AuctionTemplate>
     */
    public function forSeller(User $seller): Collection
    {
        return AuctionTemplate::query()
            ->where('seller_id', $seller->id)
            ->orderByDesc('created_at')
            ->get();
    }

    public function delete(AuctionTemplate $template): void
    {
        $template->delete();
    }
}
