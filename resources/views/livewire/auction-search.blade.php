@php
    $resultCount = method_exists($results, 'total') ? $results->total() : $results->count();
    $activeFilterCount = collect([$query, $category, $location, $priceMin, $priceMax, $condition])->filter(fn ($value) => $value !== '' && $value !== null)->count();
@endphp

<div class="space-y-8">
    <section class="market-sheen rounded-[2rem] border border-slate-200 bg-[radial-gradient(circle_at_top_left,_rgba(14,165,233,0.14),_transparent_32%),radial-gradient(circle_at_bottom_right,_rgba(245,158,11,0.14),_transparent_24%),linear-gradient(180deg,#fffef7_0%,#f8fafc_100%)] p-6 shadow-sm sm:p-8">
        <div class="grid gap-8 xl:grid-cols-[1.1fr_0.9fr] xl:items-end">
            <div class="space-y-4">
                <div class="flex flex-wrap items-center gap-3">
                    <x-badge variant="trust">Marketplace search</x-badge>
                    <span class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-slate-600">Brži ulaz u aukcije</span>
                </div>
                <div>
                    <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Aukcije.ba listing</p>
                    <h1 class="mt-2 text-3xl font-semibold text-slate-950 sm:text-4xl">Pretraži aukcije kao pravi marketplace, ne kao običan katalog.</h1>
                    <p class="mt-3 max-w-2xl text-slate-600">Filteri, hitnost, seller signal i tržišni snapshot su bliže samom rezultatu, tako da kupac brže procijeni gdje vrijedi ući u bidding.</p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-[1.5rem] border border-white/70 bg-white/85 px-4 py-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Ukupno rezultata</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-950">{{ $resultCount }}</p>
                </div>
                <div class="rounded-[1.5rem] border border-white/70 bg-white/85 px-4 py-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Aktivni filteri</p>
                    <p class="mt-2 text-3xl font-semibold text-trust-700">{{ $activeFilterCount }}</p>
                </div>
                <div class="rounded-[1.5rem] border border-white/70 bg-white/85 px-4 py-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Sortiranje</p>
                    <p class="mt-2 text-lg font-semibold text-slate-950">{{ match($sort) { 'newest' => 'Najnovije', 'most_bids' => 'Najviše bidova', 'price_low' => 'Najniža cijena', 'price_high' => 'Najviša cijena', default => 'Uskoro završava' } }}</p>
                </div>
            </div>
        </div>
    </section>

    <div class="grid gap-8 lg:grid-cols-[20rem_minmax(0,1fr)]">
        <aside class="space-y-4">
            <x-card class="market-sheen space-y-5 bg-[linear-gradient(180deg,#ffffff_0%,#f8fafc_100%)]">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Filteri</p>
                        <h2 class="mt-2 text-2xl font-semibold text-slate-900">Suzi marketplace</h2>
                    </div>
                    <button type="button" wire:click="resetFilters" class="text-sm font-medium text-trust-700 transition hover:text-trust-800">Reset</button>
                </div>

                <x-input wire:model.live.debounce.300ms="query" name="query" label="Pretraga" placeholder="iPhone, sat, gramofon" />
                <x-select wire:model.live="category" name="category" label="Kategorija" :options="$categoryOptions ?: ['' => 'Sve kategorije', 'elektronika' => 'Elektronika', 'audio' => 'Audio', 'foto-oprema' => 'Foto oprema']" />
                <x-input wire:model.live.debounce.300ms="location" name="location" label="Lokacija" placeholder="Sarajevo" />

                <div class="grid grid-cols-2 gap-3">
                    <x-input wire:model.live.debounce.400ms="priceMin" name="price_min" type="number" label="Cijena od" placeholder="0" />
                    <x-input wire:model.live.debounce.400ms="priceMax" name="price_max" type="number" label="Cijena do" placeholder="9999" />
                </div>

                <x-select wire:model.live="condition" name="condition" label="Stanje" :options="['' => 'Sve', 'new' => 'Novo', 'used' => 'Korišteno', 'refurbished' => 'Renovirano']" />
                <x-select wire:model.live="sort" name="sort" label="Sortiranje" :options="['ending_soon' => 'Uskoro završava', 'newest' => 'Najnovije', 'most_bids' => 'Najviše bidova', 'price_low' => 'Najniža cijena', 'price_high' => 'Najviša cijena']" />
            </x-card>

            <div class="market-sheen rounded-[1.75rem] border border-slate-200 bg-[linear-gradient(180deg,#fffef7_0%,#ffffff_100%)] p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Brzi ulazi</p>
                <div class="mt-4 grid gap-3">
                    <button type="button" wire:click="$set('sort', 'ending_soon')" class="rounded-2xl bg-amber-50 px-4 py-3 text-left text-sm font-medium text-amber-900">Uskoro završava</button>
                    <button type="button" wire:click="$set('sort', 'newest')" class="rounded-2xl bg-emerald-50 px-4 py-3 text-left text-sm font-medium text-emerald-900">Novo u ponudi</button>
                    <button type="button" wire:click="$set('sort', 'most_bids')" class="rounded-2xl bg-sky-50 px-4 py-3 text-left text-sm font-medium text-sky-900">Najviše nadmetanja</button>
                </div>
            </div>
        </aside>

        <div class="space-y-6">
            <div class="market-sheen flex flex-col gap-4 rounded-[1.75rem] border border-slate-200 bg-[linear-gradient(180deg,#ffffff_0%,#f8fafc_100%)] p-5 shadow-sm lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Rezultati</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $resultCount }} aktivnih aukcija</p>
                    <p class="mt-1 text-sm text-slate-600">Buyer-first listing ritam sa jačim signalima o hitnosti, interesu i seller kontekstu.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @if ($query !== '')
                        <span class="rounded-full bg-slate-100 px-3 py-2 text-sm text-slate-700">Upit: {{ $query }}</span>
                    @endif
                    @if ($location !== '')
                        <span class="rounded-full bg-slate-100 px-3 py-2 text-sm text-slate-700">Lokacija: {{ $location }}</span>
                    @endif
                    @if ($category !== '')
                        <span class="rounded-full bg-slate-100 px-3 py-2 text-sm text-slate-700">Kategorija: {{ $categoryOptions[$category] ?? $category }}</span>
                    @endif
                </div>
            </div>

            @if ($results->isEmpty())
                <x-alert variant="warning">Nema aukcija za odabrane filtere.</x-alert>
            @else
                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($results as $auction)
                        <x-auction-card
                            :title="$auction['title']"
                            :category="$auction['category']"
                            :price="$auction['price']"
                            :bids="$auction['bids']"
                            :watchers="$auction['watchers']"
                            :location="$auction['location']"
                            :time="$auction['time']"
                            :seller="$auction['seller'] ?? null"
                            :image-url="$auction['image_url'] ?? null"
                            :srcset="$auction['image_srcset'] ?? null"
                            :blurhash="$auction['image_blurhash'] ?? null"
                            badge="Aktivna aukcija"
                            badge-variant="trust"
                            cta-label="Otvori aukciju"
                            :href="route('auctions.show', ['auction' => $auction['id']])"
                        />
                    @endforeach
                </div>

                @if (method_exists($results, 'links'))
                    <div class="mt-6">
                        {{ $results->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
