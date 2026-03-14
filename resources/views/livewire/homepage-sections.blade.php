{{--
    Homepage Sections Component
    T-1250: 4 Livewire sekcije (featured, ending_soon, new_arrivals, most_watched)
--}}

@props(['sections' => ['featured', 'ending_soon', 'new_arrivals', 'most_watched']])

<div class="space-y-12">
    {{-- Featured Auctions --}}
    @if(in_array('featured', $sections))
        <section wire:init="loadFeatured">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">
                    ⭐ Izdvojene aukcije
                </h2>
                <a href="{{ route('auctions.index', ['featured' => 1]) }}" class="text-blue-600 hover:text-blue-700 font-medium">
                    Pogledaj sve →
                </a>
            </div>
            
            <div wire:poll.30s="loadFeatured" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @if($loaded['featured'] && count($featuredAuctions) > 0)
                    @foreach($featuredAuctions as $auction)
                        <x-auction-card :title="$auction->title" :category="$auction->category?->name ?? 'Bez kategorije'" :price="$auction->current_price" :bids="$auction->bids_count" :watchers="$auction->watchers_count" :location="$auction->location_city ?? $auction->location ?? 'Nepoznato'" :time="$auction->time_remaining" :seller="$auction->seller?->name" badge="Izdvojeno" badge-variant="trust" cta-label="Pogledaj detalj" :image-url="$auction->primaryImage?->getOptimizedUrl('medium') ?? $auction->images->first()?->url ?? null" :srcset="$auction->primaryImage?->getSrcset()" :blurhash="$auction->primaryImage?->blurhash" :href="route('auctions.show', ['auction' => $auction->id])" />
                    @endforeach
                @else
                    @for ($i = 0; $i < 4; $i++)
                        <div class="h-80 animate-pulse rounded-[1.5rem] bg-slate-100"></div>
                    @endfor
                @endif
            </div>
        </section>
    @endif
    
    {{-- Ending Soon --}}
    @if(in_array('ending_soon', $sections))
        <section wire:init="loadEndingSoon">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">
                    ⏰ Ubrzo završava
                </h2>
                <a href="{{ route('auctions.index', ['sort' => 'ending_soon']) }}" class="text-blue-600 hover:text-blue-700 font-medium">
                    Pogledaj sve →
                </a>
            </div>
            
            <div wire:poll.10s="loadEndingSoon" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @if($loaded['ending_soon'] && count($endingSoonAuctions) > 0)
                    @foreach($endingSoonAuctions as $auction)
                        <x-auction-card :title="$auction->title" :category="$auction->category?->name ?? 'Bez kategorije'" :price="$auction->current_price" :bids="$auction->bids_count" :watchers="$auction->watchers_count" :location="$auction->location_city ?? $auction->location ?? 'Nepoznato'" :time="$auction->time_remaining" :seller="$auction->seller?->name" badge="Uskoro pada čekić" badge-variant="warning" cta-label="Licitiraj sada" :image-url="$auction->primaryImage?->getOptimizedUrl('medium') ?? $auction->images->first()?->url ?? null" :srcset="$auction->primaryImage?->getSrcset()" :blurhash="$auction->primaryImage?->blurhash" :href="route('auctions.show', ['auction' => $auction->id])" />
                    @endforeach
                @else
                    @for ($i = 0; $i < 4; $i++)
                        <div class="h-80 animate-pulse rounded-[1.5rem] bg-slate-100"></div>
                    @endfor
                @endif
            </div>
        </section>
    @endif
    
    {{-- New Arrivals --}}
    @if(in_array('new_arrivals', $sections))
        <section wire:init="loadNewArrivals">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">
                    🆕 Novo stiglo
                </h2>
                <a href="{{ route('auctions.index', ['sort' => 'newest']) }}" class="text-blue-600 hover:text-blue-700 font-medium">
                    Pogledaj sve →
                </a>
            </div>
            
            <div wire:poll.60s="loadNewArrivals" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @if($loaded['new_arrivals'] && count($newArrivalsAuctions) > 0)
                    @foreach($newArrivalsAuctions as $auction)
                        <x-auction-card :title="$auction->title" :category="$auction->category?->name ?? 'Bez kategorije'" :price="$auction->current_price" :bids="$auction->bids_count" :watchers="$auction->watchers_count" :location="$auction->location_city ?? $auction->location ?? 'Nepoznato'" :time="$auction->time_remaining" :seller="$auction->seller?->name" badge="Novo u ponudi" badge-variant="success" cta-label="Provjeri listing" :image-url="$auction->primaryImage?->getOptimizedUrl('medium') ?? $auction->images->first()?->url ?? null" :srcset="$auction->primaryImage?->getSrcset()" :blurhash="$auction->primaryImage?->blurhash" :href="route('auctions.show', ['auction' => $auction->id])" />
                    @endforeach
                @else
                    @for ($i = 0; $i < 4; $i++)
                        <div class="h-80 animate-pulse rounded-[1.5rem] bg-slate-100"></div>
                    @endfor
                @endif
            </div>
        </section>
    @endif
    
    {{-- Most Watched --}}
    @if(in_array('most_watched', $sections))
        <section wire:init="loadMostWatched">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">
                    👁️ Najpraćenije
                </h2>
                <a href="{{ route('auctions.index', ['sort' => 'most_watched']) }}" class="text-blue-600 hover:text-blue-700 font-medium">
                    Pogledaj sve →
                </a>
            </div>
            
            <div wire:poll.60s="loadMostWatched" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @if($loaded['most_watched'] && count($mostWatchedAuctions) > 0)
                    @foreach($mostWatchedAuctions as $auction)
                        <x-auction-card :title="$auction->title" :category="$auction->category?->name ?? 'Bez kategorije'" :price="$auction->current_price" :bids="$auction->bids_count" :watchers="$auction->watchers_count" :location="$auction->location_city ?? $auction->location ?? 'Nepoznato'" :time="$auction->time_remaining" :seller="$auction->seller?->name" badge="Najviše prati" badge-variant="secondary" cta-label="Vidi zašto je tražena" :image-url="$auction->primaryImage?->getOptimizedUrl('medium') ?? $auction->images->first()?->url ?? null" :srcset="$auction->primaryImage?->getSrcset()" :blurhash="$auction->primaryImage?->blurhash" :href="route('auctions.show', ['auction' => $auction->id])" />
                    @endforeach
                @else
                    @for ($i = 0; $i < 4; $i++)
                        <div class="h-80 animate-pulse rounded-[1.5rem] bg-slate-100"></div>
                    @endfor
                @endif
            </div>
        </section>
    @endif
</div>
