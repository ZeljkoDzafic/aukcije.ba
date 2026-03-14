@extends('layouts.guest')

@section('title', 'Prodavači')

@section('content')
<section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
    <div class="space-y-8">
        <div class="market-sheen rounded-[2rem] border border-slate-200 bg-[radial-gradient(circle_at_top_left,_rgba(14,165,233,0.14),_transparent_28%),radial-gradient(circle_at_bottom_right,_rgba(245,158,11,0.15),_transparent_26%),linear-gradient(180deg,#ffffff_0%,#f8fafc_100%)] p-6 shadow-sm sm:p-8">
            <div class="grid gap-8 xl:grid-cols-[1fr_0.95fr] xl:items-end">
                <div>
                    <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Seller directory</p>
                    <h1 class="mt-2 text-4xl font-semibold text-slate-900">Provjereni i aktivni prodavači</h1>
                    <p class="mt-3 max-w-3xl text-slate-600">Prije licitiranja pregledaj reputaciju, javni profil, fokus kategorije i ritam aktivnosti svakog prodavača. Seller discovery sada je stvarni marketplace layer, ne sporedna stranica.</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="rounded-[1.5rem] border border-white/70 bg-white/80 px-4 py-4 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Prodavača</p>
                        <p class="mt-2 text-3xl font-semibold text-slate-950">{{ $sellers->count() }}</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-white/70 bg-white/80 px-4 py-4 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Sortiranje</p>
                        <p class="mt-2 text-lg font-semibold text-trust-700">{{ $sort === 'activity' ? 'Aktivnost' : ($sort === 'sales' ? 'Prodaje' : 'Reputacija') }}</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-white/70 bg-white/80 px-4 py-4 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Kategorija</p>
                        <p class="mt-2 text-lg font-semibold text-slate-950">{{ $activeCategory !== '' ? collect($categories)->firstWhere('slug', $activeCategory)['name'] ?? $activeCategory : 'Sve' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-8 xl:grid-cols-[20rem_minmax(0,1fr)]">
            <aside class="space-y-4">
                <div class="market-sheen rounded-[1.75rem] border border-slate-200 bg-[linear-gradient(180deg,#ffffff_0%,#f8fafc_100%)] p-5 shadow-sm">
                    <form method="GET" action="{{ route('sellers.index') }}" class="space-y-4">
                        <div>
                            <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Filteri</p>
                            <h2 class="mt-2 text-2xl font-semibold text-slate-900">Pronađi sellera</h2>
                        </div>
                        <x-input name="q" label="Pretraga" :value="$search" placeholder="Ime, grad ili bio" />
                        <label class="block text-sm font-medium text-slate-700">
                            Kategorija
                            <select name="category" class="input mt-2">
                                <option value="">Sve kategorije</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category['slug'] }}" @selected($activeCategory === $category['slug'])>{{ $category['name'] }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="block text-sm font-medium text-slate-700">
                            Sortiranje
                            <select name="sort" class="input mt-2">
                                <option value="reputation" @selected($sort === 'reputation')>Reputacija</option>
                                <option value="activity" @selected($sort === 'activity')>Aktivne aukcije</option>
                                <option value="sales" @selected($sort === 'sales')>Prodaje</option>
                            </select>
                        </label>
                        <div class="flex gap-3">
                            <x-button type="submit" class="flex-1">Primijeni</x-button>
                            <x-button variant="ghost" :href="route('sellers.index')" class="flex-1">Reset</x-button>
                        </div>
                    </form>
                </div>

                <div class="market-sheen rounded-[1.75rem] border border-slate-200 bg-[linear-gradient(180deg,#fffef7_0%,#ffffff_100%)] p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Zašto seller directory</p>
                    <div class="mt-4 grid gap-3">
                        <div class="rounded-2xl bg-emerald-50 px-4 py-3 text-sm text-emerald-900">Provjera reputacije prije bida</div>
                        <div class="rounded-2xl bg-sky-50 px-4 py-3 text-sm text-sky-900">Javni pregled fokus kategorija</div>
                        <div class="rounded-2xl bg-amber-50 px-4 py-3 text-sm text-amber-900">Brži ulaz u pouzdane profile</div>
                    </div>
                </div>
            </aside>

            <div class="space-y-6">
                <div class="market-sheen flex flex-col gap-4 rounded-[1.75rem] border border-slate-200 bg-[linear-gradient(180deg,#ffffff_0%,#f8fafc_100%)] p-5 shadow-sm lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Rezultati</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $sellers->count() }} seller profila</p>
                        <p class="mt-1 text-sm text-slate-600">Direktorij daje buyeru jasan uvid u kvalitet, aktivnost i tip prodavača prije prve licitacije.</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @if ($search !== '')
                            <span class="rounded-full bg-slate-100 px-3 py-2 text-sm text-slate-700">Upit: {{ $search }}</span>
                        @endif
                        @if ($activeCategory !== '')
                            <span class="rounded-full bg-slate-100 px-3 py-2 text-sm text-slate-700">Kategorija: {{ collect($categories)->firstWhere('slug', $activeCategory)['name'] ?? $activeCategory }}</span>
                        @endif
                    </div>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    @forelse ($sellers as $seller)
                        <div class="market-sheen rounded-[1.75rem] border border-slate-200 bg-[linear-gradient(180deg,#ffffff_0%,#f8fafc_100%)] p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg">
                            <div class="flex items-start gap-4">
                                <x-avatar :name="$seller['name']" size="lg" />
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <a href="{{ route('sellers.show', ['user' => $seller['id']]) }}" class="text-2xl font-semibold text-slate-900 transition hover:text-trust-700">{{ $seller['name'] }}</a>
                                        @if ($seller['badge'])
                                            <x-badge variant="trust">{{ $seller['badge'] }}</x-badge>
                                        @endif
                                    </div>
                                    <p class="mt-1 text-sm text-slate-500">{{ $seller['city'] }} · član od {{ $seller['member_since'] }}</p>
                                    <p class="mt-3 text-sm leading-7 text-slate-700">{{ $seller['bio'] }}</p>
                                </div>
                            </div>

                            <div class="mt-5 grid gap-4 sm:grid-cols-4">
                                <div class="rounded-2xl bg-slate-50 px-4 py-3">
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Trust</p>
                                    <p class="mt-1 text-lg font-semibold text-slate-900">{{ number_format($seller['trust_score'], 1) }}</p>
                                </div>
                                <div class="rounded-2xl bg-slate-50 px-4 py-3">
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Ocjene</p>
                                    <p class="mt-1 text-lg font-semibold text-slate-900">{{ $seller['ratings_count'] }}</p>
                                </div>
                                <div class="rounded-2xl bg-slate-50 px-4 py-3">
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Aktivne</p>
                                    <p class="mt-1 text-lg font-semibold text-slate-900">{{ $seller['active_auctions'] }}</p>
                                </div>
                                <div class="rounded-2xl bg-slate-50 px-4 py-3">
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Prodaje</p>
                                    <p class="mt-1 text-lg font-semibold text-slate-900">{{ $seller['sold_orders'] }}</p>
                                </div>
                            </div>

                            <div class="mt-5 flex items-center justify-between gap-4">
                                <p class="text-sm text-slate-500">Buyer signal: reputacija, aktivnost i javni profil na jednom mjestu.</p>
                                <x-button variant="ghost" :href="route('sellers.show', ['user' => $seller['id']])">Otvori profil</x-button>
                            </div>
                        </div>
                    @empty
                        <x-alert variant="info">Nema prodavača za zadanu pretragu.</x-alert>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
