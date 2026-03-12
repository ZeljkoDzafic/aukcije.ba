<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Auction;
use App\Services\AdminAuditService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class AuctionModeration extends Component
{
    public string $filter = 'all';

    public string $feedback = '';

    /** @var list<array{id: string, title: string, status: string, seller: string}> */
    public array $auctions = [
        ['id' => '1', 'title' => 'Rolex Datejust 36', 'status' => 'reported', 'seller' => 'Amar Hadžić'],
        ['id' => '2', 'title' => 'iPhone 15 Pro', 'status' => 'pending review', 'seller' => 'Lana R.'],
    ];

    public function applyAction(int $auctionId, string $action): void
    {
        $auction = collect($this->auctions)->firstWhere('id', $auctionId);

        if (Schema::hasTable('auctions')) {
            $model = Auction::query()->find($auctionId);

            if ($model) {
                match ($action) {
                    'approve' => $model->update(['status' => 'active']),
                    'cancel' => $model->update(['status' => 'cancelled']),
                    'feature' => $model->update(['is_featured' => ! $model->is_featured]),
                    default => null,
                };

                app(AdminAuditService::class)->record(
                    Auth::id(),
                    $action,
                    'auction',
                    $model->id,
                    ['title' => $model->title]
                );

                $this->feedback = "Akcija '{$action}' izvršena za aukciju {$model->title}.";

                return;
            }
        }

        $this->feedback = $auction ? "Akcija '{$action}' pripremljena za aukciju {$auction['title']}." : '';
    }

    public function applyBulk(string $action): void
    {
        $visibleIds = $this->getVisibleAuctionsProperty()->pluck('id')->all();

        if ($visibleIds === []) {
            $this->feedback = 'Nema aukcija za bulk akciju.';

            return;
        }

        foreach ($visibleIds as $auctionId) {
            if ($action === 'approve-pending' && $this->filter !== 'pending review') {
                continue;
            }

            if ($action === 'cancel-expired' && $this->filter !== 'all') {
                continue;
            }

            $this->applyAction((int) $auctionId, $action === 'approve-pending' ? 'approve' : 'cancel');
        }

        $this->feedback = $action === 'approve-pending'
            ? 'Bulk approve je izvršen nad aukcijama na čekanju.'
            : 'Bulk cancel je izvršen nad vidljivim aukcijama.';
    }

    /**
     * @return Collection<int, array{id: string, title: string, status: string, seller: string}>
     */
    public function getVisibleAuctionsProperty(): Collection
    {
        $auctions = collect($this->auctions);

        if (Schema::hasTable('auctions')) {
            $databaseAuctions = Auction::query()
                ->with('seller')
                ->limit(20)
                ->get()
                ->map(fn (Auction $auction) => [
                    'id' => (string) $auction->id,
                    'title' => $auction->title,
                    'status' => strtolower((string) $auction->status),
                    'seller' => $auction->seller?->name ?? 'Nepoznato',
                ]);

            if ($databaseAuctions->isNotEmpty()) {
                $auctions = $databaseAuctions;
            }
        }

        return $auctions
            ->when($this->filter !== 'all', fn ($items) => $items->where('status', $this->filter))
            ->values();
    }

    public function render(): View
    {
        return view('livewire.admin.auction-moderation');
    }
}
