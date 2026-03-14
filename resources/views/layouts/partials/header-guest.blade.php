<header x-data="{ open: false }" class="border-b border-slate-200 bg-white/95 backdrop-blur">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="flex items-center gap-3 font-semibold text-slate-900">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-trust-700 text-sm font-bold text-white">A</span>
            <span>Aukcije.ba</span>
        </a>

        <nav aria-label="Main navigation" class="hidden items-center gap-2 md:flex">
            <a href="{{ route('auctions.index') }}" class="nav-link">Aukcije</a>
            <a href="{{ route('categories.index') }}" class="nav-link">Kategorije</a>
            <a href="{{ route('search') }}" class="nav-link">Pretraga</a>
            <a href="{{ route('login') }}" class="nav-link">Prijava</a>
            <a href="{{ route('register') }}" class="btn-primary">Registracija</a>
        </nav>

        <button type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-slate-200 md:hidden" x-on:click="open = !open" aria-label="Otvori meni" :aria-expanded="open.toString()">
            <span class="text-xl text-slate-700">≡</span>
        </button>
    </div>

    <div x-show="open" x-transition class="border-t border-slate-200 bg-white md:hidden">
        <nav aria-label="Mobile navigation" class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-4 sm:px-6">
            <a href="{{ route('auctions.index') }}" class="nav-link">Aukcije</a>
            <a href="{{ route('categories.index') }}" class="nav-link">Kategorije</a>
            <a href="{{ route('search') }}" class="nav-link">Pretraga</a>
            <a href="{{ route('login') }}" class="nav-link">Prijava</a>
            <a href="{{ route('register') }}" class="btn-primary">Registracija</a>
        </nav>
    </div>
</header>
