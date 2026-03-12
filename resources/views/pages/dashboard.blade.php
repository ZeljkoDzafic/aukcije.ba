@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach ([['Aktivne ponude', '12'], ['Dobijene aukcije', '3'], ['Praćene aukcije', '27'], ['Wallet balans', '420 BAM']] as [$label, $value])
            <x-card>
                <p class="text-sm text-slate-500">{{ $label }}</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $value }}</p>
            </x-card>
        @endforeach
    </div>

    <div class="mt-8 grid gap-8 xl:grid-cols-[1.1fr_0.9fr]">
        <x-card class="space-y-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-slate-900">Aktivni bidovi</h1>
                <a href="{{ route('auctions.index') }}" class="link">Pregledaj aukcije</a>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <x-auction-card title="MacBook Pro 14 M3" category="Računari" price="2950.00" bids="17" watchers="43" location="Sarajevo" time="11h 02m" />
                <x-auction-card title="Omega Seamaster" category="Satovi" price="3820.00" bids="25" watchers="61" location="Mostar" time="1d 16h" />
            </div>
        </x-card>

        <div class="space-y-6">
            <x-card class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-slate-900">Watchlist</h2>
                    <a href="{{ route('watchlist.index') }}" class="link">Otvori sve</a>
                </div>
                <div class="space-y-3">
                    @foreach (['Vintage gramofon završava za 2h', 'Canon R6 završava sutra', 'Bicikl Trek - 7 bidova'] as $item)
                        <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-700">{{ $item }}</div>
                    @endforeach
                </div>
            </x-card>

            <x-card class="space-y-4">
                <h2 class="text-xl font-semibold text-slate-900">Dobijene aukcije</h2>
                <x-alert variant="warning">Imaš 1 narudžbu koja čeka uplatu. Rok za plaćanje: 48h.</x-alert>
                <div class="space-y-3 text-sm text-slate-700">
                    <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                        <span>Fujifilm X-T5</span>
                        <x-badge variant="warning">Čeka uplatu</x-badge>
                    </div>
                    <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                        <span>Vinyl kolekcija</span>
                        <x-badge variant="success">Plaćeno</x-badge>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</section>
@endsection
