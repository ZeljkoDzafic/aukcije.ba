<footer class="border-t border-slate-200 bg-white">
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
                <a href="{{ route('search') }}" class="text-slate-600 hover:text-slate-900">Pretraga</a>
            </div>
        </div>

        <div>
            <h2 class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-500">Povjerenje</h2>
            <div class="mt-3 flex flex-col gap-2 text-sm text-slate-600">
                <p>Escrow zaštita transakcija</p>
                <p>Verified seller bedževi</p>
                <p>Ocjene kupaca i prodavaca</p>
            </div>
        </div>

        <div>
            <h2 class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-500">Podrška</h2>
            <div class="mt-3 flex flex-col gap-2 text-sm">
                <a href="#" class="text-slate-600 hover:text-slate-900">FAQ</a>
                <a href="#" class="text-slate-600 hover:text-slate-900">Uslovi korištenja</a>
                <a href="#" class="text-slate-600 hover:text-slate-900">Privatnost</a>
            </div>
        </div>
    </div>
</footer>
