<aside class="hidden w-72 flex-shrink-0 border-r border-slate-200 bg-white lg:block">
    <div class="border-b border-slate-200 px-6 py-5">
        <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Seller Studio</p>
        <h2 class="mt-2 text-lg font-semibold text-slate-900">Upravljanje prodajom</h2>
    </div>
    <nav class="space-y-2 p-4">
        <a href="{{ route('seller.dashboard') }}" class="sidebar-link">Kontrolna tabla</a>
        <a href="{{ route('seller.auctions.index') }}" class="sidebar-link">Moje aukcije</a>
        <a href="{{ route('seller.auctions.create') }}" class="sidebar-link">Kreiraj aukciju</a>
        <a href="{{ route('seller.orders.index') }}" class="sidebar-link">Narudžbe</a>
        <a href="{{ route('seller.analytics') }}" class="sidebar-link">Analitika</a>
        <a href="{{ route('seller.templates.index') }}" class="sidebar-link">Template-i</a>
        <a href="{{ route('seller.operations.index') }}" class="sidebar-link">Bulk operacije</a>
        <a href="{{ route('wallet.index') }}" class="sidebar-link">Novčanik</a>
    </nav>
</aside>
