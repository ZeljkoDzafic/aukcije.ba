<div class="grid gap-8 lg:grid-cols-[18rem_minmax(0,1fr)]">
    <aside class="space-y-4">
        <x-card class="space-y-4">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Livewire filteri</p>
                <h1 class="mt-2 text-2xl font-semibold text-slate-900">Pretraga aukcija</h1>
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
    </aside>

    <div class="space-y-6">
        <div class="flex flex-col gap-4 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Rezultati</p>
                <p class="mt-2 text-lg font-semibold text-slate-900">{{ method_exists($results, 'total') ? $results->total() : $results->count() }} aktivnih aukcija</p>
            </div>
            <x-badge variant="trust">Livewire spreman za backend query</x-badge>
        </div>

        @if ($results->isEmpty())
            <x-alert variant="warning">Nema aukcija za odabrane filtere.</x-alert>
        @else
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($results as $auction)
                    <x-auction-card :title="$auction['title']" :category="$auction['category']" :price="$auction['price']" :bids="$auction['bids']" :watchers="$auction['watchers']" :location="$auction['location']" :time="$auction['time']" :image-url="$auction['image_url'] ?? null" :href="route('auctions.show', ['auction' => $auction['id']])" />
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
