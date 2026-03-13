<?php

declare(strict_types=1);

namespace App\Livewire\Seller;

use App\Models\Auction;
use App\Services\BulkAuctionService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class BulkOperations extends Component
{
    /** @var list<string> */
    public array $selectedAuctionIds = [];

    public string $statusMessage = '';

    public function toggleSelection(string $auctionId): void
    {
        if (in_array($auctionId, $this->selectedAuctionIds, true)) {
            $this->selectedAuctionIds = array_values(array_filter(
                $this->selectedAuctionIds,
                fn (string $selectedId): bool => $selectedId !== $auctionId
            ));

            return;
        }

        $this->selectedAuctionIds[] = $auctionId;
    }

    public function selectAll(): void
    {
        $seller = Auth::user();
        $this->selectedAuctionIds = $seller && Schema::hasTable('auctions')
            ? $seller->auctions()->latest()->limit(25)->pluck('id')->all()
            : [];
    }

    public function clearSelection(): void
    {
        $this->selectedAuctionIds = [];
    }

    public function apply(string $action): void
    {
        $seller = Auth::user();
        if (! $seller || $this->selectedAuctionIds === []) {
            $this->statusMessage = 'Označi barem jednu aukciju.';

            return;
        }

        $service = app(BulkAuctionService::class);

        $result = match ($action) {
            'publish' => $service->publishDrafts($seller, $this->selectedAuctionIds),
            'end' => $service->endActive($seller, $this->selectedAuctionIds),
            'clone' => $service->cloneAuctions($seller, $this->selectedAuctionIds),
            default => ['errors' => ['Nepoznata akcija.']],
        };

        $this->statusMessage = match ($action) {
            'publish' => 'Bulk objava završena: '.(($result['published'] ?? 0)).' aukcija.',
            'end' => 'Bulk završavanje završeno: '.(($result['ended'] ?? 0)).' aukcija.',
            'clone' => 'Bulk kloniranje završeno: '.(($result['cloned'] ?? 0)).' aukcija.',
            default => 'Akcija nije izvršena.',
        };

        $this->clearSelection();
    }

    public function render(): View
    {
        $seller = Auth::user();

        return view('livewire.seller.bulk-operations', [
            'auctions' => $seller && Schema::hasTable('auctions')
                ? Auction::query()->where('seller_id', $seller->id)->latest()->limit(25)->get()
                : collect(),
        ]);
    }
}
