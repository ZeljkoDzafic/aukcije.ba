@extends('layouts.seller')

@section('title', 'Detalj narudžbe')
@section('seller_heading', 'Detalj narudžbe')

@section('content')
@php
    $order = $order ?? (object) [
        'id' => 881,
        'title' => 'Canon R6 Body',
        'status' => 'awaiting_shipment',
        'buyer_name' => 'Jasmin K.',
        'buyer_email' => 'jasmin@example.test',
        'buyer_phone' => '+387 61 111 222',
        'shipping_address' => 'Zmaja od Bosne 14, Sarajevo',
        'shipping_note' => 'Brza pošta, isporuka 1-2 dana',
        'amount' => '2.140,00 BAM',
        'commission' => '107,00 BAM',
        'payout' => '2.033,00 BAM',
        'tracking_number' => 'EE123456789BA',
    ];
@endphp
<section class="space-y-8">
    <div class="grid gap-8 xl:grid-cols-[1fr_0.85fr]">
        <div class="space-y-6">
            <x-card class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm uppercase tracking-[0.18em] text-slate-500">Order #{{ $order->id }}</p>
                        <h1 class="mt-2 text-2xl font-semibold text-slate-900">{{ $order->title }}</h1>
                    </div>
                    <x-badge variant="warning">Spremno za slanje</x-badge>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-sm text-slate-500">Kupac</p>
                        <p class="mt-1 font-medium text-slate-900">{{ $order->buyer_name }}</p>
                        <p class="text-sm text-slate-600">{{ $order->buyer_email }} · {{ $order->buyer_phone }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Adresa dostave</p>
                        <p class="mt-1 font-medium text-slate-900">{{ $order->shipping_address }}</p>
                        <p class="text-sm text-slate-600">{{ $order->shipping_note }}</p>
                    </div>
                </div>
            </x-card>

            <x-card class="space-y-4">
                <h2 class="text-xl font-semibold text-slate-900">Finansijski pregled</h2>
                <x-data-table :headers="['Stavka', 'Iznos']">
                    <tr class="table-row">
                        <td class="px-4 py-3">Finalna cijena</td>
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $order->amount }}</td>
                    </tr>
                    <tr class="table-row">
                        <td class="px-4 py-3">Komisija platforme</td>
                        <td class="px-4 py-3">{{ $order->commission }}</td>
                    </tr>
                    <tr class="table-row">
                        <td class="px-4 py-3">Neto za sellera</td>
                        <td class="px-4 py-3 font-medium text-emerald-700">{{ $order->payout }}</td>
                    </tr>
                </x-data-table>
            </x-card>

            <x-card class="space-y-4">
                <h2 class="text-xl font-semibold text-slate-900">Timeline</h2>
                <div class="space-y-3">
                    @foreach (['Aukcija završena', 'Kupac uplatio', 'Narudžba spremna za slanje', 'Tracking: '.$order->tracking_number] as $step)
                        <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-700">{{ $step }}</div>
                    @endforeach
                </div>
            </x-card>
        </div>

        <div class="space-y-6">
            @livewire('seller.order-fulfillment', ['orderId' => request()->route('order')])
        </div>
    </div>
</section>
@endsection
