@extends('layouts.guest')

@section('title', 'Prodavači')

@section('content')
<section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
    <div class="space-y-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Seller directory</p>
                <h1 class="mt-2 text-4xl font-semibold text-slate-900">Provjereni i aktivni prodavači</h1>
                <p class="mt-3 max-w-3xl text-slate-600">Kupci mogu pregledati reputaciju, aktivnost i javni profil prodavača prije licitiranja.</p>
            </div>

            <form method="GET" action="{{ route('sellers.index') }}" class="grid gap-3 sm:grid-cols-[1fr_220px]">
                <x-input name="q" label="Pretraga" :value="$search" placeholder="Ime, grad ili bio" />
                <label class="block text-sm font-medium text-slate-700">
                    Sortiranje
                    <select name="sort" class="input mt-2">
                        <option value="reputation" @selected($sort === 'reputation')>Reputacija</option>
                        <option value="activity" @selected($sort === 'activity')>Aktivne aukcije</option>
                        <option value="sales" @selected($sort === 'sales')>Prodaje</option>
                    </select>
                </label>
            </form>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            @forelse ($sellers as $seller)
                <x-card class="space-y-5">
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
                            <p class="mt-3 text-sm text-slate-700">{{ $seller['bio'] }}</p>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-4">
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

                    <div class="flex items-center justify-between gap-4">
                        <p class="text-sm text-slate-500">Prije licitiranja pregledajte javni profil i ocjene.</p>
                        <x-button variant="ghost" :href="route('sellers.show', ['user' => $seller['id']])">Otvori profil</x-button>
                    </div>
                </x-card>
            @empty
                <x-alert variant="info">Nema prodavača za zadanu pretragu.</x-alert>
            @endforelse
        </div>
    </div>
</section>
@endsection
