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
        'payment_deadline_at' => null,
        'dispute_status' => null,
        'dispute_reason' => null,
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
                    <x-badge variant="{{ in_array($order->status, ['shipped', 'delivered', 'completed'], true) ? 'success' : 'warning' }}">
                        {{ str_replace('_', ' ', $order->status) }}
                    </x-badge>
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
                    @foreach ([
                        'Aukcija završena',
                        $order->status !== 'pending_payment' ? 'Kupac uplatio' : 'Čeka uplatu kupca'.($order->payment_deadline_at ? ' do '.$order->payment_deadline_at : ''),
                        in_array($order->status, ['awaiting_shipment', 'shipped', 'delivered', 'completed'], true) ? 'Narudžba spremna za slanje' : 'Narudžba u obradi',
                        'Tracking: '.($order->tracking_number ?: 'nije dodan'),
                        $order->dispute_status ? 'Spor: '.str_replace('_', ' ', (string) $order->dispute_status) : null,
                    ] as $step)
                        @if ($step)
                            <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-700">{{ $step }}</div>
                        @endif
                    @endforeach
                </div>
            </x-card>

            @if ($order->dispute_reason)
                <x-card class="space-y-3">
                    <h2 class="text-xl font-semibold text-slate-900">Spor signal</h2>
                    <div class="rounded-2xl bg-red-50 px-4 py-3 text-sm text-slate-700">
                        Razlog spora: {{ str_replace('_', ' ', (string) $order->dispute_reason) }}
                    </div>
                </x-card>
            @endif
        </div>

        <div class="space-y-6">
            @if (($orderActions ?? collect())->isNotEmpty())
                <x-card class="space-y-4">
                    <h2 class="text-xl font-semibold text-slate-900">Operativni koraci</h2>
                    <div class="space-y-3">
                        @foreach ($orderActions as $action)
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
            @endif

            @livewire('seller.order-fulfillment', ['orderId' => request()->route('order')])
        </div>
    </div>
</section>
@endsection
