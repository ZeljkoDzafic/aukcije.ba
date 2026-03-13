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

    public string $bulkNote = 'Moderatorska bulk odluka nakon pregleda queue-a.';

    /** @var list<string> */
    public array $selectedAuctionIds = [];

    /** @var list<array{id: string, title: string, status: string, seller: string, latest_note?: string|null}> */
    public array $auctions = [
        ['id' => '1', 'title' => 'Rolex Datejust 36', 'status' => 'reported', 'seller' => 'Aleksa K.', 'latest_note' => 'Prijava zbog netačnog opisa.'],
        ['id' => '2', 'title' => 'iPhone 15 Pro', 'status' => 'pending review', 'seller' => 'Nika R.', 'latest_note' => 'Čeka završnu potvrdu.'],
    ];

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

    public function selectVisible(): void
    {
        $this->selectedAuctionIds = $this->visibleAuctions->pluck('id')->values()->all();
    }

    public function clearSelection(): void
    {
        $this->selectedAuctionIds = [];
    }

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
                    [
                        'title' => $model->title,
                        'note' => $this->bulkNote !== '' ? $this->bulkNote : null,
                    ]
                );

                $this->feedback = "Akcija '{$action}' izvršena za aukciju {$model->title}.";

                return;
            }
        }

        $this->feedback = $auction ? "Akcija '{$action}' pripremljena za aukciju {$auction['title']}." : '';
    }

    public function applyBulk(string $action): void
    {
        $selectedIds = $this->selectedAuctionIds !== []
            ? $this->selectedAuctionIds
            : $this->getVisibleAuctionsProperty()->pluck('id')->all();

        if ($selectedIds === []) {
            $this->feedback = 'Nema aukcija za bulk akciju.';

            return;
        }

        foreach ($selectedIds as $auctionId) {
            if ($action === 'approve-pending' && $this->filter !== 'pending review') {
                continue;
            }

            if ($action === 'cancel-expired' && $this->filter !== 'all') {
                continue;
            }

            $auction = Schema::hasTable('auctions') ? Auction::query()->find($auctionId) : null;

            if ($auction) {
                match ($action) {
                    'approve-pending' => $auction->update(['status' => 'active']),
                    'cancel-expired' => $auction->update(['status' => 'cancelled']),
                    'feature-selected' => $auction->update(['is_featured' => true]),
                    default => null,
                };

                app(AdminAuditService::class)->record(
                    Auth::id(),
                    $action,
                    'auction',
                    $auction->id,
                    [
                        'title' => $auction->title,
                        'note' => $this->bulkNote,
                    ]
                );

                continue;
            }

            $this->applyAction((int) $auctionId, $action === 'approve-pending' ? 'approve' : 'cancel');
        }

        $this->clearSelection();

        $this->feedback = match ($action) {
            'approve-pending' => 'Bulk approve je izvršen nad odabranim aukcijama na čekanju.',
            'feature-selected' => 'Bulk feature je izvršen nad odabranim aukcijama.',
            default => 'Bulk cancel je izvršen nad odabranim aukcijama.',
        };
    }

    /**
     * @return Collection<int, array{id: string, title: string, status: string, seller: string, latest_note?: string|null}>
     */
    public function getVisibleAuctionsProperty(): Collection
    {
        $auctions = collect($this->auctions);

        if (Schema::hasTable('auctions')) {
            $latestNotes = collect();

            if (Schema::hasTable('admin_logs')) {
                $latestNotes = \App\Models\AdminLog::query()
                    ->where('target_type', 'auction')
                    ->latest('created_at')
                    ->get()
                    ->groupBy('target_id')
                    ->map(fn (Collection $entries): ?string => $entries->first()?->metadata['note'] ?? null);
            }

            $databaseAuctions = Auction::query()
                ->with('seller')
                ->limit(20)
                ->get()
                ->map(fn (Auction $auction) => [
                    'id' => (string) $auction->id,
                    'title' => $auction->title,
                    'status' => strtolower((string) $auction->status),
                    'seller' => $auction->seller?->name ?? 'Nepoznato',
                    'latest_note' => $latestNotes->get($auction->id),
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
