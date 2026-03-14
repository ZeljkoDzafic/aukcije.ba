@extends('layouts.app')

@section('title', 'Kontrolna tabla')

@section('content')
<section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="space-y-8">
        <div class="panel-hero">
            <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
                <div class="space-y-4">
                    <p class="panel-kicker">Buyer command center</p>
                    <div>
                        <h1 class="text-3xl font-semibold text-white sm:text-4xl">Sve što pratiš, licitiraš i kupuješ na jednom mjestu.</h1>
                        <p class="mt-3 max-w-2xl text-sm text-slate-200 sm:text-base">Panel vodi prema narednoj odluci: gdje vodiš, gdje si nadmašen, šta čeka uplatu i šta vrijedi ponovo otvoriti.</p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <x-button :href="route('auctions.index')">Pregledaj aukcije</x-button>
                        <x-button variant="ghost" :href="route('watchlist.index')" class="border-white/20 bg-white/10 text-white hover:bg-white/15">Praćene aukcije</x-button>
                    </div>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    @foreach ($bidSummary as $item)
                        <div class="panel-metric">
                            <p class="text-xs uppercase tracking-[0.24em] text-slate-300">{{ $item['label'] }}</p>
                            <p class="mt-2 text-2xl font-semibold text-white">{{ $item['value'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($stats as $stat)
                <x-card class="panel-shell-soft market-sheen">
                    <p class="text-sm text-slate-500">{{ $stat['label'] }}</p>
                    <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $stat['value'] }}</p>
                </x-card>
            @endforeach
        </div>

        <div class="grid gap-8 xl:grid-cols-[1.1fr_0.9fr]">
            <x-card class="panel-shell space-y-4">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-semibold text-slate-900">Moje licitacije</h1>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('bids.index') }}" class="link">Otvori sve</a>
                        <a href="{{ route('auctions.index') }}" class="link">Pregledaj aukcije</a>
                    </div>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach ($activeBids as $auction)
                        <x-auction-card :title="$auction['title']" :category="$auction['category']" :price="$auction['price']" :bids="$auction['bids']" :watchers="$auction['watchers']" :location="$auction['location']" :time="$auction['time']" :image-url="$auction['image_url'] ?? null" :href="route('auctions.show', ['auction' => $auction['id']])" />
                    @endforeach
                </div>
            </x-card>

            <div class="space-y-6">
                <x-card class="panel-shell space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-slate-900">Komandni centar</h2>
                        <a href="{{ route('notifications.index') }}" class="link">Sve obavijesti</a>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-3">
                        @foreach ($commandCenter as $item)
                            <a href="{{ $item['href'] }}" class="panel-subtle-card transition hover:bg-slate-100">
                                <p class="text-sm text-slate-500">{{ $item['label'] }}</p>
                                <p class="mt-1 text-xl font-semibold text-slate-900">{{ $item['value'] }}</p>
                            </a>
                        @endforeach
                    </div>
                </x-card>

                <x-card class="panel-shell space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-slate-900">Prioriteti</h2>
                        <a href="{{ route('orders.index') }}" class="link">Otvori narudžbe</a>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-3 xl:grid-cols-5">
                        @foreach ($priorityQueue as $item)
                            <a href="{{ $item['href'] }}" class="panel-shell-soft p-4 transition hover:-translate-y-0.5 hover:border-slate-300">
                                <p class="text-sm text-slate-500">{{ $item['label'] }}</p>
                                <p class="mt-1 text-xl font-semibold text-slate-900">{{ $item['value'] }}</p>
                            </a>
                        @endforeach
                    </div>
                </x-card>

                <x-card class="panel-shell space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-slate-900">Praćene aukcije</h2>
                        <a href="{{ route('watchlist.index') }}" class="link">Otvori sve</a>
                    </div>
                    <div class="space-y-3">
                        @foreach ($watchlistItems as $item)
                            <div class="panel-subtle-card text-sm text-slate-700">{{ $item }}</div>
                        @endforeach
                    </div>
                </x-card>

                <x-card class="panel-shell space-y-4">
                    <h2 class="text-xl font-semibold text-slate-900">Dobijene aukcije</h2>
                    <x-alert variant="warning">Imaš 1 narudžbu koja čeka uplatu. Rok za plaćanje: 48h.</x-alert>
                    <div class="space-y-3 text-sm text-slate-700">
                        @foreach ($orders as $order)
                            <div class="flex items-center justify-between rounded-[1.5rem] bg-slate-50 px-4 py-3">
                                <span>{{ $order['title'] }}</span>
                                <x-badge :variant="in_array($order['status'], ['paid', 'completed', 'shipped'], true) ? 'success' : 'warning'">
                                    {{ $order['status'] }}
                                </x-badge>
                            </div>
                        @endforeach
                    </div>
                </x-card>
            </div>
        </div>
    </div>
</section>
@endsection
