<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Praćenje</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-900">Moje praćene aukcije</h1>
        </div>
        <div class="flex flex-wrap gap-3 text-sm">
            @foreach (['active' => 'Aktivne', 'ended' => 'Završene', 'all' => 'Sve'] as $key => $label)
                <button type="button" wire:click="$set('filter', '{{ $key }}')" class="rounded-full px-4 py-2 {{ $filter === $key ? 'bg-trust-50 text-trust-700' : 'bg-slate-100 text-slate-600' }}">{{ $label }}</button>
            @endforeach
        </div>
    </div>

    @if ($results->isEmpty())
        <x-alert variant="info">Nemaš praćenih aukcija. Započni pretragu i dodaj artikle koji te zanimaju.</x-alert>
    @else
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($results as $auction)
                <div class="space-y-3">
                    <x-auction-card :title="$auction['title']" :category="$auction['category']" :price="$auction['price']" :bids="$auction['bids']" :watchers="$auction['watchers']" :location="$auction['location']" :time="$auction['time']" :image-url="$auction['image_url'] ?? null" :srcset="$auction['image_srcset'] ?? null" :blurhash="$auction['image_blurhash'] ?? null" :badge="$auction['badge'] ?? 'Praćena aukcija'" :href="route('auctions.show', ['auction' => $auction['id']])" />
                    <x-button variant="ghost" class="w-full" wire:click="remove('{{ $auction['id'] }}')">Ukloni iz praćenja</x-button>
                </div>
            @endforeach
        </div>

        @if (method_exists($results, 'links'))
            <div class="mt-6">
                {{ $results->links() }}
            </div>
        @endif
    @endif
</div>
