@extends('layouts.guest')

@section('title', 'Aukcije.ba - Licitiraj. Pobijedi. Uživaj.')
@section('meta')
    <x-seo-meta
        title="Aukcije.ba - Licitiraj. Pobijedi. Uživaj."
        description="Regionalna aukcijska platforma sa escrow zaštitom, verified seller signalima i real-time licitiranjem."
    />
@endsection

@section('content')
<section class="bg-[radial-gradient(circle_at_top_left,_rgba(37,99,235,0.12),_transparent_40%),linear-gradient(180deg,#ffffff_0%,#eff6ff_100%)]">
    <div class="mx-auto grid max-w-7xl gap-12 px-4 py-20 sm:px-6 lg:grid-cols-[1.1fr_0.9fr] lg:px-8 lg:py-24">
        <div class="space-y-8">
            <div class="space-y-4">
                <x-badge variant="trust">Escrow zaštita i real-time bidding</x-badge>
                <h1 class="max-w-2xl text-4xl font-semibold tracking-tight text-slate-950 sm:text-5xl">Regionalna aukcijska platforma za artikle koje vrijedi dobiti po pravoj cijeni.</h1>
                <p class="max-w-2xl text-lg text-slate-600">Kupci dobijaju brzinu, selleri dobijaju strukturisan seller studio, a obje strane dobijaju povjerenje kroz escrow, verified bedževe i jasne rokove transakcije.</p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                <x-button :href="route('register')">Počni licitirati</x-button>
                <x-button variant="ghost" :href="route('register')">Prodaj odmah</x-button>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <x-card>
                    <p class="text-sm text-slate-500">Aktivnih aukcija</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-900">1.240</p>
                </x-card>
                <x-card>
                    <p class="text-sm text-slate-500">Registriranih korisnika</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-900">18.500</p>
                </x-card>
                <x-card>
                    <p class="text-sm text-slate-500">Pozitivnih ocjena</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-900">97.8%</p>
                </x-card>
            </div>

            <div class="space-y-3">
                <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Popularne pretrage</p>
                <div class="flex flex-wrap gap-2">
                    @foreach ($popularSearches as $term)
                        <a href="{{ route('search', ['q' => $term]) }}" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 transition hover:border-trust-300 hover:bg-trust-50 hover:text-trust-700">{{ $term }}</a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="space-y-5 rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-xl shadow-trust-100 backdrop-blur">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Featured aukcija</p>
                    <h2 class="mt-2 text-2xl font-semibold text-slate-900">{{ $featuredAuction['title'] }}</h2>
                </div>
                <x-badge variant="warning">Uskoro završava</x-badge>
            </div>
            <a href="{{ route('auctions.show', ['auction' => $featuredAuction['id']]) }}" class="block overflow-hidden rounded-3xl bg-gradient-to-br from-slate-100 to-slate-200">
                @if ($featuredAuction['image_url'])
                    <img src="{{ $featuredAuction['image_url'] }}" alt="{{ $featuredAuction['title'] }}" class="h-72 w-full object-cover" />
                @else
                    <div class="p-10 text-center text-sm font-medium text-slate-500">Hero auction preview</div>
                @endif
            </a>
            <div class="grid gap-4 sm:grid-cols-2">
                <x-price-display :amount="$featuredAuction['price']">Trenutna cijena</x-price-display>
                <div class="space-y-1">
                    <p class="price-label">Preostalo</p>
                    <p class="countdown-normal text-2xl">{{ $featuredAuction['time'] }}</p>
                </div>
            </div>
            <x-progress-bar :value="84" />
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="flex items-end justify-between gap-4">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Featured aukcije</p>
            <h2 class="mt-2 text-3xl font-semibold text-slate-900">Aukcije koje završavaju uskoro</h2>
        </div>
        <a href="{{ route('auctions.index') }}" class="link">Pregledaj sve</a>
    </div>

    <div class="mt-8 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($featuredAuctions as $auction)
            <x-auction-card :title="$auction['title']" :category="$auction['category']" :price="$auction['price']" :bids="$auction['bids']" :watchers="$auction['watchers']" :location="$auction['location']" :time="$auction['time']" :image-url="$auction['image_url']" :href="route('auctions.show', ['auction' => $auction['id']])" />
        @endforeach
    </div>
</section>

<section class="bg-white">
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($categoryHighlights as $category)
                <x-card class="space-y-3">
                    <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Kategorija</p>
                    <a href="{{ route('categories.show', ['category' => $category['slug']]) }}" class="text-xl font-semibold text-slate-900 transition hover:text-trust-700">{{ $category['name'] }}</a>
                    <p class="text-sm text-slate-600">{{ $category['count'] }} aktivnih aukcija i najtrazeniji artikli u ovoj kategoriji.</p>
                </x-card>
            @endforeach
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="flex items-end justify-between gap-4">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Najtraženije kategorije</p>
            <h2 class="mt-2 text-3xl font-semibold text-slate-900">Gdje korisnici trenutno najviše licitiraju</h2>
        </div>
        <a href="{{ route('categories.index') }}" class="link">Sve kategorije</a>
    </div>

    <div class="mt-8 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
        @foreach ($trendingCategories as $category)
            <x-card class="space-y-3">
                <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Traženo</p>
                <a href="{{ route('categories.show', ['category' => $category['slug']]) }}" class="text-xl font-semibold text-slate-900 transition hover:text-trust-700">{{ $category['name'] }}</a>
                <p class="text-sm text-slate-600">{{ $category['count'] }} aktivnih aukcija u fokusu kupaca.</p>
            </x-card>
        @endforeach
    </div>
</section>

<section class="bg-slate-50">
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid gap-10 lg:grid-cols-2">
            <div>
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Najviše praćeno</p>
                        <h2 class="mt-2 text-3xl font-semibold text-slate-900">Aukcije koje privlače pažnju</h2>
                    </div>
                    <a href="{{ route('auctions.index', ['sort' => 'most_bids']) }}" class="link">Vidi više</a>
                </div>

                <div class="mt-8 grid gap-6">
                    @foreach ($mostWatchedAuctions as $auction)
                        <x-auction-card :title="$auction['title']" :category="$auction['category']" :price="$auction['price']" :bids="$auction['bids']" :watchers="$auction['watchers']" :location="$auction['location']" :time="$auction['time']" :image-url="$auction['image_url']" :href="route('auctions.show', ['auction' => $auction['id']])" />
                    @endforeach
                </div>
            </div>

            <div>
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Uskoro ističe</p>
                        <h2 class="mt-2 text-3xl font-semibold text-slate-900">Ne propusti završnicu licitacije</h2>
                    </div>
                    <a href="{{ route('auctions.index', ['sort' => 'ending_soon']) }}" class="link">Vidi više</a>
                </div>

                <div class="mt-8 grid gap-6">
                    @foreach ($endingSoonAuctions as $auction)
                        <x-auction-card :title="$auction['title']" :category="$auction['category']" :price="$auction['price']" :bids="$auction['bids']" :watchers="$auction['watchers']" :location="$auction['location']" :time="$auction['time']" :image-url="$auction['image_url']" :href="route('auctions.show', ['auction' => $auction['id']])" />
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bg-white">
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Novo u ponudi</p>
                <h2 class="mt-2 text-3xl font-semibold text-slate-900">Svježe objavljene aukcije</h2>
                <p class="mt-2 max-w-2xl text-slate-600">Ovaj blok vuče odvojeni homepage data sloj i osvježava se bez oslanjanja na statične fallbacke.</p>
            </div>
            <a href="{{ route('auctions.index', ['sort' => 'newest']) }}" class="link">Nove aukcije</a>
        </div>

        <div class="mt-8">
            @livewire('homepage-sections', ['sections' => ['new_arrivals']])
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="grid gap-6 lg:grid-cols-3">
        <x-card class="space-y-3">
            <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">1</p>
            <h3 class="text-2xl font-semibold text-slate-900">Registruj se</h3>
            <p class="text-slate-600">Verifikuj email, podesi račun i biraj da li nastupaš kao kupac ili seller.</p>
        </x-card>
        <x-card class="space-y-3">
            <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">2</p>
            <h3 class="text-2xl font-semibold text-slate-900">Licitiraj ili objavi aukciju</h3>
            <p class="text-slate-600">Prati minimalni bid, koristi proxy bidding i dobij trenutni feedback kroz real-time update.</p>
        </x-card>
        <x-card class="space-y-3">
            <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">3</p>
            <h3 class="text-2xl font-semibold text-slate-900">Završi kupovinu sigurno</h3>
            <p class="text-slate-600">Escrow i sistem ocjena čuvaju obje strane do zatvaranja transakcije.</p>
        </x-card>
    </div>
</section>

<section class="bg-slate-950 text-white">
    <div class="mx-auto grid max-w-7xl gap-6 px-4 py-14 sm:px-6 lg:grid-cols-3 lg:px-8">
        <div class="rounded-3xl border border-white/10 bg-white/5 p-6">
            <p class="text-sm uppercase tracking-[0.18em] text-slate-300">Povjerenje</p>
            <h2 class="mt-3 text-2xl font-semibold">Jasna pravila kupovine i prodaje</h2>
            <p class="mt-3 text-slate-300">Brz pristup pravilima, ocjenama korisnika, porukama i operativnim statusima svake narudžbe.</p>
        </div>
        <div class="rounded-3xl border border-white/10 bg-white/5 p-6">
            <p class="text-sm uppercase tracking-[0.18em] text-slate-300">Pomoć</p>
            <h2 class="mt-3 text-2xl font-semibold">Centar pomoći za kupce i prodavače</h2>
            <p class="mt-3 text-slate-300">Vodiči za sigurnu kupovinu, objavu aukcije, ocjene i kontakt sa podrškom na jednom mjestu.</p>
            <a href="{{ route('help.index') }}" class="mt-4 inline-block text-sm font-medium text-white underline underline-offset-4">Otvori pomoć</a>
        </div>
        <div class="rounded-3xl border border-white/10 bg-white/5 p-6">
            <p class="text-sm uppercase tracking-[0.18em] text-slate-300">Zajednica</p>
            <h2 class="mt-3 text-2xl font-semibold">Platforma koja objašnjava promjene</h2>
            <p class="mt-3 text-slate-300">Novosti, obavijesti i pravne stranice dostupne su javno, bez skrivanja važnih informacija iza naloga.</p>
            <a href="{{ route('news.index') }}" class="mt-4 inline-block text-sm font-medium text-white underline underline-offset-4">Pogledaj vijesti</a>
        </div>
    </div>
</section>

<section class="bg-white">
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Vijesti i obavijesti</p>
                <h2 class="mt-2 text-3xl font-semibold text-slate-900">Šta je novo na platformi</h2>
            </div>
            <a href="{{ route('news.index') }}" class="link">Sve vijesti</a>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-3">
            @foreach ($latestNews as $article)
                <x-card class="space-y-3">
                    <p class="text-sm text-slate-500">{{ optional($article['published_at'] ?? null)->format('d.m.Y.') ?? optional($article->published_at)->format('d.m.Y.') }}</p>
                    <h3 class="text-xl font-semibold text-slate-900">{{ $article['title'] ?? $article->title }}</h3>
                    <p class="text-slate-600">{{ $article['excerpt'] ?? $article->excerpt }}</p>
                    <a href="{{ route('news.show', ['article' => $article['slug'] ?? $article->slug]) }}" class="link">Pročitaj više</a>
                </x-card>
            @endforeach
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="flex items-end justify-between gap-4">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Iskustva korisnika</p>
            <h2 class="mt-2 text-3xl font-semibold text-slate-900">Zašto se korisnici vraćaju</h2>
        </div>
        <a href="{{ route('content.show', ['slug' => 'ocjene-i-saradnja']) }}" class="link">Kako rade ocjene</a>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        @foreach ([
            ['name' => 'Kupac iz Sarajeva', 'text' => 'Najviše mi znači što mogu brzo provjeriti pravila, ocjene i status narudžbe bez dodatnog traženja.'],
            ['name' => 'Prodavač iz Mostara', 'text' => 'Objava aukcije i slanje tracking podataka su dovoljno jasni da se posao može voditi bez improvizacije.'],
            ['name' => 'Verifikovani prodavač', 'text' => 'Vijesti i javne stranice olakšavaju da kupci odmah razumiju kako platforma radi i kome vjeruju.'],
        ] as $quote)
            <x-card class="space-y-4">
                <p class="text-slate-700">“{{ $quote['text'] }}”</p>
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-500">{{ $quote['name'] }}</p>
            </x-card>
        @endforeach
    </div>
</section>
@endsection
