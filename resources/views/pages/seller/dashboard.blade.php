@extends('layouts.seller')

@section('title', 'Seller Dashboard')
@section('seller_heading', 'Seller Dashboard')

@section('content')
<section class="space-y-8">
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach ($stats as $stat)
            <x-card>
                <p class="text-sm text-slate-500">{{ $stat['label'] }}</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $stat['value'] }}</p>
            </x-card>
        @endforeach
    </div>

    <div class="grid gap-8 xl:grid-cols-[1.1fr_0.9fr]">
        <x-card class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-semibold text-slate-900">Aktivne aukcije</h2>
                <x-badge variant="trust">{{ strtoupper($seller->getTier()['name'] ?? 'free') }} tier</x-badge>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                @foreach ($activeAuctions as $auction)
                    <x-auction-card
                        :title="$auction['title']"
                        :category="$auction['category']"
                        :price="$auction['price']"
                        :bids="$auction['bids']"
                        :watchers="$auction['watchers']"
                        :location="$auction['location']"
                        :time="$auction['time']"
                        :image-url="$auction['image_url']"
                        :href="route('seller.auctions.edit', ['auction' => $auction['id']])"
                    />
                @endforeach
            </div>
        </x-card>

        <div class="space-y-6">
            <x-card class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-slate-900">Seller command center</h2>
                    <a href="{{ route('notifications.index') }}" class="link">Sve obavijesti</a>
                </div>
                <div class="grid gap-3 sm:grid-cols-3">
                    @foreach ($sellerCommandCenter as $item)
                        <a href="{{ $item['href'] }}" class="rounded-2xl bg-slate-50 px-4 py-4 transition hover:bg-slate-100">
                            <p class="text-sm text-slate-500">{{ $item['label'] }}</p>
                            <p class="mt-1 text-xl font-semibold text-slate-900">{{ $item['value'] }}</p>
                        </a>
                    @endforeach
                </div>
            </x-card>

            <x-card class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-slate-900">Operativni prioriteti</h2>
                    <a href="{{ route('seller.orders.index') }}" class="link">Seller narudžbe</a>
                </div>
                <div class="grid gap-3 sm:grid-cols-3 xl:grid-cols-8">
                    @foreach ($sellerPriorityQueue as $item)
                        <a href="{{ $item['href'] }}" class="rounded-2xl border border-slate-200 px-4 py-4 transition hover:border-slate-300 hover:bg-slate-50">
                            <p class="text-sm text-slate-500">{{ $item['label'] }}</p>
                            <p class="mt-1 text-xl font-semibold text-slate-900">{{ $item['value'] }}</p>
                        </a>
                    @endforeach
                </div>
            </x-card>

            <x-card class="space-y-4">
                <h2 class="text-xl font-semibold text-slate-900">Narudžbe za slanje</h2>
                <x-alert variant="warning">{{ $shippingQueue->count() }} narudžbe traže operativnu pažnju.</x-alert>
                <div class="space-y-3 text-sm text-slate-700">
                    @foreach ($shippingQueue as $entry)
                        <div class="rounded-2xl bg-slate-50 px-4 py-3">
                            Order #{{ $entry['id'] }} · {{ $entry['title'] }} · {{ str_replace('_', ' ', $entry['status']) }}
                        </div>
                    @endforeach
                </div>
            </x-card>

            <x-card class="space-y-4">
                <h2 class="text-xl font-semibold text-slate-900">Javni seller profil</h2>
                <p class="text-sm text-slate-600">Kupci mogu vidjeti tvoju reputaciju, aktivne aukcije i ocjene na javnom profilu.</p>
                <x-button variant="ghost" :href="route('sellers.show', ['user' => $seller->id])">Otvori javni profil</x-button>
            </x-card>
        </div>
    </div>
</section>
@endsection
