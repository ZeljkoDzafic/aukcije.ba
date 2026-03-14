@props([
    'title' => 'Samsung Galaxy S24 Ultra',
    'category' => 'Elektronika',
    'price' => 1250,
    'bids' => 14,
    'watchers' => 32,
    'location' => 'Sarajevo',
    'time' => '2d 04h',
    'href' => null,
    'imageUrl' => null,
    'blurhash' => null,
    'srcset' => null,
    'badge' => 'Uskoro završava',
    'badgeVariant' => 'warning',
    'seller' => null,
    'ctaLabel' => 'Otvori aukciju',
])

<article class="auction-card group relative rounded-[1.75rem] border border-slate-200/80 bg-white shadow-sm shadow-slate-200/60 transition duration-300 hover:-translate-y-1 hover:border-slate-300 hover:shadow-xl">
    @if ($href)
        <a href="{{ $href }}" class="absolute inset-0 z-10 rounded-[1.25rem]" aria-label="Otvori aukciju {{ $title }}"></a>
    @endif

    <div class="auction-card-image rounded-t-[1.75rem]">
        @if ($imageUrl)
            <x-blurhash-placeholder
                :src="$imageUrl"
                :srcset="$srcset"
                sizes="(min-width: 1280px) 25vw, (min-width: 768px) 33vw, 100vw"
                :blurhash="$blurhash"
                :alt="$title"
                class="h-full w-full"
            />
        @else
            <div class="flex h-full items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200 text-sm font-medium text-slate-500">Aukcija</div>
        @endif
        <div class="absolute inset-x-0 top-0 flex items-center justify-between px-4 py-4">
            <x-badge :variant="$badgeVariant">{{ $badge }}</x-badge>
            <div class="rounded-full border border-white/30 bg-slate-950/55 px-3 py-1 text-xs font-medium text-white backdrop-blur">
                {{ $watchers }} prati
            </div>
        </div>
    </div>

    <div class="relative z-20 space-y-4 p-5">
        <div class="space-y-2">
            <div class="flex items-center justify-between gap-3">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ $category }}</p>
                <span class="text-xs text-slate-500">{{ $location }}</span>
            </div>
            <h3 class="line-clamp-2 text-lg font-semibold leading-tight text-slate-900">{{ $title }}</h3>
        </div>

        <div class="grid grid-cols-[1fr_auto] items-end gap-3">
            <x-price-display :amount="$price" />
            <div class="rounded-2xl bg-amber-50 px-3 py-2 text-right">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-amber-700">Preostalo</p>
                <p class="countdown text-sm font-semibold text-amber-900">{{ $time }}</p>
            </div>
        </div>

        <div class="space-y-2">
            <div class="flex items-center justify-between text-sm text-slate-600">
                <span>{{ $bids }} bidova</span>
                <span class="font-medium">{{ max($bids + 1, 2) }} aktivna učesnika</span>
            </div>
            <x-progress-bar :value="78" />
        </div>

        @if ($seller)
            <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3 text-sm">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Prodavač</p>
                    <p class="font-medium text-slate-900">{{ $seller }}</p>
                </div>
                <div class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                    Seller signal
                </div>
            </div>
        @endif

        <div class="flex gap-3">
            <x-button class="pointer-events-none flex-1">{{ $ctaLabel }}</x-button>
            <x-button variant="ghost" class="flex-1">Prati</x-button>
        </div>
    </div>
</article>
