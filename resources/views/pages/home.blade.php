@extends('layouts.guest')

@section('title', 'Aukcije.ba - Licitiraj. Pobijedi. Uživaj.')
@section('meta')
    <x-seo-meta
        title="Aukcije.ba - Licitiraj. Pobijedi. Uživaj."
        description="Regionalna aukcijska platforma sa escrow zaštitom, verified seller signalima i real-time licitiranjem."
    />
@endsection

@section('content')
<section class="bg-[radial-gradient(circle_at_top_left,_rgba(37,99,235,0.12),_transparent_40%),linear-gradient(180deg,#ffffff_0%,#eff6ff_100%)]">
    <div class="mx-auto grid max-w-7xl gap-12 px-4 py-20 sm:px-6 lg:grid-cols-[1.1fr_0.9fr] lg:px-8 lg:py-24">
        <div class="space-y-8">
            <div class="space-y-4">
                <x-badge variant="trust">Escrow zaštita i real-time bidding</x-badge>
                <h1 class="max-w-2xl text-4xl font-semibold tracking-tight text-slate-950 sm:text-5xl">Regionalna aukcijska platforma za artikle koje vrijedi dobiti po pravoj cijeni.</h1>
                <p class="max-w-2xl text-lg text-slate-600">Kupci dobijaju brzinu, selleri dobijaju strukturisan seller studio, a obje strane dobijaju povjerenje kroz escrow, verified bedževe i jasne rokove transakcije.</p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                <x-button :href="route('register')">Počni licitirati</x-button>
                <x-button variant="ghost" :href="route('register')">Prodaj odmah</x-button>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <x-card>
                    <p class="text-sm text-slate-500">Aktivnih aukcija</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-900">1.240</p>
                </x-card>
                <x-card>
                    <p class="text-sm text-slate-500">Registriranih korisnika</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-900">18.500</p>
                </x-card>
                <x-card>
                    <p class="text-sm text-slate-500">Pozitivnih ocjena</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-900">97.8%</p>
                </x-card>
            </div>
        </div>

        <div class="space-y-5 rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-xl shadow-trust-100 backdrop-blur">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Featured aukcija</p>
                    <h2 class="mt-2 text-2xl font-semibold text-slate-900">Rolex Oyster Perpetual 39</h2>
                </div>
                <x-badge variant="warning">Uskoro završava</x-badge>
            </div>
            <div class="rounded-3xl bg-gradient-to-br from-slate-100 to-slate-200 p-10 text-center text-sm font-medium text-slate-500">Hero auction preview</div>
            <div class="grid gap-4 sm:grid-cols-2">
                <x-price-display amount="4250.00">Trenutna cijena</x-price-display>
                <div class="space-y-1">
                    <p class="price-label">Preostalo</p>
                    <p class="countdown-normal text-2xl">02d 14h 11m</p>
                </div>
            </div>
            <x-progress-bar :value="84" />
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="flex items-end justify-between gap-4">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Featured aukcije</p>
            <h2 class="mt-2 text-3xl font-semibold text-slate-900">Aukcije koje završavaju uskoro</h2>
        </div>
        <a href="{{ route('auctions.index') }}" class="link">Pregledaj sve</a>
    </div>

    <div class="mt-8 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        <x-auction-card />
        <x-auction-card title="Bosanski ćilim iz 1950." category="Kolekcionarstvo" price="620.00" bids="9" watchers="21" location="Travnik" time="1d 09h" />
        <x-auction-card title="Canon EOS R6 sa objektivom" category="Foto oprema" price="1890.00" bids="22" watchers="58" location="Mostar" time="3d 01h" />
    </div>
</section>

<section class="bg-white">
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            @foreach (['Elektronika', 'Satovi', 'Auto oprema', 'Kolekcionarstvo'] as $category)
                <x-card class="space-y-3">
                    <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Kategorija</p>
                    <h3 class="text-xl font-semibold text-slate-900">{{ $category }}</h3>
                    <p class="text-sm text-slate-600">Najtraženiji artikli, aktivni bidovi i selleri sa rating signalima.</p>
                </x-card>
            @endforeach
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="grid gap-6 lg:grid-cols-3">
        <x-card class="space-y-3">
            <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">1</p>
            <h3 class="text-2xl font-semibold text-slate-900">Registruj se</h3>
            <p class="text-slate-600">Verifikuj email, podesi račun i biraj da li nastupaš kao kupac ili seller.</p>
        </x-card>
        <x-card class="space-y-3">
            <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">2</p>
            <h3 class="text-2xl font-semibold text-slate-900">Licitiraj ili objavi aukciju</h3>
            <p class="text-slate-600">Prati minimalni bid, koristi proxy bidding i dobij trenutni feedback kroz real-time update.</p>
        </x-card>
        <x-card class="space-y-3">
            <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">3</p>
            <h3 class="text-2xl font-semibold text-slate-900">Završi kupovinu sigurno</h3>
            <p class="text-slate-600">Escrow i sistem ocjena čuvaju obje strane do zatvaranja transakcije.</p>
        </x-card>
    </div>
</section>
@endsection
