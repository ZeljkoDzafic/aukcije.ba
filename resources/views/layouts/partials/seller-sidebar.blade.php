@php
    $links = [
        ['label' => 'Kontrolna tabla', 'route' => 'seller.dashboard', 'match' => 'seller.dashboard'],
        ['label' => 'Moje aukcije', 'route' => 'seller.auctions.index', 'match' => 'seller.auctions.*'],
        ['label' => 'Kreiraj aukciju', 'route' => 'seller.auctions.create', 'match' => 'seller.auctions.create'],
        ['label' => 'Narudžbe', 'route' => 'seller.orders.index', 'match' => 'seller.orders.*'],
        ['label' => 'Analitika', 'route' => 'seller.analytics', 'match' => 'seller.analytics'],
        ['label' => 'Template-i', 'route' => 'seller.templates.index', 'match' => 'seller.templates.*'],
        ['label' => 'Bulk operacije', 'route' => 'seller.operations.index', 'match' => 'seller.operations.*'],
        ['label' => 'Novčanik', 'route' => 'wallet.index', 'match' => 'wallet.*'],
    ];
@endphp

<aside class="hidden w-72 flex-shrink-0 border-r border-slate-200/80 bg-[linear-gradient(180deg,_rgba(120,53,15,0.98)_0%,_rgba(146,64,14,0.96)_42%,_rgba(15,23,42,0.97)_100%)] lg:block">
    <div class="border-b border-white/10 px-6 py-6 text-white">
        <p class="text-sm font-medium uppercase tracking-[0.18em] text-amber-200/80">Seller Studio</p>
        <h2 class="mt-2 text-lg font-semibold text-white">Upravljanje prodajom</h2>
        <p class="mt-2 text-sm text-amber-50/75">Objava, fulfilment i reputacija na jednom mjestu.</p>
    </div>
    <nav class="space-y-2 p-4">
        @foreach ($links as $link)
            <a
                href="{{ route($link['route']) }}"
                class="sidebar-link rounded-2xl {{ request()->routeIs($link['match']) ? 'bg-white/14 text-white ring-1 ring-white/10' : 'text-amber-50/90 hover:bg-white/8 hover:text-white' }}"
            >
                {{ $link['label'] }}
            </a>
        @endforeach
    </nav>

    <div class="mt-auto p-4">
        <div class="rounded-[1.75rem] border border-white/10 bg-white/6 p-4 text-white">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-amber-100/60">Brzi ulazi</p>
            <div class="mt-3 grid gap-2">
                @if (auth()->check())
                    <a href="{{ route('sellers.show', ['user' => auth()->id()]) }}" class="rounded-xl px-3 py-2 text-sm transition hover:bg-white/8">Javni profil</a>
                @endif
                <a href="{{ route('home') }}" class="rounded-xl px-3 py-2 text-sm transition hover:bg-white/8">Storefront</a>
            </div>
        </div>
    </div>
</aside>
