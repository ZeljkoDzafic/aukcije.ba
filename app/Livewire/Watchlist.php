<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Auction;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class Watchlist extends Component
{
    public string $filter = 'active';

    /** @var list<array<string, mixed>> */
    public array $items = [
        ['id' => 'demo-1', 'title' => 'Rolex Datejust 36', 'category' => 'Satovi', 'price' => 5850.00, 'bids' => 31, 'watchers' => 74, 'location' => 'Sarajevo', 'time' => '6h 42m', 'state' => 'active'],
        ['id' => 'demo-2', 'title' => 'Sony A7 IV', 'category' => 'Foto oprema', 'price' => 2310.00, 'bids' => 14, 'watchers' => 19, 'location' => 'Tuzla', 'time' => '1d 03h', 'state' => 'active'],
        ['id' => 'demo-3', 'title' => 'Vinyl kolekcija EX/YU', 'category' => 'Kolekcionarstvo', 'price' => 320.00, 'bids' => 12, 'watchers' => 22, 'location' => 'Mostar', 'time' => 'Završeno', 'state' => 'ended'],
    ];

    public function remove(string $auctionId): void
    {
        if (Auth::check() && Schema::hasTable('auction_watchers')) {
            Auth::user()->watchlist()->detach($auctionId);
        }

        $this->items = array_values(array_filter($this->items, fn (array $item) => (string) $item['id'] !== (string) $auctionId));
    }

    public function render(): View
    {
        $results = collect($this->items);

        if (Auth::check() && Schema::hasTable('auction_watchers') && Schema::hasTable('auctions')) {
            $databaseResults = Auth::user()
                ->watchlist()
                ->with('category')
                ->limit(12)
                ->get()
                ->map(function (Auction $auction) {
                    return [
                        'id' => $auction->id,
                        'title' => $auction->title,
                        'category' => $auction->category?->name ?? 'Bez kategorije',
                        'price' => (float) $auction->current_price,
                        'bids' => $auction->bids_count,
                        'watchers' => $auction->watchers_count,
                        'location' => $auction->location_city ?? $auction->location ?? 'Nepoznato',
                        'time' => $auction->time_remaining,
                        'state' => $auction->ends_at && $auction->ends_at->isPast() ? 'ended' : 'active',
                    ];
                });

            if ($databaseResults->isNotEmpty()) {
                $results = $databaseResults;
            }
        }

        return view('livewire.watchlist', [
            'results' => $results
                ->when($this->filter !== 'all', fn ($items) => $items->where('state', $this->filter))
                ->values(),
        ]);
    }
}
