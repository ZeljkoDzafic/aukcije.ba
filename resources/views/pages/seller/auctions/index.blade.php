@extends('layouts.seller')

@section('title', 'Moje aukcije')
@section('seller_heading', 'Moje aukcije')

@section('content')
<section class="space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <p class="text-sm text-slate-500">Pregled svih draft, aktivnih i završenih listinga.</p>
        </div>
        <x-button :href="route('seller.auctions.create')">Nova aukcija</x-button>
    </div>

    @if ($readinessItems->isNotEmpty())
        <x-card class="space-y-4">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-slate-900">Seller readiness</h2>
                    <p class="mt-1 text-sm text-slate-600">Brze korekcije koje najviše utiču na prodaju, povjerenje i kvalitet listinga.</p>
                </div>
                <x-badge variant="warning">{{ $readinessItems->count() }} signala</x-badge>
            </div>

            <div class="grid gap-3 lg:grid-cols-2">
                @foreach ($readinessItems as $item)
                    <div class="rounded-3xl border border-amber-200 bg-amber-50 px-5 py-4">
                        <p class="font-semibold text-slate-900">{{ $item['title'] }}</p>
                        <p class="mt-2 text-sm text-slate-700">{{ $item['hint'] }}</p>
                        <div class="mt-4">
                            <x-button variant="ghost" :href="$item['href']">{{ $item['cta'] }}</x-button>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-card>
    @endif

    @if ($auctions->isEmpty())
        <x-alert variant="info">Još nemaš aukcija. Kreiraj prvi listing i dodaj kvalitetne slike prije objave.</x-alert>
    @else
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($auctions as $auction)
                <div class="space-y-3">
                    <x-auction-card
                        :title="$auction['title']"
                        :category="$auction['category']"
                        :price="$auction['price']"
                        :bids="$auction['bids']"
                        :watchers="$auction['watchers']"
                        :location="$auction['location']"
                        :time="$auction['time']"
                        :image-url="$auction['image_url']"
                        :badge="str_replace('_', ' ', $auction['status'])"
                        :href="route('seller.auctions.edit', ['auction' => $auction['id']])"
                    />
                    <div class="flex gap-3">
                        <x-button variant="secondary" class="flex-1" :href="route('seller.auctions.edit', ['auction' => $auction['id']])">Uredi</x-button>
                        <x-button variant="ghost" class="flex-1" :href="route('auctions.show', ['auction' => $auction['id']])">Javni pregled</x-button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</section>
@endsection
