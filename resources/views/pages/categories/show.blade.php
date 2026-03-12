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
                ['@type' => 'ListItem', 'position' => 2, 'name' => $category->name, 'item' => url()->current()],
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
        </x-card>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($auctions as $auction)
                <x-auction-card :title="$auction['title']" :category="$auction['category']" :price="$auction['price']" :bids="$auction['bids']" :watchers="$auction['watchers']" :location="$auction['location']" :time="$auction['time']" :href="route('auctions.show', ['auction' => $auction['id']])" />
            @endforeach
        </div>
    </div>
</section>
@endsection
