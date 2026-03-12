<?php

namespace App\Livewire\Admin;

use App\Models\AdminLog;
use App\Models\Auction;
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

        if (Schema::hasTable('admin_logs')) {
            AdminLog::query()->create([
                'admin_id' => Auth::id(),
                'action' => $action,
                'target_type' => 'auction',
                'target_id' => $auction->id,
                'metadata' => ['note' => $this->moderationNote],
            ]);
        }

        $this->statusMessage = "Moderation akcija '{$action}' je evidentirana za aukciju {$auction->title}.";
    }

    public function render()
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
