<?php

namespace App\Livewire\Admin;

use App\Models\Auction;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class AuctionModeration extends Component
{
    public string $filter = 'all';

    public string $feedback = '';

    public array $auctions = [
        ['id' => 1, 'title' => 'Rolex Datejust 36', 'status' => 'reported', 'seller' => 'Amar Hadžić'],
        ['id' => 2, 'title' => 'iPhone 15 Pro', 'status' => 'pending review', 'seller' => 'Lana R.'],
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

                if (Schema::hasTable('admin_logs')) {
                    \App\Models\AdminLog::query()->create([
                        'admin_id' => Auth::id(),
                        'action' => $action,
                        'target_type' => 'auction',
                        'target_id' => $model->id,
                        'metadata' => ['title' => $model->title],
                    ]);
                }

                $this->feedback = "Akcija '{$action}' izvršena za aukciju {$model->title}.";

                return;
            }
        }

        $this->feedback = $auction ? "Akcija '{$action}' pripremljena za aukciju {$auction['title']}." : '';
    }

    public function applyBulk(string $action): void
    {
        $visibleIds = collect($this->visibleAuctions)->pluck('id')->all();

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

    public function getVisibleAuctionsProperty()
    {
        $auctions = collect($this->auctions);

        if (Schema::hasTable('auctions')) {
            $databaseAuctions = Auction::query()
                ->with('seller')
                ->limit(20)
                ->get()
                ->map(fn (Auction $auction) => [
                    'id' => $auction->id,
                    'title' => $auction->title,
                    'status' => strtolower($auction->status instanceof \BackedEnum ? $auction->status->value : (string) $auction->status),
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

    public function render()
    {
        return view('livewire.admin.auction-moderation');
    }
}
