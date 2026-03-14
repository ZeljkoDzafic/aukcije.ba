<header x-data="{ open: false }" class="fixed inset-x-0 top-0 z-40 border-b border-slate-200 bg-white/95 backdrop-blur">
    <div class="border-b border-slate-200/80 bg-slate-50/90">
        <div class="mx-auto hidden max-w-7xl items-center justify-between gap-6 px-4 py-2 text-xs font-medium text-slate-600 sm:flex sm:px-6 lg:px-8">
            <div class="flex items-center gap-5">
                <a href="{{ route('home') }}" class="transition hover:text-slate-900">Početna</a>
                <a href="{{ route('categories.index') }}" class="transition hover:text-slate-900">Kategorije</a>
                <a href="{{ route('sellers.index') }}" class="transition hover:text-slate-900">Prodavači</a>
                <a href="{{ route('news.index') }}" class="transition hover:text-slate-900">Vijesti</a>
                <a href="{{ route('help.index') }}" class="transition hover:text-slate-900">Sigurnost i pomoć</a>
            </div>
            <div class="flex items-center gap-4">
                <span class="rounded-full bg-emerald-50 px-3 py-1 text-emerald-700">Escrow zaštita</span>
                <span class="rounded-full bg-amber-50 px-3 py-1 text-amber-700">Real-time bidding</span>
            </div>
        </div>
    </div>

    <div class="mx-auto flex max-w-7xl items-center gap-4 px-4 py-3 sm:px-6 lg:px-8">
        <a href="{{ auth()->check() ? route('dashboard') : route('home') }}" class="flex items-center gap-3 font-semibold text-slate-900">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-trust-700 text-sm font-bold text-white">A</span>
            <span class="hidden sm:inline">Aukcije.ba</span>
        </a>

        <form action="{{ route('search') }}" method="GET" class="hidden flex-1 lg:block">
            <label for="global-search" class="sr-only">Pretraži aukcije</label>
            <input id="global-search" name="q" class="input" type="search" value="{{ request('q') }}" placeholder="Pretrazi aukcije, kategorije i prodavce">
        </form>

        @auth
            <nav aria-label="Main navigation" class="hidden items-center gap-2 md:flex">
                <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>
                <a href="{{ route('bids.index') }}" class="nav-link">Moje licitacije</a>
                <a href="{{ route('watchlist.index') }}" class="nav-link">Praćene aukcije</a>
                <a href="{{ route('wallet.index') }}" class="nav-link">Novčanik</a>
                @if (auth()->user()?->hasAnyRole(['seller', 'verified_seller']))
                    <x-button :href="route('seller.auctions.create')">Objavi aukciju</x-button>
                @endif
                <a href="{{ route('notifications.index') }}" class="relative inline-flex h-11 w-11 items-center justify-center rounded-xl border border-slate-200 text-slate-700" aria-label="Obavijesti">
                    <span>🔔</span>
                    @if (auth()->user()?->marketplaceNotifications()->whereNull('read_at')->count() > 0)
                        <span class="absolute right-2 top-2 h-2.5 w-2.5 rounded-full bg-danger"></span>
                    @endif
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-button variant="ghost">Odjava</x-button>
                </form>
            </nav>
        @else
            <nav aria-label="Main navigation" class="hidden items-center gap-2 md:flex">
                <a href="{{ route('auctions.index') }}" class="nav-link">Aukcije</a>
                <a href="{{ route('categories.index') }}" class="nav-link">Kategorije</a>
                <a href="{{ route('sellers.index') }}" class="nav-link">Prodavači</a>
                <a href="{{ route('news.index') }}" class="nav-link">Vijesti</a>
                <a href="{{ route('help.index') }}" class="nav-link">Pomoć</a>
                <x-button :href="route('register')">Objavi aukciju</x-button>
                <x-button variant="ghost" :href="route('login')">Prijava</x-button>
                <x-button variant="ghost" :href="route('register')">Registracija</x-button>
            </nav>
        @endauth

        <button type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-slate-200 md:hidden" x-on:click="open = !open" aria-label="Otvori navigaciju" :aria-expanded="open.toString()">
            <span class="text-xl text-slate-700">≡</span>
        </button>
    </div>

    <div x-show="open" x-transition class="border-t border-slate-200 bg-white md:hidden">
        <nav aria-label="Mobile navigation" class="flex flex-col gap-2 px-4 py-4">
            @auth
                <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>
            @else
                <a href="{{ route('home') }}" class="nav-link">Početna</a>
            @endauth
            <a href="{{ route('auctions.index') }}" class="nav-link">Aukcije</a>
            <a href="{{ route('categories.index') }}" class="nav-link">Kategorije</a>
            <a href="{{ route('sellers.index') }}" class="nav-link">Prodavači</a>
            <a href="{{ route('news.index') }}" class="nav-link">Vijesti</a>
            <a href="{{ route('help.index') }}" class="nav-link">Pomoć</a>
            @auth
                @if (auth()->user()?->hasAnyRole(['seller', 'verified_seller']))
                    <a href="{{ route('seller.auctions.create') }}" class="nav-link">Objavi aukciju</a>
                @endif
                <a href="{{ route('bids.index') }}" class="nav-link">Moje licitacije</a>
                <a href="{{ route('watchlist.index') }}" class="nav-link">Praćene aukcije</a>
                <a href="{{ route('wallet.index') }}" class="nav-link">Novčanik</a>
                <a href="{{ route('notifications.index') }}" class="nav-link">Obavijesti</a>
                <a href="{{ route('settings.notifications') }}" class="nav-link">Postavke</a>
                <a href="{{ route('messages.index') }}" class="nav-link">Poruke</a>
                <form method="POST" action="{{ route('logout') }}" class="pt-2">
                    @csrf
                    <x-button variant="ghost" class="w-full">Odjava</x-button>
                </form>
            @else
                <a href="{{ route('register') }}" class="nav-link">Objavi aukciju</a>
                <a href="{{ route('login') }}" class="nav-link">Prijava</a>
                <a href="{{ route('register') }}" class="nav-link">Registracija</a>
            @endauth
        </nav>
    </div>
</header>
