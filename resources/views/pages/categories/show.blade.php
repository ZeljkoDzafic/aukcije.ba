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
<section class="mx-auto max-w-5xl px-4 py-12 sm:px-6 lg:px-8">
    <div class="space-y-8">
        <x-card class="space-y-4">
            <x-badge variant="trust">Kategorija</x-badge>
            <h1 class="text-3xl font-semibold text-slate-900">{{ $category->name }}</h1>
            <p class="text-slate-600">{{ $category->description }}</p>
            <p class="text-sm text-slate-500">{{ $category->auctions_count }} aukcija u ovoj kategoriji</p>

            @if (! empty($category->children) && $category->children->isNotEmpty())
                <div class="rounded-2xl bg-slate-50 p-4">
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
        </x-card>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($auctions as $auction)
                <x-auction-card :title="$auction['title']" :category="$auction['category']" :price="$auction['price']" :bids="$auction['bids']" :watchers="$auction['watchers']" :location="$auction['location']" :time="$auction['time']" :image-url="$auction['image_url'] ?? null" :href="route('auctions.show', ['auction' => $auction['id']])" />
            @endforeach
        </div>
    </div>
</section>
@endsection
