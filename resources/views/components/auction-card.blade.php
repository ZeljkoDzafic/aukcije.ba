@props([
    'title' => 'Samsung Galaxy S24 Ultra',
    'category' => 'Elektronika',
    'price' => 1250,
    'bids' => 14,
    'watchers' => 32,
    'location' => 'Sarajevo',
    'time' => '2d 04h',
    'href' => null,
    'imageUrl' => null,
    'blurhash' => null,
    'badge' => 'Uskoro završava',
])

<article class="auction-card group relative">
    @if ($href)
        <a href="{{ $href }}" class="absolute inset-0 z-10 rounded-[1.25rem]" aria-label="Otvori aukciju {{ $title }}"></a>
    @endif

    <div class="auction-card-image">
        @if ($imageUrl)
            <x-blurhash-placeholder :src="$imageUrl" :blurhash="$blurhash" :alt="$title" class="h-full w-full" />
        @else
            <div class="flex h-full items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200 text-sm font-medium text-slate-500">Aukcija</div>
        @endif
        <div class="absolute left-3 top-3">
            <x-badge variant="warning">{{ $badge }}</x-badge>
        </div>
    </div>

    <div class="relative z-20 space-y-4 p-5">
        <div class="space-y-1">
            <p class="text-sm text-slate-500">{{ $category }}</p>
            <h3 class="line-clamp-2 text-lg font-semibold text-slate-900">{{ $title }}</h3>
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
            <x-button class="pointer-events-none flex-1">Otvori aukciju</x-button>
            <x-button variant="ghost" class="flex-1">Prati</x-button>
        </div>
    </div>
</article>
