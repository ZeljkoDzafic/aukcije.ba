<header x-data="{ open: false }" class="fixed inset-x-0 top-0 z-40 border-b border-slate-200 bg-white/95 backdrop-blur">
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
            <div class="hidden items-center gap-2 md:flex">
                <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>
                <a href="{{ route('bids.index') }}" class="nav-link">Moje licitacije</a>
                <a href="{{ route('watchlist.index') }}" class="nav-link">Praćene aukcije</a>
                <a href="{{ route('wallet.index') }}" class="nav-link">Novčanik</a>
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
            </div>
        @else
            <div class="hidden items-center gap-2 md:flex">
                <a href="{{ route('auctions.index') }}" class="nav-link">Aukcije</a>
                <a href="{{ route('categories.index') }}" class="nav-link">Kategorije</a>
                <a href="{{ route('news.index') }}" class="nav-link">Vijesti</a>
                <a href="{{ route('help.index') }}" class="nav-link">Pomoć</a>
                <x-button variant="ghost" :href="route('login')">Prijava</x-button>
                <x-button :href="route('register')">Registracija</x-button>
            </div>
        @endauth

        <button type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-slate-200 md:hidden" x-on:click="open = !open" aria-label="Otvori navigaciju" :aria-expanded="open.toString()">
            <span class="text-xl text-slate-700">≡</span>
        </button>
    </div>

    <div x-show="open" x-transition class="border-t border-slate-200 bg-white md:hidden">
        <nav class="flex flex-col gap-2 px-4 py-4">
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
                <a href="{{ route('login') }}" class="nav-link">Prijava</a>
                <a href="{{ route('register') }}" class="nav-link">Registracija</a>
            @endauth
        </nav>
    </div>
</header>
