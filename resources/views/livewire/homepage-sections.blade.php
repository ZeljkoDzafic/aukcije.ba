{{--
    Homepage Sections Component
    T-1250: 4 Livewire sekcije (featured, ending_soon, new_arrivals, most_watched)
--}}

@props(['sections' => ['featured', 'ending_soon', 'new_arrivals', 'most_watched']])

<div class="space-y-12">
    {{-- Featured Auctions --}}
    @if(in_array('featured', $sections))
        <section>
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">
                    ⭐ Izdvojene aukcije
                </h2>
                <a href="{{ route('auctions.index', ['featured' => 1]) }}" class="text-blue-600 hover:text-blue-700 font-medium">
                    Pogledaj sve →
                </a>
            </div>
            
            <div wire:poll.30s="loadFeatured" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($featuredAuctions as $auction)
                    <x-auction-card :title="$auction->title" :category="$auction->category?->name ?? 'Bez kategorije'" :price="$auction->current_price" :bids="$auction->bids_count" :watchers="$auction->watchers_count" :location="$auction->location_city ?? $auction->location ?? 'Nepoznato'" :time="$auction->time_remaining" :image-url="$auction->primaryImage?->url ?? $auction->images->first()?->url ?? null" :href="route('auctions.show', ['auction' => $auction->id])" />
                @endforeach
            </div>
        </section>
    @endif
    
    {{-- Ending Soon --}}
    @if(in_array('ending_soon', $sections))
        <section>
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">
                    ⏰ Ubrzo završava
                </h2>
                <a href="{{ route('auctions.index', ['sort' => 'ending_soon']) }}" class="text-blue-600 hover:text-blue-700 font-medium">
                    Pogledaj sve →
                </a>
            </div>
            
            <div wire:poll.10s="loadEndingSoon" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($endingSoonAuctions as $auction)
                    <x-auction-card :title="$auction->title" :category="$auction->category?->name ?? 'Bez kategorije'" :price="$auction->current_price" :bids="$auction->bids_count" :watchers="$auction->watchers_count" :location="$auction->location_city ?? $auction->location ?? 'Nepoznato'" :time="$auction->time_remaining" :image-url="$auction->primaryImage?->url ?? $auction->images->first()?->url ?? null" :href="route('auctions.show', ['auction' => $auction->id])" />
                @endforeach
            </div>
        </section>
    @endif
    
    {{-- New Arrivals --}}
    @if(in_array('new_arrivals', $sections))
        <section>
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">
                    🆕 Novo stiglo
                </h2>
                <a href="{{ route('auctions.index', ['sort' => 'newest']) }}" class="text-blue-600 hover:text-blue-700 font-medium">
                    Pogledaj sve →
                </a>
            </div>
            
            <div wire:poll.60s="loadNewArrivals" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($newArrivalsAuctions as $auction)
                    <x-auction-card :title="$auction->title" :category="$auction->category?->name ?? 'Bez kategorije'" :price="$auction->current_price" :bids="$auction->bids_count" :watchers="$auction->watchers_count" :location="$auction->location_city ?? $auction->location ?? 'Nepoznato'" :time="$auction->time_remaining" :image-url="$auction->primaryImage?->url ?? $auction->images->first()?->url ?? null" :href="route('auctions.show', ['auction' => $auction->id])" />
                @endforeach
            </div>
        </section>
    @endif
    
    {{-- Most Watched --}}
    @if(in_array('most_watched', $sections))
        <section>
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">
                    👁️ Najpraćenije
                </h2>
                <a href="{{ route('auctions.index', ['sort' => 'most_watched']) }}" class="text-blue-600 hover:text-blue-700 font-medium">
                    Pogledaj sve →
                </a>
            </div>
            
            <div wire:poll.60s="loadMostWatched" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($mostWatchedAuctions as $auction)
                    <x-auction-card :title="$auction->title" :category="$auction->category?->name ?? 'Bez kategorije'" :price="$auction->current_price" :bids="$auction->bids_count" :watchers="$auction->watchers_count" :location="$auction->location_city ?? $auction->location ?? 'Nepoznato'" :time="$auction->time_remaining" :image-url="$auction->primaryImage?->url ?? $auction->images->first()?->url ?? null" :href="route('auctions.show', ['auction' => $auction->id])" />
                @endforeach
            </div>
        </section>
    @endif
</div>

@push('scripts')
<script>
    Livewire.on('auction-updated', (auctionId) => {
        // Refresh specific auction card
        $wire.loadAuction(auctionId);
    });
</script>
@endpush
