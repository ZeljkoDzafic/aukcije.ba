<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Auction;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Watchlist extends Component
{
    use WithPagination;

    #[Url]
    public string $filter = 'active';

    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    public function remove(string $auctionId): void
    {
        if (Auth::check() && Schema::hasTable('auction_watchers')) {
            Auth::user()->watchlist()->detach($auctionId);
        }
    }

    public function render(): View
    {
        if (Auth::check() && Schema::hasTable('auction_watchers') && Schema::hasTable('auctions')) {
            $query = Auth::user()
                ->watchlist()
                ->with(['category', 'primaryImage']);

            if ($this->filter !== 'all') {
                $query->when($this->filter === 'active', fn ($q) => $q->where('status', 'active')->where('ends_at', '>', now()));
                $query->when($this->filter === 'ended', fn ($q) => $q->where(fn ($q) => $q->where('status', '!=', 'active')->orWhere('ends_at', '<=', now())));
            }

            $results = $query->paginate(20);

            return view('livewire.watchlist', [
                'results' => $results->through(function (Auction $auction) {
                    return [
                        'id'        => $auction->id,
                        'title'     => $auction->title,
                        'category'  => $auction->category?->name ?? 'Bez kategorije',
                        'price'     => (float) $auction->current_price,
                        'bids'      => $auction->bids_count,
                        'watchers'  => $auction->watchers_count,
                        'location'  => $auction->location_city ?? $auction->location ?? 'Nepoznato',
                        'time'      => $auction->time_remaining,
                        'state'     => $auction->ends_at && $auction->ends_at->isPast() ? 'ended' : 'active',
                        'image_url' => $auction->primaryImage?->url,
                        'badge'     => $auction->ends_at && $auction->ends_at->diffInHours(now(), false) <= 24 ? 'Uskoro završava' : 'Praćena aukcija',
                    ];
                }),
            ]);
        }

        // Demo data for unauthenticated or pre-migration state
        $demoItems = collect([
            ['id' => 'demo-1', 'title' => 'Rolex Datejust 36', 'category' => 'Satovi', 'price' => 5850.00, 'bids' => 31, 'watchers' => 74, 'location' => 'Sarajevo', 'time' => '6h 42m', 'state' => 'active', 'badge' => 'Uskoro završava'],
            ['id' => 'demo-2', 'title' => 'Sony A7 IV', 'category' => 'Foto oprema', 'price' => 2310.00, 'bids' => 14, 'watchers' => 19, 'location' => 'Tuzla', 'time' => '1d 03h', 'state' => 'active', 'badge' => 'Praćena aukcija'],
            ['id' => 'demo-3', 'title' => 'Vinyl kolekcija EX/YU', 'category' => 'Kolekcionarstvo', 'price' => 320.00, 'bids' => 12, 'watchers' => 22, 'location' => 'Mostar', 'time' => 'Završeno', 'state' => 'ended', 'badge' => 'Praćena aukcija'],
        ]);

        $filtered = $this->filter !== 'all'
            ? $demoItems->where('state', $this->filter)->values()
            : $demoItems;

        return view('livewire.watchlist', ['results' => $filtered]);
    }
}
