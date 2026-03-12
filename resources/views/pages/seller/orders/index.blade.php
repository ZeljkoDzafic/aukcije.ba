@extends('layouts.seller')

@section('title', 'Seller narudžbe')
@section('seller_heading', 'Narudžbe')

@section('content')
@php
    $orders = $orders ?? collect([
        ['id' => 881, 'title' => 'Canon R6', 'buyer' => 'Jasmin K.', 'amount' => '2.140,00 BAM', 'status' => 'awaiting_shipment'],
        ['id' => 879, 'title' => 'Gramofon Technics', 'buyer' => 'Lana R.', 'amount' => '980,00 BAM', 'status' => 'pending_payment'],
    ]);
@endphp
<section class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap gap-3 text-sm">
        <span class="rounded-full bg-trust-50 px-4 py-2 text-trust-700">Čeka uplatu</span>
        <span class="rounded-full bg-slate-100 px-4 py-2 text-slate-600">Spremno za slanje</span>
        <span class="rounded-full bg-slate-100 px-4 py-2 text-slate-600">Poslano</span>
        <span class="rounded-full bg-slate-100 px-4 py-2 text-slate-600">Završeno</span>
        <span class="rounded-full bg-slate-100 px-4 py-2 text-slate-600">Sporovi</span>
        </div>
        <x-button variant="ghost" :href="route('seller.orders.export')">Export CSV</x-button>
    </div>

    <x-card class="space-y-4">
        @foreach ($orders as $entry)
            <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-4">
                <div>
                    <p class="font-semibold text-slate-900">Order #{{ $entry['id'] }} · {{ $entry['title'] }}</p>
                    <p class="text-sm text-slate-600">Kupac: {{ $entry['buyer'] }} · {{ $entry['amount'] }}</p>
                </div>
                <x-button variant="secondary" :href="route('seller.orders.show', ['order' => $entry['id']])">Detalj</x-button>
            </div>
        @endforeach
    </x-card>
</section>
@endsection
