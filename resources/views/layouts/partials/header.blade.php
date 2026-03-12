<header x-data="{ open: false }" class="fixed inset-x-0 top-0 z-40 border-b border-slate-200 bg-white/95 backdrop-blur">
    <div class="mx-auto flex max-w-7xl items-center gap-4 px-4 py-3 sm:px-6 lg:px-8">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 font-semibold text-slate-900">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-trust-700 text-sm font-bold text-white">A</span>
            <span class="hidden sm:inline">Aukcije.ba</span>
        </a>

        <form class="hidden flex-1 lg:block">
            <label for="global-search" class="sr-only">Pretraži aukcije</label>
            <input id="global-search" class="input" type="search" placeholder="Pretraži aukcije, kategorije i prodavce">
        </form>

        <div class="hidden items-center gap-2 md:flex">
            <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>
            <a href="{{ route('watchlist.index') }}" class="nav-link">Watchlist</a>
            <a href="{{ route('wallet.index') }}" class="nav-link">Wallet</a>
            <button type="button" class="relative inline-flex h-11 w-11 items-center justify-center rounded-xl border border-slate-200 text-slate-700" aria-label="Obavijesti">
                <span>🔔</span>
                <span class="absolute right-2 top-2 h-2.5 w-2.5 rounded-full bg-danger"></span>
            </button>
        </div>

        <button type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-slate-200 md:hidden" x-on:click="open = !open" aria-label="Otvori navigaciju" :aria-expanded="open.toString()">
            <span class="text-xl text-slate-700">≡</span>
        </button>
    </div>

    <div x-show="open" x-transition class="border-t border-slate-200 bg-white md:hidden">
        <nav class="flex flex-col gap-2 px-4 py-4">
            <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>
            <a href="{{ route('auctions.index') }}" class="nav-link">Aukcije</a>
            <a href="{{ route('watchlist.index') }}" class="nav-link">Watchlist</a>
            <a href="{{ route('wallet.index') }}" class="nav-link">Wallet</a>
            <a href="{{ route('messages.index') }}" class="nav-link">Poruke</a>
        </nav>
    </div>
</header>
