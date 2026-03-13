@extends('layouts.seller')

@section('title', 'Seller spor')
@section('seller_heading', 'Seller spor')

@section('content')
<section class="space-y-8">
    <div class="grid gap-8 xl:grid-cols-[1fr_0.9fr]">
        <div class="space-y-6">
            <x-card class="space-y-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm uppercase tracking-[0.18em] text-slate-500">Narudžba #{{ str($order->id)->upper()->substr(0, 8) }}</p>
                        <h1 class="mt-2 text-2xl font-semibold text-slate-900">{{ $order->auction?->title ?? 'Aukcija bez naslova' }}</h1>
                        <p class="mt-2 text-sm text-slate-600">Kupac: {{ $order->buyer?->name ?? $order->buyer?->email ?? 'Nepoznat kupac' }}</p>
                    </div>
                    <x-badge variant="danger">{{ str($dispute->status)->replace('_', ' ')->headline() }}</x-badge>
                </div>

                <div class="grid gap-4 md:grid-cols-3">
                    <div class="rounded-2xl bg-slate-50 px-4 py-4">
                        <p class="text-sm text-slate-500">Razlog spora</p>
                        <p class="mt-1 font-medium text-slate-900">{{ str($dispute->reason)->replace('_', ' ')->headline() }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-4">
                        <p class="text-sm text-slate-500">Vrijednost</p>
                        <p class="mt-1 font-medium text-slate-900">{{ number_format((float) $order->total_amount, 2, ',', '.') }} KM</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-4">
                        <p class="text-sm text-slate-500">Tracking</p>
                        <p class="mt-1 font-medium text-slate-900">{{ $order->shipment?->tracking_number ?? 'nije dodan' }}</p>
                    </div>
                </div>

                <div class="rounded-3xl bg-red-50 px-5 py-4 text-sm text-slate-700">
                    <p class="font-medium text-slate-900">Opis i seller kontekst</p>
                    <p class="mt-2">{{ $dispute->description ?: 'Kupac je otvorio spor. Provjeri tracking, poruke i fulfilment status prije odgovora.' }}</p>
                </div>
            </x-card>

            <x-card class="space-y-4">
                <h2 class="text-xl font-semibold text-slate-900">Šta provjeriti odmah</h2>
                <div class="space-y-3 text-sm text-slate-700">
                    <div class="rounded-2xl bg-slate-50 px-4 py-3">Potvrdi da li je tracking broj unesen i vidljiv kupcu.</div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3">Provjeri poruke uz aukciju i sačuvaj komunikaciju unutar platforme.</div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3">Ako je pošiljka već predata kuriru, ažuriraj fulfillment i pošalji buyeru potvrdu.</div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3">Ako spor ostane otvoren, pripremi dokaze za moderaciju.</div>
                </div>
            </x-card>
        </div>

        <div class="space-y-6">
            <x-card class="space-y-4">
                <h2 class="text-xl font-semibold text-slate-900">Seller akcije</h2>
                <div class="space-y-3">
                    @foreach ($sellerDisputeActions as $action)
                        <div class="rounded-2xl border border-slate-200 px-4 py-4">
                            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                <div>
                                    <p class="font-medium text-slate-900">{{ $action['label'] }}</p>
                                    <p class="mt-1 text-sm text-slate-600">{{ $action['hint'] }}</p>
                                </div>
                                <x-button variant="ghost" :href="$action['href']">{{ $action['label'] }}</x-button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-card>

            <x-card class="space-y-4">
                <h2 class="text-xl font-semibold text-slate-900">Fulfillment signal</h2>
                <x-alert variant="{{ $order->shipment?->tracking_number ? 'success' : 'warning' }}">
                    {{ $order->shipment?->tracking_number ? 'Tracking broj postoji. Sljedeći korak je potvrda i komunikacija sa kupcem.' : 'Tracking broj još nije evidentiran. Dodaj ga prije odgovora na spor.' }}
                </x-alert>
                <x-button :href="route('seller.orders.show', ['order' => $order->id])">Nazad na fulfillment</x-button>
            </x-card>
        </div>
    </div>
</section>
@endsection
