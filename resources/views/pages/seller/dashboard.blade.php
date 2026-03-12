@extends('layouts.seller')

@section('title', 'Seller Dashboard')
@section('seller_heading', 'Seller Dashboard')

@section('content')
<section class="space-y-8">
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach ([['Aktivne aukcije', '14'], ['Ukupna prodaja', '24.800 BAM'], ['Wallet balans', '2.140 BAM'], ['Prosječna ocjena', '4.9']] as [$label, $value])
            <x-card>
                <p class="text-sm text-slate-500">{{ $label }}</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $value }}</p>
            </x-card>
        @endforeach
    </div>

    <div class="grid gap-8 xl:grid-cols-[1.1fr_0.9fr]">
        <x-card class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-semibold text-slate-900">Aktivne aukcije</h2>
                <x-badge variant="trust">Premium tier</x-badge>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <x-auction-card title="Omega Speedmaster Reduced" category="Satovi" price="4150.00" bids="18" watchers="39" location="Sarajevo" time="8h 44m" />
                <x-auction-card title="Leica Summicron 50mm" category="Foto oprema" price="1860.00" bids="10" watchers="28" location="Mostar" time="1d 10h" />
            </div>
        </x-card>

        <div class="space-y-6">
            <x-card class="space-y-4">
                <h2 class="text-xl font-semibold text-slate-900">Narudžbe za slanje</h2>
                <x-alert variant="warning">2 narudžbe moraju biti poslane u narednih 24h.</x-alert>
                <div class="space-y-3 text-sm text-slate-700">
                    <div class="rounded-2xl bg-slate-50 px-4 py-3">Order #A-881 · Canon R6 · Čeka tracking broj</div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3">Order #A-879 · Gramofon Technics · Spremno za slanje</div>
                </div>
            </x-card>

            <x-card class="space-y-4">
                <h2 class="text-xl font-semibold text-slate-900">Tier status</h2>
                <p class="text-sm text-slate-600">Premium plan: 14 od 50 aktivnih aukcija, komisija 5%, prioritetna podrška uključena.</p>
                <x-progress-bar :value="28" />
                <x-button variant="ghost">Pogledaj upgrade opcije</x-button>
            </x-card>
        </div>
    </div>
</section>
@endsection
