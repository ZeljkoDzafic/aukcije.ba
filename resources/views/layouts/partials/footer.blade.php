<footer class="border-t border-slate-200 bg-white">
    <div class="border-b border-slate-200 bg-slate-950 text-white">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 py-8 sm:px-6 lg:grid-cols-[1.2fr_0.8fr_0.8fr] lg:px-8">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Marketplace update</p>
                <h2 class="mt-3 text-2xl font-semibold">Objavi aukciju, izgradi seller prisustvo i vodi kupca kroz jasniji trust sloj.</h2>
                <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-300">Inspiracija lokalnim marketplace navikama, ali sa više fokusa na escrow, seller reputaciju i sadržajno jače listinge.</p>
            </div>
            <div class="rounded-[1.5rem] border border-white/10 bg-white/5 p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Prodaja</p>
                <a href="{{ route('register') }}" class="mt-3 block text-lg font-semibold text-white">Pokreni seller nalog</a>
                <p class="mt-2 text-sm text-slate-300">Objava aukcije, seller directory i profil sa reputacijom.</p>
            </div>
            <div class="rounded-[1.5rem] border border-white/10 bg-white/5 p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Sigurnost</p>
                <a href="{{ route('help.index') }}" class="mt-3 block text-lg font-semibold text-white">Pomoć i sigurnost</a>
                <p class="mt-2 text-sm text-slate-300">Pravila, zaštita kupaca i pojašnjenje transakcijskog toka.</p>
            </div>
        </div>
    </div>

    <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:px-6 lg:grid-cols-4 lg:px-8">
        <div class="space-y-3">
            <div class="flex items-center gap-3 font-semibold text-slate-900">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-trust-700 text-sm font-bold text-white">A</span>
                <span>Aukcije.ba</span>
            </div>
            <p class="text-sm text-slate-600">Regionalna aukcijska platforma sa escrow zaštitom, verified seller signalima i real-time licitiranjem.</p>
        </div>

        <div>
            <h2 class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-500">Platforma</h2>
            <div class="mt-3 flex flex-col gap-2 text-sm">
                <a href="{{ route('auctions.index') }}" class="text-slate-600 hover:text-slate-900">Aukcije</a>
                <a href="{{ route('categories.index') }}" class="text-slate-600 hover:text-slate-900">Kategorije</a>
                <a href="{{ route('sellers.index') }}" class="text-slate-600 hover:text-slate-900">Prodavači</a>
                <a href="{{ route('register') }}" class="text-slate-600 hover:text-slate-900">Objavi aukciju</a>
                <a href="{{ route('search') }}" class="text-slate-600 hover:text-slate-900">Pretraga</a>
                <a href="{{ route('news.index') }}" class="text-slate-600 hover:text-slate-900">Vijesti</a>
                <a href="{{ route('help.index') }}" class="text-slate-600 hover:text-slate-900">Pomoć</a>
            </div>
        </div>

        <div>
            <h2 class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-500">Povjerenje</h2>
            <div class="mt-3 flex flex-col gap-2 text-sm text-slate-600">
                <p>Escrow zaštita transakcija</p>
                <p>Verified seller bedževi</p>
                <p>Ocjene kupaca i prodavaca</p>
                <p>Javna pravila i obavijesti</p>
            </div>
        </div>

        <div>
            <h2 class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-500">Podrška</h2>
            <div class="mt-3 flex flex-col gap-2 text-sm">
                <a href="{{ route('content.about') }}" class="text-slate-600 hover:text-slate-900">O nama</a>
                <a href="{{ route('content.buying') }}" class="text-slate-600 hover:text-slate-900">Kako kupovati</a>
                <a href="{{ route('content.selling') }}" class="text-slate-600 hover:text-slate-900">Kako prodavati</a>
                <a href="{{ route('content.show', ['slug' => 'kontakt']) }}" class="text-slate-600 hover:text-slate-900">Kontakt</a>
                <a href="{{ route('content.terms') }}" class="text-slate-600 hover:text-slate-900">Uslovi korištenja</a>
                <a href="{{ route('content.privacy') }}" class="text-slate-600 hover:text-slate-900">Politika privatnosti</a>
            </div>
        </div>
    </div>
</footer>
