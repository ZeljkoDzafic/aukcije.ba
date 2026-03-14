@extends('layouts.guest')

@php
    $category = $category ?? (object) ['name' => 'Elektronika', 'slug' => 'elektronika', 'description' => 'Pregled tržišta i aktivnih aukcija u kategoriji Elektronika.', 'auctions_count' => 3];
    $auctions = $auctions ?? collect([
        ['id' => 1, 'title' => 'Samsung Galaxy S24 Ultra', 'category' => 'Elektronika', 'price' => 1250.00, 'bids' => 14, 'watchers' => 32, 'location' => 'Sarajevo', 'time' => '2d 04h'],
        ['id' => 2, 'title' => 'Sony WH-1000XM5', 'category' => 'Elektronika', 'price' => 480.00, 'bids' => 11, 'watchers' => 17, 'location' => 'Banja Luka', 'time' => '5h 12m'],
        ['id' => 3, 'title' => 'Nintendo Switch OLED', 'category' => 'Elektronika', 'price' => 410.00, 'bids' => 8, 'watchers' => 29, 'location' => 'Zenica', 'time' => '9h 48m'],
    ]);
@endphp

@section('title', $category->name)
@section('meta')
    <x-seo-meta
        :title="$category->name.' - Aukcije.ba'"
        :description="$category->description"
    />
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Kategorije', 'item' => route('categories.index')],
                ...($category->parent_slug ? [['@type' => 'ListItem', 'position' => 2, 'name' => $category->parent_name, 'item' => route('categories.show', ['category' => $category->parent_slug])]] : []),
                ['@type' => 'ListItem', 'position' => $category->parent_slug ? 3 : 2, 'name' => $category->name, 'item' => url()->current()],
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endsection

@section('content')
<section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
    <div class="space-y-8">
        <div class="market-sheen rounded-[2rem] border border-slate-200 bg-[radial-gradient(circle_at_top_left,_rgba(14,165,233,0.14),_transparent_28%),radial-gradient(circle_at_bottom_right,_rgba(245,158,11,0.15),_transparent_26%),linear-gradient(180deg,#ffffff_0%,#f8fafc_100%)] p-6 shadow-sm sm:p-8">
            <div class="grid gap-8 xl:grid-cols-[1fr_0.9fr] xl:items-end">
                <div class="space-y-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <x-badge variant="trust">Kategorija</x-badge>
                        <x-badge variant="secondary">Marketplace segment</x-badge>
                    </div>
                    <h1 class="text-4xl font-semibold text-slate-900">{{ $category->name }}</h1>
                    <p class="max-w-3xl text-slate-600">{{ $category->description }}</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-[1.5rem] border border-white/70 bg-white/80 px-4 py-4 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Aukcija</p>
                        <p class="mt-2 text-3xl font-semibold text-slate-950">{{ $category->auctions_count }}</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-white/70 bg-white/80 px-4 py-4 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Fokus</p>
                        <p class="mt-2 text-lg font-semibold text-trust-700">Buyer discovery</p>
                    </div>
                </div>
            </div>

            @if (! empty($category->children) && $category->children->isNotEmpty())
                <div class="mt-6 rounded-2xl bg-white/80 p-4">
                    <p class="mb-3 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Podkategorije</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($category->children as $child)
                            <a href="{{ route('categories.show', ['category' => $child['slug']]) }}" class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 transition hover:border-trust-300 hover:text-trust-700">
                                {{ $child['name'] }}
                                <span class="ml-2 text-xs text-slate-400">{{ $child['auctions_count'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($auctions as $auction)
                <x-auction-card
                    :title="$auction['title']"
                    :category="$auction['category']"
                    :price="$auction['price']"
                    :bids="$auction['bids']"
                    :watchers="$auction['watchers']"
                    :location="$auction['location']"
                    :time="$auction['time']"
                    :image-url="$auction['image_url'] ?? null"
                    badge="Kategorija signal"
                    badge-variant="trust"
                    cta-label="Otvori aukciju"
                    :href="route('auctions.show', ['auction' => $auction['id']])"
                />
            @endforeach
        </div>
    </div>
</section>
@endsection
