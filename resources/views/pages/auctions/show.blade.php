@extends('layouts.guest')

@php
    $auction = isset($auction)
        ? (object) array_merge([
            'id' => 1,
            'title' => 'Samsung Galaxy S24 Ultra 512GB',
            'description' => 'Detalj aukcije sa real-time bidding panelom.',
            'category_name' => 'Elektronika',
            'category_slug' => 'elektronika',
            'current_price' => 1250.00,
            'minimum_bid' => 1255.00,
            'ends_at' => now()->addDays(3),
            'location' => 'Sarajevo',
            'condition' => 'Polovno, odlično',
            'shipping_info' => 'Brza pošta ili lično preuzimanje',
            'bids_count' => 23,
            'watchers_count' => 32,
            'seller_name' => 'Haris B.',
            'seller_rating' => '4.9',
            'seller_sales' => 128,
            'seller_location' => 'Sarajevo',
            'images' => [],
            'bid_history' => [],
        ], (array) $auction)
        : (object) [
            'id' => 1,
            'title' => 'Samsung Galaxy S24 Ultra 512GB',
            'description' => 'Detalj aukcije sa real-time bidding panelom.',
            'category_name' => 'Elektronika',
            'category_slug' => 'elektronika',
            'current_price' => 1250.00,
            'minimum_bid' => 1255.00,
            'ends_at' => now()->addDays(3),
            'location' => 'Sarajevo',
            'condition' => 'Polovno, odlično',
            'shipping_info' => 'Brza pošta ili lično preuzimanje',
            'bids_count' => 23,
            'watchers_count' => 32,
            'seller_name' => 'Haris B.',
            'seller_rating' => '4.9',
            'seller_sales' => 128,
            'seller_location' => 'Sarajevo',
            'images' => [],
            'bid_history' => [],
        ];

    $relatedAuctions = $relatedAuctions ?? collect();
    $bidHistory = collect($auction->bid_history ?: [
        ['bidder' => 'h***1', 'amount' => '1.250,00 BAM'],
        ['bidder' => 'a***7', 'amount' => '1.245,00 BAM'],
        ['bidder' => 'm***3', 'amount' => '1.230,00 BAM'],
    ]);
@endphp

@section('title', $auction->title)
@section('meta')
    <x-seo-meta
        :title="$auction->title.' - Aukcije.ba'"
        :description="$auction->description"
        type="product"
    />
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $auction->title,
            'description' => $auction->description,
            'category' => $auction->category_name,
            'offers' => [
                '@type' => 'Offer',
                'priceCurrency' => 'BAM',
                'price' => number_format((float) $auction->current_price, 2, '.', ''),
                'availability' => 'https://schema.org/InStock',
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Aukcije', 'item' => route('auctions.index')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => $auction->category_name, 'item' => route('categories.show', ['category' => $auction->category_slug])],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $auction->title, 'item' => url()->current()],
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endsection

@section('content')
<section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
    <div class="grid gap-8 lg:grid-cols-[1.15fr_0.85fr]">
        <div class="space-y-6">
            <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                <x-image-gallery :count="max(count($auction->images), 4)" />
            </div>

            <x-card class="space-y-4">
                <div class="space-y-2">
                    <p class="text-sm text-slate-500">{{ $auction->category_name }}</p>
                    <h1 class="text-3xl font-semibold text-slate-900">{{ $auction->title }}</h1>
                    <p class="text-slate-600">{{ $auction->description }}</p>
                </div>
                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <p class="text-sm text-slate-500">Stanje</p>
                        <p class="mt-1 font-medium text-slate-900">{{ $auction->condition }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Lokacija</p>
                        <p class="mt-1 font-medium text-slate-900">{{ $auction->location }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Dostava</p>
                        <p class="mt-1 font-medium text-slate-900">{{ $auction->shipping_info }}</p>
                    </div>
                </div>
            </x-card>

            <x-card class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-slate-900">Seller informacije</h2>
                    <x-badge variant="trust">Verified seller</x-badge>
                </div>
                <div class="flex items-center gap-4">
                    <x-avatar :name="$auction->seller_name" size="lg" />
                    <div>
                        <p class="font-semibold text-slate-900">{{ $auction->seller_name }}</p>
                        <p class="text-sm text-slate-600">{{ $auction->seller_rating }} rating · {{ $auction->seller_sales }} prodaja · {{ $auction->seller_location }}</p>
                    </div>
                </div>
            </x-card>
        </div>

        <div class="space-y-6 lg:sticky lg:top-24 lg:self-start">
            <x-card class="space-y-5">
                <x-price-display :amount="$auction->current_price" />
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-sm text-slate-600">
                        <span>Aukcija završava za</span>
                        <span class="countdown-warning text-lg">{{ $auction->ends_at instanceof \Illuminate\Support\Carbon ? $auction->ends_at->diffForHumans() : $auction->ends_at }}</span>
                    </div>
                    <x-progress-bar :value="74" />
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <x-input name="bid_amount" type="number" label="Vaš bid" hint="Minimalni sljedeći bid: 1.255,00 BAM" />
                    <x-input name="proxy_max" type="number" label="Maksimalni proxy bid" hint="Opcionalno" />
                </div>
                <label class="inline-flex items-center gap-3 text-sm text-slate-600">
                    <input type="checkbox" class="rounded border-slate-300 text-trust-600 focus:ring-trust-500">
                    <span>Uključi proxy bidding</span>
                </label>
                <div class="space-y-3">
                    <x-button class="w-full">Licitiraj</x-button>
                    <x-button variant="ghost" class="w-full">Dodaj u praćenje</x-button>
                </div>
                <div
                    id="bidding-console-root"
                    data-auction-id="{{ $auction->id }}"
                    data-current-price="{{ $auction->current_price }}"
                    data-minimum-bid="{{ $auction->minimum_bid }}"
                    data-ends-at="{{ $auction->ends_at instanceof \Illuminate\Support\Carbon ? $auction->ends_at->toIso8601String() : $auction->ends_at }}"
                    data-currency="BAM"
                    data-can-bid="true"
                    data-endpoint="{{ url('/api/v1/auctions/'.$auction->id.'/bid') }}"
                    data-user-id="{{ auth()->id() }}"
                ></div>
            </x-card>

            <x-card class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-slate-900">Historija bidova</h2>
                    <span class="text-sm text-slate-500">{{ $auction->bids_count }} ukupno</span>
                </div>
                <div class="space-y-3">
                    @foreach ($bidHistory as $entry)
                        <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                            <span class="font-medium text-slate-900">{{ $entry['bidder'] }}</span>
                            <span class="countdown text-slate-700">{{ $entry['amount'] }}</span>
                        </div>
                    @endforeach
                </div>
            </x-card>
        </div>
    </div>

    <div class="mt-10 space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-semibold text-slate-900">Povezane aukcije</h2>
            <a href="{{ route('auctions.index') }}" class="link">Vidi još</a>
        </div>
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($relatedAuctions as $item)
                <x-auction-card :title="$item['title']" :category="$item['category']" :price="$item['price']" :bids="$item['bids']" :watchers="$item['watchers']" :location="$item['location']" :time="$item['time']" :href="route('auctions.show', ['auction' => $item['id']])" />
            @endforeach
        </div>
    </div>
</section>
@endsection
