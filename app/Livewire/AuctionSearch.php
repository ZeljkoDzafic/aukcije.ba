<?php

namespace App\Livewire;

use App\Models\Auction;
use App\Models\Category;
use Livewire\Component;
use Illuminate\Support\Facades\Schema;

class AuctionSearch extends Component
{
    public string $query = '';

    public string $category = '';

    public string $location = '';

    public string $sort = 'ending_soon';

    public array $categoryOptions = [];

    public array $auctions = [
        ['id' => 'demo-1', 'title' => 'Samsung Galaxy S24 Ultra', 'category' => 'Elektronika', 'price' => 1250.00, 'bids' => 14, 'watchers' => 32, 'location' => 'Sarajevo', 'time' => '2d 04h'],
        ['id' => 'demo-2', 'title' => 'Sony WH-1000XM5', 'category' => 'Audio', 'price' => 480.00, 'bids' => 11, 'watchers' => 17, 'location' => 'Banja Luka', 'time' => '5h 12m'],
        ['id' => 'demo-3', 'title' => 'Vintage Leica M3', 'category' => 'Foto oprema', 'price' => 2750.00, 'bids' => 19, 'watchers' => 44, 'location' => 'Tuzla', 'time' => '1d 18h'],
    ];

    public function render()
    {
        $results = collect($this->auctions);

        if (Schema::hasTable('categories')) {
            $this->categoryOptions = ['' => 'Sve kategorije'] + Category::query()
                ->orderBy('name')
                ->limit(20)
                ->pluck('name', 'slug')
                ->all();
        }

        if (Schema::hasTable('auctions')) {
            $query = Auction::query()
                ->with('category')
                ->where('status', 'active')
                ->when($this->query !== '', fn ($query) => $query->where('title', 'like', '%'.$this->query.'%'))
                ->when($this->category !== '', fn ($query) => $query->whereHas('category', fn ($category) => $category->where('slug', $this->category)->orWhere('name', 'like', '%'.$this->category.'%')))
                ->when(
                    $this->location !== '',
                    fn ($query) => $query->where(function ($inner) {
                        $inner->when(Schema::hasColumn('auctions', 'location_city'), fn ($q) => $q->orWhere('location_city', 'like', '%'.$this->location.'%'))
                            ->when(Schema::hasColumn('auctions', 'location'), fn ($q) => $q->orWhere('location', 'like', '%'.$this->location.'%'));
                    })
                );

            match ($this->sort) {
                'newest' => $query->latest(),
                'most_bids' => $query->orderByDesc('bids_count'),
                'price_low' => $query->orderBy('current_price'),
                'price_high' => $query->orderByDesc('current_price'),
                default => $query->orderBy('ends_at'),
            };

            $databaseResults = $query
                ->limit(12)
                ->get()
                ->map(fn (Auction $auction) => [
                    'id' => $auction->id,
                    'title' => $auction->title,
                    'category' => $auction->category?->name ?? 'Bez kategorije',
                    'price' => (float) $auction->current_price,
                    'bids' => $auction->bids_count,
                    'watchers' => $auction->watchers_count,
                    'location' => $auction->location_city ?? $auction->location ?? 'Nepoznato',
                    'time' => $auction->time_remaining,
                ]);

            if ($databaseResults->isNotEmpty()) {
                $results = $databaseResults;
            }
        }

        return view('livewire.auction-search', [
            'results' => $results->filter(function (array $auction) {
                return ($this->query === '' || str_contains(strtolower($auction['title']), strtolower($this->query)))
                    && ($this->category === '' || str_contains(strtolower($auction['category']), strtolower(str_replace('_', ' ', $this->category))) || $this->category === str($auction['category'])->slug()->value())
                    && ($this->location === '' || str_contains(strtolower($auction['location']), strtolower($this->location)));
            })->values(),
        ]);
    }
}
