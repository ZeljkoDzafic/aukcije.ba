@extends('layouts.guest')

@section('title', $seller->name.' - Prodavač')
@section('meta')
    <x-seo-meta :title="$seller->name.' - Aukcije.ba'" description="Javni profil prodavača, reputacija, aktivne aukcije i ocjene kupaca." />
@endsection

@section('content')
<section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
    <div class="grid gap-8 xl:grid-cols-[0.85fr_1.15fr]">
        <div class="space-y-6">
            <x-card class="space-y-5">
                <div class="flex items-center gap-4">
                    <x-avatar :name="$seller->name" size="lg" />
                    <div>
                        <h1 class="text-3xl font-semibold text-slate-900">{{ $seller->name }}</h1>
                        <p class="text-slate-600">{{ ucfirst(method_exists($seller, 'roleSummary') ? $seller->roleSummary() : 'seller') }} · {{ $sellerStats['city'] }}</p>
                    </div>
                    <div class="ml-auto">
                        <x-seller-reputation-badge :seller="$seller" size="sm" />
                    </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="rounded-2xl bg-slate-50 px-4 py-3">
                        <p class="text-sm text-slate-500">Trust</p>
                        <p class="mt-1 text-xl font-semibold text-slate-900">{{ $sellerStats['trust'] }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3">
                        <p class="text-sm text-slate-500">Aktivne aukcije</p>
                        <p class="mt-1 text-xl font-semibold text-slate-900">{{ $sellerStats['active_auctions'] }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3">
                        <p class="text-sm text-slate-500">Završene prodaje</p>
                        <p class="mt-1 text-xl font-semibold text-slate-900">{{ $sellerStats['sold_orders'] }}</p>
                    </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl border border-slate-200 px-4 py-3">
                        <p class="text-sm text-slate-500">Prosječna ocjena</p>
                        <p class="mt-1 text-xl font-semibold text-slate-900">{{ $sellerStats['average_rating'] }}/5</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 px-4 py-3">
                        <p class="text-sm text-slate-500">Javne ocjene</p>
                        <p class="mt-1 text-xl font-semibold text-slate-900">{{ $sellerStats['ratings_count'] }}</p>
                    </div>
                </div>
            </x-card>

            <x-card class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-slate-900">Ocjene kupaca</h2>
                    <x-badge variant="trust">{{ $ratings->count() }} prikazano</x-badge>
                </div>
                <div class="space-y-3">
                    @forelse ($ratings as $rating)
                        <div class="rounded-2xl bg-slate-50 px-4 py-4">
                            <div class="flex items-center justify-between gap-3">
                                <p class="font-medium text-slate-900">{{ $rating->score }}/5</p>
                                <p class="text-sm text-slate-500">{{ $rating->created_at?->format('d.m.Y.') }}</p>
                            </div>
                            <p class="mt-2 text-sm text-slate-700">{{ $rating->comment ?: 'Korektna saradnja i uredna komunikacija.' }}</p>
                        </div>
                    @empty
                        <x-alert variant="info">Još nema javnih ocjena za ovog prodavača.</x-alert>
                    @endforelse
                </div>
            </x-card>
        </div>

        <div class="space-y-6">
            @if ($featuredAuctions->isNotEmpty())
                <x-card class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-semibold text-slate-900">Istaknuto iz seller ponude</h2>
                        <x-badge variant="trust">Storefront</x-badge>
                    </div>
                    <div class="grid gap-6 md:grid-cols-2">
                        @foreach ($featuredAuctions as $auction)
                            <x-auction-card
                                :title="$auction['title']"
                                :category="$auction['category']"
                                :price="$auction['price']"
                                :bids="$auction['bids']"
                                :watchers="$auction['watchers']"
                                :location="$auction['location']"
                                :time="$auction['time']"
                                :image-url="$auction['image_url']"
                                :href="route('auctions.show', ['auction' => $auction['id']])"
                            />
                        @endforeach
                    </div>
                </x-card>
            @endif

            @if ($categoryCollections->isNotEmpty())
                <x-card class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-slate-900">Kolekcije po kategorijama</h2>
                        <a href="{{ route('sellers.index') }}" class="link">Svi prodavači</a>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        @foreach ($categoryCollections as $collection)
                            <div class="rounded-2xl border border-slate-200 px-4 py-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ $collection['category'] }}</p>
                                <p class="mt-2 text-lg font-semibold text-slate-900">{{ $collection['count'] }} aktivnih aukcija</p>
                                <p class="mt-1 text-sm text-slate-600">Najviše pažnje trenutno dobija: {{ $collection['top_title'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </x-card>
            @endif

            @if ($endingSoonAuctions->isNotEmpty())
                <x-card class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-slate-900">Uskoro završava kod ovog prodavača</h2>
                        <x-badge variant="warning">Ne propusti završnicu</x-badge>
                    </div>
                    <div class="grid gap-6 md:grid-cols-2">
                        @foreach ($endingSoonAuctions as $auction)
                            <x-auction-card
                                :title="$auction['title']"
                                :category="$auction['category']"
                                :price="$auction['price']"
                                :bids="$auction['bids']"
                                :watchers="$auction['watchers']"
                                :location="$auction['location']"
                                :time="$auction['time']"
                                :image-url="$auction['image_url']"
                                :href="route('auctions.show', ['auction' => $auction['id']])"
                            />
                        @endforeach
                    </div>
                </x-card>
            @endif

            @if ($newlyListedAuctions->isNotEmpty())
                <x-card class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-slate-900">Novo dodano kod ovog prodavača</h2>
                        <x-badge variant="info">Svježe objave</x-badge>
                    </div>
                    <div class="grid gap-6 md:grid-cols-2">
                        @foreach ($newlyListedAuctions as $auction)
                            <x-auction-card
                                :title="$auction['title']"
                                :category="$auction['category']"
                                :price="$auction['price']"
                                :bids="$auction['bids']"
                                :watchers="$auction['watchers']"
                                :location="$auction['location']"
                                :time="$auction['time']"
                                :image-url="$auction['image_url']"
                                :href="route('auctions.show', ['auction' => $auction['id']])"
                            />
                        @endforeach
                    </div>
                </x-card>
            @endif

            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-semibold text-slate-900">Aktivne aukcije prodavača</h2>
                <a href="{{ route('auctions.index') }}" class="link">Sve aukcije</a>
            </div>
            <div class="grid gap-6 md:grid-cols-2">
                @forelse ($activeAuctions as $auction)
                    <x-auction-card
                        :title="$auction['title']"
                        :category="$auction['category']"
                        :price="$auction['price']"
                        :bids="$auction['bids']"
                        :watchers="$auction['watchers']"
                        :location="$auction['location']"
                        :time="$auction['time']"
                        :image-url="$auction['image_url']"
                        :href="route('auctions.show', ['auction' => $auction['id']])"
                    />
                @empty
                    <x-alert variant="info">Prodavač trenutno nema aktivnih aukcija.</x-alert>
                @endforelse
            </div>
        </div>
    </div>
</section>
@endsection
