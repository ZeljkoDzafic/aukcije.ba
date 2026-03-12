<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Auction;
use App\Services\AdminAuditService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class AuctionDetailManager extends Component
{
    public ?string $auctionId = null;

    public string $moderationNote = 'Potrebno zatražiti dodatne fotografije serijskog broja.';

    public string $statusMessage = '';

    public function moderate(string $action): void
    {
        $auction = $this->getAuction();

        if (! $auction) {
            return;
        }

        match ($action) {
            'approve' => $auction->update(['status' => 'active']),
            'feature' => $auction->update(['is_featured' => ! $auction->is_featured]),
            'cancel' => $auction->update(['status' => 'cancelled']),
            default => null,
        };

        app(AdminAuditService::class)->record(
            Auth::id(),
            $action,
            'auction',
            $auction->id,
            ['note' => $this->moderationNote]
        );

        $this->statusMessage = "Moderation akcija '{$action}' je evidentirana za aukciju {$auction->title}.";
    }

    public function render(): View
    {
        return view('livewire.admin.auction-detail-manager', [
            'auction' => $this->getAuction(),
        ]);
    }

    protected function getAuction(): ?Auction
    {
        if (! $this->auctionId || ! Schema::hasTable('auctions')) {
            return null;
        }

        return Auction::query()->with(['seller', 'category', 'bids.user'])->find($this->auctionId);
    }
}
