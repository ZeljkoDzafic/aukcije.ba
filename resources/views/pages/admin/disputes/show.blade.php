@extends('layouts.admin')

@section('title', 'Detalj spora')
@section('admin_heading', 'Detalj spora')

@section('content')
@php
    $disputeRecord = $disputeRecord ?? null;
    $decisionHistory = $decisionHistory ?? collect();
    $disputeTitle = $disputeRecord?->reason ? str($disputeRecord->reason)->replace('_', ' ')->headline() : 'item_not_as_described';
    $disputeStatus = $disputeRecord?->status ? str($disputeRecord->status)->replace('_', ' ')->headline() : 'Otvoren spor';
    $disputeDescription = $disputeRecord?->description ?: 'Kupac navodi da artikal ima više ogrebotina nego što je opisano i traži partial refund.';
    $orderId = $disputeRecord?->order?->id ?? 'A-881';
    $orderAmount = number_format((float) ($disputeRecord?->order?->total_amount ?? 2140), 2, ',', '.');
    $trackingNumber = $disputeRecord?->order?->shipment?->tracking_number ?? 'EE123456789BA';
@endphp
<section class="space-y-8">
    <div class="grid gap-8 xl:grid-cols-[1fr_0.9fr]">
        <div class="space-y-6">
            <x-card class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm uppercase tracking-[0.18em] text-slate-500">Dispute #{{ str((string) ($disputeRecord?->id ?? 'D-91'))->upper()->substr(0, 8) }}</p>
                        <h1 class="mt-2 text-2xl font-semibold text-slate-900">{{ $disputeTitle }}</h1>
                    </div>
                    <x-badge variant="warning">{{ $disputeStatus }}</x-badge>
                </div>
                <p class="text-slate-600">{{ $disputeDescription }}</p>
            </x-card>

            <x-card class="space-y-4">
                <h2 class="text-xl font-semibold text-slate-900">Timeline</h2>
                <div class="space-y-3">
                    @foreach (['Spor otvoren', 'Dokazi dodani', 'Seller odgovorio', 'Čeka odluku moderatora'] as $event)
                        <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-700">{{ $event }}</div>
                    @endforeach
                </div>
            </x-card>

            <x-card class="space-y-4">
                <h2 class="text-xl font-semibold text-slate-900">Komunikacija</h2>
                <div class="space-y-3">
                    <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-700">Kupac: “Opis je bio optimističan, tražim refund od 250 BAM.”</div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-700">Seller: “Spreman sam na djelimičan refund nakon pregleda fotografija.”</div>
                </div>
            </x-card>

            <x-card class="space-y-4">
                <h2 class="text-xl font-semibold text-slate-900">Decision history</h2>
                <div class="space-y-3">
                    @forelse ($decisionHistory as $entry)
                        <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-700">
                            <span class="font-semibold text-slate-900">{{ strtoupper($entry->action) }}</span>
                            · {{ $entry->admin?->name ?? 'Admin' }}
                            · {{ $entry->created_at?->format('d.m.Y. H:i') }}
                            @if (($entry->metadata['resolution'] ?? null) !== null)
                                <div class="mt-2 text-slate-600">Ishod: {{ $entry->metadata['resolution'] }}</div>
                            @endif
                            @if (($entry->metadata['message'] ?? null) !== null)
                                <div class="mt-2 text-slate-600">Poruka: {{ $entry->metadata['message'] }}</div>
                            @endif
                        </div>
                    @empty
                        <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">Još nema zabilježenih moderatorskih odluka za ovaj spor.</div>
                    @endforelse
                </div>
            </x-card>
        </div>

        <div class="space-y-6">
            <x-card class="space-y-4">
                <h2 class="text-xl font-semibold text-slate-900">Order detalji</h2>
                <x-data-table :headers="['Polje', 'Vrijednost']">
                    <tr class="table-row"><td class="px-4 py-3">Order</td><td class="px-4 py-3">#{{ str((string) $orderId)->upper()->substr(0, 8) }}</td></tr>
                    <tr class="table-row"><td class="px-4 py-3">Iznos</td><td class="px-4 py-3">{{ $orderAmount }} BAM</td></tr>
                    <tr class="table-row"><td class="px-4 py-3">Tracking</td><td class="px-4 py-3">{{ $trackingNumber }}</td></tr>
                    <tr class="table-row"><td class="px-4 py-3">Escrow</td><td class="px-4 py-3">Zamrznut</td></tr>
                </x-data-table>
            </x-card>

            @livewire('admin.dispute-resolution', ['disputeId' => request()->route('dispute')])
        </div>
    </div>
</section>
@endsection
