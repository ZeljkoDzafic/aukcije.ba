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
    @if (($operationsInbox ?? collect())->isNotEmpty())
        <div class="grid gap-3 md:grid-cols-3">
            @foreach ($operationsInbox as $item)
                <a href="{{ $item['href'] }}" class="rounded-3xl border border-slate-200 bg-white px-5 py-4 transition hover:border-slate-300 hover:bg-slate-50">
                    <p class="text-sm text-slate-500">{{ $item['label'] }}</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $item['value'] }}</p>
                    <p class="mt-2 text-sm text-slate-600">{{ $item['hint'] }}</p>
                </a>
            @endforeach
        </div>
    @endif

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
            <div class="flex flex-col gap-4 rounded-2xl bg-slate-50 px-4 py-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="space-y-2">
                    <p class="font-semibold text-slate-900">Order #{{ $entry['id'] }} · {{ $entry['title'] }}</p>
                    <p class="text-sm text-slate-600">Kupac: {{ $entry['buyer'] }} · {{ $entry['amount'] }}</p>
                    <div class="flex flex-wrap gap-3 text-sm text-slate-600">
                        @if (!empty($entry['payment_deadline_at']))
                            <span>Rok uplate: {{ $entry['payment_deadline_at'] }}</span>
                        @endif
                        @if (!empty($entry['tracking_number']))
                            <span>Tracking: {{ $entry['tracking_number'] }}</span>
                        @endif
                        @if (!empty($entry['dispute_status']))
                            <span class="font-medium text-red-700">Spor: {{ str_replace('_', ' ', $entry['dispute_status']) }}</span>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <x-badge :variant="in_array($entry['status'], ['shipped', 'completed', 'delivered'], true) ? 'success' : 'warning'">{{ str_replace('_', ' ', $entry['status']) }}</x-badge>
                    <x-button variant="secondary" :href="route('seller.orders.show', ['order' => $entry['id']])">Detalj</x-button>
                    @if (!empty($entry['dispute_status']))
                        <x-button variant="ghost" :href="route('seller.orders.dispute.show', ['order' => $entry['id']])">Spor</x-button>
                    @endif
                </div>
            </div>
        @endforeach
    </x-card>
</section>
@endsection
