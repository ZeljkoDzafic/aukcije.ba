<header class="border-b border-slate-200/70 bg-white/80 px-4 py-4 backdrop-blur sm:px-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Seller studio</p>
            <h1 class="text-2xl font-semibold text-slate-900">@yield('seller_heading', 'Dashboard')</h1>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <span class="rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-sm font-medium text-amber-800">Seller mode aktivan</span>
            <a href="{{ route('home') }}" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                Storefront
            </a>
            <a href="{{ route('seller.auctions.create') }}" class="btn-primary">Kreiraj aukciju</a>
        </div>
    </div>
</header>
