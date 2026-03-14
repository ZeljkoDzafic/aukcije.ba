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
            'reserve_price' => null,
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
            'reserve_price' => null,
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
<section class="mx-auto max-w-7xl bg-[radial-gradient(circle_at_top_right,_rgba(14,165,233,0.08),_transparent_24%),radial-gradient(circle_at_bottom_left,_rgba(245,158,11,0.10),_transparent_24%)] px-4 py-12 sm:px-6 lg:px-8">
    <div class="mb-8 flex flex-wrap items-center gap-3 text-sm">
        <a href="{{ route('auctions.index') }}" class="text-slate-500 transition hover:text-slate-900">Aukcije</a>
        <span class="text-slate-300">/</span>
        <a href="{{ route('categories.show', ['category' => $auction->category_slug]) }}" class="text-slate-500 transition hover:text-slate-900">{{ $auction->category_name }}</a>
        <span class="text-slate-300">/</span>
        <span class="font-medium text-slate-900">{{ $auction->title }}</span>
    </div>

    <div class="grid gap-8 lg:grid-cols-[1.15fr_0.85fr]">
        <div class="space-y-6">
            <div class="market-sheen rounded-[2rem] border border-slate-200 bg-[linear-gradient(180deg,#ffffff_0%,#f8fafc_100%)] p-6 shadow-sm">
                <x-image-gallery :count="max(count($auction->images), 4)" />
            </div>

            <x-card class="market-sheen space-y-6 bg-[linear-gradient(180deg,#ffffff_0%,#f8fafc_100%)]">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="space-y-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <x-badge variant="trust">{{ $auction->category_name }}</x-badge>
                            <x-badge variant="warning">Aktivna aukcija</x-badge>
                            @if ($auction->watchers_count > 20)
                                <x-badge variant="secondary">Visok interes</x-badge>
                            @endif
                        </div>
                        <h1 class="max-w-3xl text-3xl font-semibold leading-tight text-slate-900 sm:text-4xl">{{ $auction->title }}</h1>
                        <p class="text-sm text-slate-500">Lokacija {{ $auction->location }} · {{ $auction->bids_count }} bidova · {{ $auction->watchers_count }} prati</p>
                    </div>
                    <div class="market-sheen rounded-[1.5rem] bg-[linear-gradient(135deg,#0f172a_0%,#1e293b_100%)] px-5 py-4 text-white">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Vrijeme do kraja</p>
                        <p class="mt-2 text-2xl font-semibold text-amber-300">{{ $auction->ends_at instanceof \Illuminate\Support\Carbon ? $auction->ends_at->diffForHumans() : $auction->ends_at }}</p>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-4">
                    <div class="rounded-[1.5rem] bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Trenutna cijena</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format((float) $auction->current_price, 2, ',', '.') }} BAM</p>
                    </div>
                    <div class="rounded-[1.5rem] bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Minimalni sljedeći bid</p>
                        <p class="mt-2 text-2xl font-semibold text-trust-700">{{ number_format((float) $auction->minimum_bid, 2, ',', '.') }} BAM</p>
                    </div>
                    <div class="rounded-[1.5rem] bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Interes</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $auction->watchers_count }}</p>
                        <p class="mt-1 text-sm text-slate-500">korisnika prati aukciju</p>
                    </div>
                    <div class="rounded-[1.5rem] bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Seller rating</p>
                        <p class="mt-2 text-2xl font-semibold text-emerald-700">{{ $auction->seller_rating }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ $auction->seller_sales }} prodaja</p>
                    </div>
                </div>

                <div class="rounded-[1.75rem] border border-slate-200 bg-gradient-to-br from-white to-slate-50 p-5">
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="rounded-full bg-emerald-50 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">Escrow zaštita</div>
                        <div class="rounded-full bg-sky-50 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-sky-700">Verified seller signal</div>
                        <div class="rounded-full bg-amber-50 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-amber-700">Real-time bidding</div>
                    </div>
                    <p class="mt-4 text-sm leading-7 text-slate-600">Detalj aukcije sada ističe brze signale za odluku: hitnost, reputaciju prodavača, interes tržišta i sljedeći bid bez potrebe da korisnik traži te informacije po ekranu.</p>
                </div>

                <div class="space-y-2">
                    <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Opis artikla</p>
                    <x-rich-text :html="$auction->description" class="prose-sm text-slate-600" />
                </div>
                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="rounded-[1.5rem] border border-slate-200 p-4">
                        <p class="text-sm text-slate-500">Stanje</p>
                        <p class="mt-1 font-medium text-slate-900">{{ $auction->condition }}</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-slate-200 p-4">
                        <p class="text-sm text-slate-500">Lokacija</p>
                        <p class="mt-1 font-medium text-slate-900">{{ $auction->location }}</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-slate-200 p-4">
                        <p class="text-sm text-slate-500">Dostava</p>
                        <p class="mt-1 font-medium text-slate-900">{{ $auction->shipping_info }}</p>
                    </div>
                </div>
            </x-card>

            <x-card class="market-sheen space-y-5 bg-[linear-gradient(180deg,#ffffff_0%,#f8fafc_100%)]">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-slate-900">Informacije o prodavcu</h2>
                    <x-seller-reputation-badge :seller="property_exists($auction, 'seller') && $auction->seller ? $auction->seller : (object) ['name' => $auction->seller_name, 'trust_score' => (float) $auction->seller_rating, 'created_at' => now()]" size="sm" />
                </div>
                <div class="flex items-center gap-4 rounded-[1.5rem] bg-slate-50 p-4">
                    <x-avatar :name="$auction->seller_name" size="lg" />
                    <div>
                        <a href="{{ ! empty($auction->seller_id) ? route('sellers.show', ['user' => $auction->seller_id]) : '#' }}" class="font-semibold text-slate-900 transition hover:text-trust-700">{{ $auction->seller_name }}</a>
                        <p class="text-sm text-slate-600">{{ $auction->seller_rating }} ocjena · {{ $auction->seller_sales }} prodaja · {{ $auction->seller_location }}</p>
                    </div>
                </div>
                <div class="grid gap-4 md:grid-cols-3">
                    <div class="rounded-[1.5rem] border border-slate-200 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Reputacija</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $auction->seller_rating }}/5</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-slate-200 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Ukupno prodaja</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $auction->seller_sales }}</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-slate-200 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Seller lokacija</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $auction->seller_location }}</p>
                    </div>
                </div>
            </x-card>

            <x-card class="market-sheen space-y-4 bg-[linear-gradient(180deg,#fffef7_0%,#ffffff_100%)]">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-slate-900">Zašto licitirati sada</h2>
                    <x-badge variant="warning">Decision panel</x-badge>
                </div>
                <div class="grid gap-4 md:grid-cols-3">
                    <div class="rounded-[1.5rem] bg-amber-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-amber-700">Hitnost</p>
                        <p class="mt-3 text-sm leading-7 text-amber-900">Aukcija ima jasan vremenski okvir, što ubrzava odluku i pojačava nadmetanje pred kraj.</p>
                    </div>
                    <div class="rounded-[1.5rem] bg-sky-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-sky-700">Likvidnost</p>
                        <p class="mt-3 text-sm leading-7 text-sky-900">{{ $auction->bids_count }} bidova i {{ $auction->watchers_count }} prati sugerišu da listing nije mrtav i da tržište reaguje.</p>
                    </div>
                    <div class="rounded-[1.5rem] bg-emerald-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">Povjerenje</p>
                        <p class="mt-3 text-sm leading-7 text-emerald-900">Seller profil, reputacija i escrow sloj spuštaju rizik kod skupljih i osjetljivijih artikala.</p>
                    </div>
                </div>
            </x-card>
        </div>

        <div class="space-y-6 lg:sticky lg:top-24 lg:self-start">
            <div class="market-sheen overflow-hidden rounded-[2rem] border border-slate-200 bg-[linear-gradient(180deg,#ffffff_0%,#f8fafc_100%)] shadow-lg shadow-slate-200/70">
                <div class="bg-[linear-gradient(135deg,#0f172a_0%,#111827_55%,#102a43_100%)] px-6 py-5 text-white">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Bid panel</p>
                    <div class="mt-3 flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm text-slate-300">Trenutna cijena</p>
                            <p class="mt-2 text-4xl font-semibold">{{ number_format((float) $auction->current_price, 2, ',', '.') }} BAM</p>
                        </div>
                        <div class="rounded-[1.25rem] bg-white/5 px-4 py-3 text-right">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Kraj aukcije</p>
                            <p class="mt-2 text-lg font-semibold text-amber-300">{{ $auction->ends_at instanceof \Illuminate\Support\Carbon ? $auction->ends_at->diffForHumans() : $auction->ends_at }}</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-5 p-6">
                    <x-reserve-price-badge :auction="$auction" />
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm text-slate-600">
                            <span>Napredak aukcije</span>
                            <span>{{ $auction->watchers_count }} prati listing</span>
                        </div>
                        <x-progress-bar :value="74" />
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="rounded-[1.25rem] bg-slate-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Minimalni bid</p>
                            <p class="mt-2 text-xl font-semibold text-trust-700">{{ number_format((float) $auction->minimum_bid, 2, ',', '.') }} BAM</p>
                        </div>
                        <div class="rounded-[1.25rem] bg-slate-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Aktivnost</p>
                            <p class="mt-2 text-xl font-semibold text-slate-900">{{ $auction->bids_count }} nadmetanja</p>
                        </div>
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
                        <x-button class="w-full">Licitiraj odmah</x-button>
                        <x-button variant="ghost" class="w-full">Dodaj u praćenje</x-button>
                    </div>

                    <div class="grid gap-3">
                        <div class="rounded-[1.25rem] border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">Escrow zaštita smanjuje rizik transakcije.</div>
                        <div class="rounded-[1.25rem] border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-900">Detalj prodavača i trust score su vidljivi prije bida.</div>
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
                </div>
            </div>

            <x-card class="market-sheen space-y-4 bg-[linear-gradient(180deg,#ffffff_0%,#f8fafc_100%)]">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-slate-900">Pregled bidova</h2>
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
                <x-auction-card :title="$item['title']" :category="$item['category']" :price="$item['price']" :bids="$item['bids']" :watchers="$item['watchers']" :location="$item['location']" :time="$item['time']" badge="Slična prilika" badge-variant="secondary" cta-label="Pogledaj detalj" :href="route('auctions.show', ['auction' => $item['id']])" />
            @endforeach
        </div>
    </div>

    <x-similar-auctions-section :current-auction="$auction" />
</section>
@endsection
