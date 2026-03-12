<header class="border-b border-slate-200 bg-white px-4 py-4 sm:px-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Seller</p>
            <h1 class="text-2xl font-semibold text-slate-900">@yield('seller_heading', 'Dashboard')</h1>
        </div>
        <div class="flex items-center gap-3">
            <span class="rounded-full bg-trust-50 px-3 py-1 text-sm font-medium text-trust-700">Premium seller</span>
            <a href="{{ route('seller.auctions.create') }}" class="btn-primary">Kreiraj aukciju</a>
        </div>
    </div>
</header>
