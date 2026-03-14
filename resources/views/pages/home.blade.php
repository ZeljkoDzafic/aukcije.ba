@extends('layouts.guest')

@section('title', 'Aukcije.ba - Licitiraj. Pobijedi. Uživaj.')
@section('meta')
    <x-seo-meta
        title="Aukcije.ba - Licitiraj. Pobijedi. Uživaj."
        description="Regionalna aukcijska platforma sa escrow zaštitom, verified seller signalima i real-time licitiranjem."
    />
@endsection

@section('content')
@php
    $marketSignals = [
        ['label' => 'Aktivnih aukcija', 'value' => '1.240', 'tone' => 'text-slate-950'],
        ['label' => 'Registriranih korisnika', 'value' => '18.500', 'tone' => 'text-trust-700'],
        ['label' => 'Pozitivnih ocjena', 'value' => '97.8%', 'tone' => 'text-emerald-700'],
    ];

    $operatingLanes = [
        [
            'eyebrow' => 'Za kupce',
            'title' => 'Prati najtraženije aukcije i uđi u licitaciju bez lutanja po platformi.',
            'body' => 'Pametnije kartice, seller signali, live sekcije i pregled hitnosti olakšavaju odluku prije prvog bida.',
            'href' => route('auctions.index'),
            'cta' => 'Istraži aukcije',
            'tone' => 'from-white to-slate-50',
        ],
        [
            'eyebrow' => 'Za prodavače',
            'title' => 'Objava aukcije, seller profil i trust elementi sada mogu nositi ozbiljniji sadržaj.',
            'body' => 'WYSIWYG opis aukcije i jasniji market entry pointi podižu kvalitet listinga i konverziju prema prodaji.',
            'href' => route('register'),
            'cta' => 'Postani seller',
            'tone' => 'from-amber-50 to-white',
        ],
    ];
@endphp

<section class="overflow-hidden bg-[radial-gradient(circle_at_top_left,_rgba(14,165,233,0.14),_transparent_30%),radial-gradient(circle_at_bottom_right,_rgba(245,158,11,0.18),_transparent_28%),linear-gradient(180deg,#fff7ed_0%,#ffffff_36%,#f8fafc_100%)]">
    <div class="mx-auto max-w-7xl px-4 pb-8 pt-14 sm:px-6 lg:px-8 lg:pb-10 lg:pt-20">
        <div class="grid gap-10 lg:grid-cols-[1.12fr_0.88fr] lg:items-start">
            <div class="space-y-8">
                <div class="space-y-5">
                    <div class="flex flex-wrap items-center gap-3">
                        <x-badge variant="trust">Escrow marketplace</x-badge>
                        <span class="rounded-full border border-slate-200 bg-white/80 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-slate-600">Buyer focus + seller studio</span>
                    </div>
                    <h1 class="max-w-3xl text-4xl font-semibold tracking-tight text-slate-950 sm:text-5xl lg:text-6xl">Marketplace za ozbiljne aukcije, sa jačim seller signalima i bržim putem do odluke.</h1>
                    <p class="max-w-2xl text-lg leading-8 text-slate-600">Aukcije.ba sada jasnije vodi i kupce i prodavače: live sekcije, jači trust sloj, sadržajno bogatiji listingi i čišći ulazi u kategorije, pomoć i seller ekosistem.</p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <x-button :href="route('auctions.index')">Pregledaj aukcije</x-button>
                    <x-button variant="ghost" :href="route('register')">Objavi prvu aukciju</x-button>
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                    @foreach ($marketSignals as $signal)
                        <div class="rounded-[1.5rem] border border-white/70 bg-white/80 px-5 py-5 shadow-sm shadow-slate-200/50 backdrop-blur">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ $signal['label'] }}</p>
                            <p class="mt-3 text-3xl font-semibold {{ $signal['tone'] }}">{{ $signal['value'] }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="market-sheen rounded-[1.75rem] border border-slate-200 bg-white/80 p-5 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-500">Popularne pretrage</p>
                        <a href="{{ route('search') }}" class="text-sm font-medium text-trust-700 transition hover:text-trust-800">Napredna pretraga</a>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach ($popularSearches as $term)
                            <a href="{{ route('search', ['q' => $term]) }}" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 transition hover:border-trust-300 hover:bg-trust-50 hover:text-trust-700">{{ $term }}</a>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="space-y-5">
                <div class="market-sheen rounded-[2rem] border border-slate-200/80 bg-[linear-gradient(135deg,#020617_0%,#0f172a_48%,#3b1d12_100%)] p-6 text-white shadow-2xl shadow-slate-300/40">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Market pulse</p>
                            <h2 class="mt-3 text-3xl font-semibold">{{ $featuredAuction['title'] }}</h2>
                            <p class="mt-3 max-w-md text-sm leading-7 text-slate-300">Hero listing koji pokazuje hitnost, vizual, trenutnu cijenu i brz ulaz u detalj aukcije bez suvišnog skrolanja.</p>
                        </div>
                        <x-badge variant="warning">Uskoro završava</x-badge>
                    </div>

                    <a href="{{ route('auctions.show', ['auction' => $featuredAuction['id']]) }}" class="mt-6 block overflow-hidden rounded-[1.75rem] border border-white/10 bg-gradient-to-br from-slate-800 via-slate-900 to-amber-950">
                        @if ($featuredAuction['image_url'])
                            <img src="{{ $featuredAuction['image_url'] }}" alt="{{ $featuredAuction['title'] }}" class="h-80 w-full object-cover opacity-90" />
                        @else
                            <div class="flex h-80 items-center justify-center text-sm font-medium text-slate-400">Preview aukcije</div>
                        @endif
                    </a>

                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-[1.5rem] bg-white/5 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Trenutna cijena</p>
                            <p class="mt-2 text-3xl font-semibold">{{ number_format((float) $featuredAuction['price'], 2, ',', '.') }} BAM</p>
                        </div>
                        <div class="rounded-[1.5rem] bg-white/5 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Vrijeme do kraja</p>
                            <p class="mt-2 text-3xl font-semibold text-amber-300">{{ $featuredAuction['time'] }}</p>
                        </div>
                    </div>

                    <div class="mt-4 grid gap-3 sm:grid-cols-3">
                        <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-300">Verified seller signali</div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-300">Escrow zaštita kupovine</div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-300">Real-time bidding status</div>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="market-sheen rounded-[1.75rem] border border-slate-200 bg-[linear-gradient(180deg,#ffffff_0%,#f8fafc_100%)] p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Najtraženija kategorija</p>
                        <h3 class="mt-3 text-2xl font-semibold text-slate-950">{{ $trendingCategories[0]['name'] ?? 'Elektronika' }}</h3>
                        <p class="mt-3 text-sm leading-7 text-slate-600">{{ $trendingCategories[0]['count'] ?? 0 }} aktivnih aukcija sa najjačim interesom kupaca.</p>
                        <a href="{{ route('categories.index') }}" class="mt-4 inline-flex text-sm font-medium text-trust-700">Otvori kategorije</a>
                    </div>
                    <div class="market-sheen rounded-[1.75rem] border border-slate-200 bg-[linear-gradient(180deg,#fffef7_0%,#ffffff_100%)] p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Seller discovery</p>
                        <h3 class="mt-3 text-2xl font-semibold text-slate-950">Profil prodavača više nije sporedna stvar.</h3>
                        <p class="mt-3 text-sm leading-7 text-slate-600">Javni seller directory, trust score i reputacija dobijaju jači ulaz sa početne stranice.</p>
                        <a href="{{ route('sellers.index') }}" class="mt-4 inline-flex text-sm font-medium text-trust-700">Pregledaj prodavače</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
    <div class="grid gap-6 lg:grid-cols-2">
        @foreach ($operatingLanes as $lane)
            <div class="market-sheen rounded-[2rem] border border-slate-200 bg-gradient-to-br {{ $lane['tone'] }} p-7 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ $lane['eyebrow'] }}</p>
                <h2 class="mt-3 max-w-xl text-3xl font-semibold text-slate-950">{{ $lane['title'] }}</h2>
                <p class="mt-4 max-w-2xl text-slate-600">{{ $lane['body'] }}</p>
                <a href="{{ $lane['href'] }}" class="mt-6 inline-flex rounded-full border border-slate-300 px-5 py-3 text-sm font-medium text-slate-900 transition hover:border-slate-900 hover:bg-slate-900 hover:text-white">{{ $lane['cta'] }}</a>
            </div>
        @endforeach
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
    <div class="market-sheen rounded-[2rem] border border-slate-200 bg-[linear-gradient(135deg,#0f172a_0%,#0b1220_50%,#10314c_100%)] px-6 py-7 text-white shadow-xl sm:px-8">
        <div class="grid gap-8 xl:grid-cols-[1fr_0.9fr] xl:items-end">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Search-first marketplace</p>
                <h2 class="mt-3 text-3xl font-semibold">Prvo pronađi segment tržišta, pa tek onda uđi u aukciju.</h2>
                <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-300">Lokalni marketplace korisnici očekuju da već sa početne stranice mogu birati između upita, kategorije i seller discovery toka. Zato je pretraga sada bliže vrhu iskustva.</p>
            </div>

            <form action="{{ route('search') }}" method="GET" class="grid gap-3 rounded-[1.75rem] border border-white/10 bg-white/5 p-4 sm:grid-cols-[1.2fr_0.8fr_auto]">
                <input name="q" type="search" class="rounded-2xl border border-white/10 bg-white px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:border-trust-400 focus:outline-none focus:ring-2 focus:ring-trust-100" placeholder="Pretraži aukcije, npr. BMW felge, Rolex, Leica..." />
                <select name="category" class="rounded-2xl border border-white/10 bg-white px-4 py-3 text-slate-900 focus:border-trust-400 focus:outline-none focus:ring-2 focus:ring-trust-100">
                    <option value="">Sve kategorije</option>
                    @foreach ($categoryHighlights as $category)
                        <option value="{{ $category['slug'] }}">{{ $category['name'] }}</option>
                    @endforeach
                </select>
                <x-button type="submit">Pokreni pretragu</x-button>
            </form>
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    @livewire('homepage-sections', ['sections' => ['featured', 'most_watched', 'ending_soon', 'new_arrivals']])
</section>

<section class="bg-[linear-gradient(180deg,#ffffff_0%,#f8fafc_100%)]">
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Explore marketplace</p>
                <h2 class="mt-2 text-3xl font-semibold text-slate-900">Brzi ulazi u kategorije i seller ekonomiju</h2>
            </div>
            <a href="{{ route('categories.index') }}" class="link">Sve kategorije</a>
        </div>

        <div class="mt-8 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($categoryHighlights as $category)
                <div class="market-sheen rounded-[1.75rem] border border-slate-200 bg-gradient-to-br from-white to-slate-50 p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Kategorija</p>
                    <a href="{{ route('categories.show', ['category' => $category['slug']]) }}" class="mt-3 block text-2xl font-semibold text-slate-900 transition hover:text-trust-700">{{ $category['name'] }}</a>
                    <p class="mt-3 text-sm leading-7 text-slate-600">{{ $category['count'] }} aktivnih aukcija i prostor za više kvalitetnih seller listinga.</p>
                    <a href="{{ route('categories.show', ['category' => $category['slug']]) }}" class="mt-5 inline-flex text-sm font-medium text-trust-700">Otvori segment</a>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
        <div class="market-sheen rounded-[2rem] border border-slate-200 bg-[linear-gradient(135deg,#0f172a_0%,#111827_46%,#1f2937_100%)] p-8 text-white">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Kako platforma sada radi</p>
            <h2 class="mt-3 max-w-2xl text-3xl font-semibold">Od landing stranice do licitacije i seller studija, iskustvo je organizovano oko odluke.</h2>
            <div class="mt-8 grid gap-4 md:grid-cols-3">
                <div class="rounded-[1.5rem] border border-white/10 bg-white/5 p-5">
                    <p class="text-sm font-semibold text-amber-300">01</p>
                    <h3 class="mt-3 text-xl font-semibold">Otkrivanje</h3>
                    <p class="mt-3 text-sm leading-7 text-slate-300">Kategorije, najpraćenije aukcije i seller discovery daju jasniji pregled tržišta.</p>
                </div>
                <div class="rounded-[1.5rem] border border-white/10 bg-white/5 p-5">
                    <p class="text-sm font-semibold text-amber-300">02</p>
                    <h3 class="mt-3 text-xl font-semibold">Evaluacija</h3>
                    <p class="mt-3 text-sm leading-7 text-slate-300">Trust signali, hitnost, bid aktivnost i seller kontekst su bliže samoj odluci.</p>
                </div>
                <div class="rounded-[1.5rem] border border-white/10 bg-white/5 p-5">
                    <p class="text-sm font-semibold text-amber-300">03</p>
                    <h3 class="mt-3 text-xl font-semibold">Konverzija</h3>
                    <p class="mt-3 text-sm leading-7 text-slate-300">Jasni CTA-i vode ka licitaciji, watchlisti, seller onboardingu i sadržajnim stranicama.</p>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="market-sheen rounded-[1.75rem] border border-slate-200 bg-[linear-gradient(180deg,#ffffff_0%,#f8fafc_100%)] p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Najtraženije kategorije</p>
                <div class="mt-5 space-y-3">
                    @foreach ($trendingCategories as $category)
                        <a href="{{ route('categories.show', ['category' => $category['slug']]) }}" class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-4 transition hover:bg-slate-100">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $category['name'] }}</p>
                                <p class="text-sm text-slate-500">{{ $category['count'] }} aktivnih aukcija</p>
                            </div>
                            <span class="text-sm font-medium text-trust-700">Otvori</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="market-sheen rounded-[1.75rem] border border-slate-200 bg-[linear-gradient(180deg,#fffef7_0%,#ffffff_100%)] p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Trust layer</p>
                <div class="mt-5 grid gap-3">
                    <a href="{{ route('help.index') }}" class="rounded-2xl bg-emerald-50 px-4 py-4 text-sm font-medium text-emerald-900">Centar pomoći za kupce i prodavače</a>
                    <a href="{{ route('content.terms') }}" class="rounded-2xl bg-slate-50 px-4 py-4 text-sm font-medium text-slate-800">Pravila kupovine i prodaje</a>
                    <a href="{{ route('news.index') }}" class="rounded-2xl bg-amber-50 px-4 py-4 text-sm font-medium text-amber-900">Novosti, promjene i obavijesti platforme</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bg-[linear-gradient(180deg,#fffef7_0%,#ffffff_100%)]">
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Vijesti i obavijesti</p>
                <h2 class="mt-2 text-3xl font-semibold text-slate-900">Sadržaj koji objašnjava šta se dešava na platformi</h2>
            </div>
            <a href="{{ route('news.index') }}" class="link">Sve vijesti</a>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-3">
            @foreach ($latestNews as $article)
                <div class="market-sheen rounded-[1.75rem] border border-slate-200 bg-gradient-to-br from-white to-slate-50 p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <p class="text-sm text-slate-500">{{ optional($article['published_at'] ?? null)->format('d.m.Y.') ?? optional($article->published_at)->format('d.m.Y.') }}</p>
                    <h3 class="mt-3 text-xl font-semibold text-slate-900">{{ $article['title'] ?? $article->title }}</h3>
                    <p class="mt-3 text-slate-600">{{ $article['excerpt'] ?? $article->excerpt }}</p>
                    <a href="{{ route('news.show', ['article' => $article['slug'] ?? $article->slug]) }}" class="mt-5 inline-flex text-sm font-medium text-trust-700">Pročitaj više</a>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="flex items-end justify-between gap-4">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Iskustva korisnika</p>
            <h2 class="mt-2 text-3xl font-semibold text-slate-900">Platforma mora izgledati kao mjesto za stvarnu trgovinu, ne demo ekran.</h2>
        </div>
        <a href="{{ route('content.show', ['slug' => 'ocjene-i-saradnja']) }}" class="link">Kako rade ocjene</a>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        @foreach ([
            ['name' => 'Kupac iz Sarajeva', 'text' => 'Kad odmah vidim seller signal, hitnost i status aukcije, mnogo brže odlučujem da li ulazim u bidding.'],
            ['name' => 'Prodavač iz Mostara', 'text' => 'Opis aukcije više može izgledati profesionalno, pa listing djeluje ozbiljnije i informativnije.'],
            ['name' => 'Verifikovani prodavač', 'text' => 'Bolji homepage i marketplace segmenti znače da kupac lakše dođe do mene i razumije zašto mi može vjerovati.'],
        ] as $quote)
            <div class="market-sheen rounded-[1.75rem] border border-slate-200 bg-[linear-gradient(180deg,#ffffff_0%,#f8fafc_100%)] p-6 shadow-sm">
                <p class="text-lg leading-8 text-slate-700">“{{ $quote['text'] }}”</p>
                <p class="mt-5 text-sm font-semibold uppercase tracking-[0.18em] text-slate-500">{{ $quote['name'] }}</p>
            </div>
        @endforeach
    </div>
</section>
@endsection
