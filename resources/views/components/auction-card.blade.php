@props([
    'title' => 'Samsung Galaxy S24 Ultra',
    'category' => 'Elektronika',
    'price' => 1250,
    'bids' => 14,
    'watchers' => 32,
    'location' => 'Sarajevo',
    'time' => '2d 04h',
    'href' => null,
])

<article class="auction-card group">
    <div class="auction-card-image">
        <div class="flex h-full items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200 text-sm font-medium text-slate-500">Aukcija</div>
        <div class="absolute left-3 top-3">
            <x-badge variant="warning">Uskoro završava</x-badge>
        </div>
    </div>

    <div class="space-y-4 p-5">
        <div class="space-y-1">
            <p class="text-sm text-slate-500">{{ $category }}</p>
            <h3 class="text-lg font-semibold text-slate-900">{{ $title }}</h3>
        </div>

        <x-price-display :amount="$price" />

        <div class="space-y-2">
            <div class="flex items-center justify-between text-sm text-slate-600">
                <span>Preostalo</span>
                <span class="countdown">{{ $time }}</span>
            </div>
            <x-progress-bar :value="78" />
        </div>

        <div class="flex items-center justify-between text-sm text-slate-500">
            <span>{{ $bids }} bidova</span>
            <span>{{ $watchers }} prati</span>
            <span>{{ $location }}</span>
        </div>

        <div class="flex gap-3">
            <x-button class="flex-1" :href="$href ?: route('auctions.show', ['auction' => 1])">Licitiraj</x-button>
            <x-button variant="ghost" class="flex-1">Prati</x-button>
        </div>
    </div>
</article>
