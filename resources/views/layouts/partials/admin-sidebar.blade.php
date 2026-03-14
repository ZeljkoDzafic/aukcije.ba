@php
    $links = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'match' => 'admin.dashboard'],
        ['label' => 'Aukcije', 'route' => 'admin.auctions.index', 'match' => 'admin.auctions.*'],
        ['label' => 'Korisnici', 'route' => 'admin.users.index', 'match' => 'admin.users.*'],
        ['label' => 'KYC pregled', 'route' => 'admin.kyc.index', 'match' => 'admin.kyc.*'],
        ['label' => 'Kategorije', 'route' => 'admin.categories.index', 'match' => 'admin.categories.*'],
        ['label' => 'Sporovi', 'route' => 'admin.disputes.index', 'match' => 'admin.disputes.*'],
        ['label' => 'Statične stranice', 'route' => 'admin.content.pages.index', 'match' => 'admin.content.pages.*'],
        ['label' => 'Vijesti', 'route' => 'admin.content.news.index', 'match' => 'admin.content.news.*'],
        ['label' => 'Audit log', 'route' => 'admin.activity.index', 'match' => 'admin.activity.*'],
        ['label' => 'Statistike', 'route' => 'admin.statistics', 'match' => 'admin.statistics'],
    ];
@endphp

<aside class="hidden w-72 flex-shrink-0 border-r border-slate-200/80 bg-[linear-gradient(180deg,_rgba(2,6,23,0.98)_0%,_rgba(15,23,42,0.97)_55%,_rgba(12,74,110,0.97)_100%)] text-slate-100 lg:block">
    <div class="border-b border-white/10 px-6 py-6">
        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-sky-200/70">Backoffice</p>
        <div class="mt-2 text-lg font-semibold text-white">Admin Panel</div>
        <p class="mt-2 text-sm text-slate-300">Moderacija, sadržaj i operativne odluke.</p>
        <div class="mt-5 grid gap-3">
            <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                <p class="text-xs uppercase tracking-[0.22em] text-slate-400">Status sistema</p>
                <p class="mt-2 text-sm font-medium text-white">Marketplace live, zahtjevi čekaju obradu</p>
            </div>
        </div>
    </div>
    <nav class="space-y-2 p-4">
        @foreach ($links as $link)
            @php
                $active = request()->routeIs($link['match']);
            @endphp
            <a
                href="{{ route($link['route']) }}"
                class="sidebar-link rounded-2xl {{ $active ? 'bg-white/12 text-white ring-1 ring-white/10' : 'text-slate-200 hover:bg-white/8 hover:text-white' }}"
            >
                {{ $link['label'] }}
            </a>
        @endforeach
    </nav>

    <div class="mt-auto border-t border-white/10 p-4">
        <div class="rounded-[1.75rem] border border-white/10 bg-white/5 p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Brze akcije</p>
            <div class="mt-3 grid gap-2">
                <a href="{{ route('home') }}" class="rounded-xl px-3 py-2 text-sm text-slate-200 transition hover:bg-white/8 hover:text-white">Pogledaj storefront</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full rounded-xl bg-white/10 px-3 py-2 text-left text-sm font-medium text-white transition hover:bg-white/15">
                        Odjava
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>
