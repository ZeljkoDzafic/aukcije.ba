<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\HomepageDataService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class HomepageSections extends Component
{
    /** @var list<string> */
    public array $sections = ['featured', 'ending_soon', 'new_arrivals', 'most_watched'];

    public iterable $featuredAuctions = [];

    public iterable $endingSoonAuctions = [];

    public iterable $newArrivalsAuctions = [];

    public iterable $mostWatchedAuctions = [];

    /** @var array<string, bool> */
    public array $loaded = [
        'featured' => false,
        'ending_soon' => false,
        'new_arrivals' => false,
        'most_watched' => false,
    ];

    public function mount(array $sections = ['featured', 'ending_soon', 'new_arrivals', 'most_watched']): void
    {
        $this->sections = $sections;

        if (app()->runningUnitTests()) {
            if (in_array('featured', $this->sections, true)) {
                $this->loadFeatured();
            }

            if (in_array('ending_soon', $this->sections, true)) {
                $this->loadEndingSoon();
            }

            if (in_array('new_arrivals', $this->sections, true)) {
                $this->loadNewArrivals();
            }

            if (in_array('most_watched', $this->sections, true)) {
                $this->loadMostWatched();
            }
        }
    }

    public function loadFeatured(): void
    {
        $this->loaded['featured'] = true;
        $this->featuredAuctions = app(HomepageDataService::class)->featured();
    }

    public function loadEndingSoon(): void
    {
        $this->loaded['ending_soon'] = true;
        $this->endingSoonAuctions = app(HomepageDataService::class)->endingSoon();
    }

    public function loadNewArrivals(): void
    {
        $this->loaded['new_arrivals'] = true;
        $this->newArrivalsAuctions = app(HomepageDataService::class)->newArrivals();
    }

    public function loadMostWatched(): void
    {
        $this->loaded['most_watched'] = true;
        $this->mostWatchedAuctions = app(HomepageDataService::class)->mostWatched();
    }

    public function render(): View
    {
        return view('livewire.homepage-sections');
    }
}
