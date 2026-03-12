<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Auction;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AuctionSearch extends Component
{
    use WithPagination;

    #[Url]
    public string $query = '';

    #[Url]
    public string $category = '';

    #[Url]
    public string $location = '';

    #[Url]
    public string $sort = 'ending_soon';

    #[Url]
    public ?float $priceMin = null;

    #[Url]
    public ?float $priceMax = null;

    #[Url]
    public string $condition = '';

    /** @var array<string, string> */
    public array $categoryOptions = [];

    public function updatedQuery(): void
    {
        $this->resetPage();
    }

    public function updatedCategory(): void
    {
        $this->resetPage();
    }

    public function updatedLocation(): void
    {
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function updatedPriceMin(): void
    {
        $this->resetPage();
    }

    public function updatedPriceMax(): void
    {
        $this->resetPage();
    }

    public function updatedCondition(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        if (Schema::hasTable('categories')) {
            $this->categoryOptions = ['' => 'Sve kategorije'] + Category::query()
                ->orderBy('name')
                ->limit(20)
                ->pluck('name', 'slug')
                ->all();
        }

        if (Schema::hasTable('auctions')) {
            if (config('scout.driver') !== 'null' && $this->query !== '') {
                $builder = Auction::search($this->query)
                    ->query(function ($q) {
                        $q->active()
                            ->with(['category', 'primaryImage'])
                            ->when($this->category !== '', fn ($q) => $q->whereHas('category', fn ($c) => $c->where('slug', $this->category)))
                            ->when($this->priceMin !== null, fn ($q) => $q->where('current_price', '>=', $this->priceMin))
                            ->when($this->priceMax !== null, fn ($q) => $q->where('current_price', '<=', $this->priceMax))
                            ->when($this->condition !== '', fn ($q) => $q->where('condition', $this->condition))
                            ->when($this->location !== '', fn ($q) => $q->where('location_city', 'like', '%'.$this->location.'%'));
                    });

                match ($this->sort) {
                    'newest'    => $builder->orderBy('created_at', 'desc'),
                    'most_bids' => $builder->orderBy('bids_count', 'desc'),
                    'price_low' => $builder->orderBy('current_price', 'asc'),
                    'price_high' => $builder->orderBy('current_price', 'desc'),
                    default     => $builder->orderBy('ends_at', 'asc'),
                };

                $results = $builder->paginate(20);
            } else {
                $query = Auction::query()
                    ->with(['category', 'primaryImage'])
                    ->active()
                    ->when($this->query !== '', fn ($q) => $q->where('title', 'like', '%'.$this->query.'%'))
                    ->when($this->category !== '', fn ($q) => $q->whereHas('category', fn ($c) => $c->where('slug', $this->category)))
                    ->when($this->priceMin !== null, fn ($q) => $q->where('current_price', '>=', $this->priceMin))
                    ->when($this->priceMax !== null, fn ($q) => $q->where('current_price', '<=', $this->priceMax))
                    ->when($this->condition !== '', fn ($q) => $q->where('condition', $this->condition))
                    ->when($this->location !== '', fn ($q) => $q->where('location_city', 'like', '%'.$this->location.'%'));

                match ($this->sort) {
                    'newest'    => $query->latest(),
                    'most_bids' => $query->orderByDesc('bids_count'),
                    'price_low' => $query->orderBy('current_price'),
                    'price_high' => $query->orderByDesc('current_price'),
                    default     => $query->orderBy('ends_at'),
                };

                $results = $query->paginate(20);
            }

            return view('livewire.auction-search', [
                'results' => $results->through(fn (Auction $auction) => [
                    'id'        => $auction->id,
                    'title'     => $auction->title,
                    'category'  => $auction->category?->name ?? 'Bez kategorije',
                    'price'     => (float) $auction->current_price,
                    'bids'      => $auction->bids_count,
                    'watchers'  => $auction->watchers_count,
                    'location'  => $auction->location_city ?? $auction->location ?? 'Nepoznato',
                    'time'      => $auction->time_remaining,
                    'image_url' => $auction->primaryImage?->url,
                ]),
            ]);
        }

        // Fallback demo data when DB tables don't exist yet (local dev / CI without migrations)
        $demoResults = collect([
            ['id' => 'demo-1', 'title' => 'Samsung Galaxy S24 Ultra', 'category' => 'Elektronika', 'price' => 1250.00, 'bids' => 14, 'watchers' => 32, 'location' => 'Sarajevo', 'time' => '2d 04h', 'image_url' => null],
            ['id' => 'demo-2', 'title' => 'Sony WH-1000XM5', 'category' => 'Audio', 'price' => 480.00, 'bids' => 11, 'watchers' => 17, 'location' => 'Banja Luka', 'time' => '5h 12m', 'image_url' => null],
            ['id' => 'demo-3', 'title' => 'Vintage Leica M3', 'category' => 'Foto oprema', 'price' => 2750.00, 'bids' => 19, 'watchers' => 44, 'location' => 'Tuzla', 'time' => '1d 18h', 'image_url' => null],
        ]);

        return view('livewire.auction-search', ['results' => $demoResults]);
    }
}
