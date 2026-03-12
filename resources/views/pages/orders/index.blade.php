@extends('layouts.app')

@section('title', 'Narudžbe')

@section('content')
<section class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Kupac</p>
                <h1 class="mt-2 text-3xl font-semibold text-slate-900">Moje narudžbe</h1>
                <p class="mt-2 text-slate-600">Status plaćanja, dostave i povezana aukcija na jednom mjestu.</p>
            </div>
            <a href="{{ route('auctions.index') }}" class="link">Nazad na aukcije</a>
        </div>

        @if ($orders->isEmpty())
            <x-alert variant="info">Još nemaš dobijenih aukcija. Kad osvojiš artikl, narudžba će se pojaviti ovdje.</x-alert>
        @else
            <div class="grid gap-4">
                @foreach ($orders as $order)
                    <x-card class="space-y-4">
                        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                            <div class="flex gap-4">
                                <div class="h-20 w-20 overflow-hidden rounded-2xl bg-slate-100">
                                    @if ($order->auction?->primaryImage?->url)
                                        <img src="{{ $order->auction->primaryImage->url }}" alt="{{ $order->auction?->title }}" class="h-full w-full object-cover" loading="lazy" />
                                    @else
                                        <div class="flex h-full items-center justify-center text-xs font-medium uppercase tracking-[0.18em] text-slate-500">Order</div>
                                    @endif
                                </div>
                                <div class="space-y-2">
                                    <div class="flex flex-wrap items-center gap-3">
                                        <h2 class="text-lg font-semibold text-slate-900">{{ $order->auction?->title ?? 'Aukcija bez naslova' }}</h2>
                                        <x-badge :variant="$order->status_badge['color']">{{ $order->status_badge['label'] }}</x-badge>
                                    </div>
                                    <p class="text-sm text-slate-500">Prodavač: {{ $order->seller?->name ?? $order->seller?->email ?? 'Nepoznat prodavač' }}</p>
                                    <div class="flex flex-wrap gap-3 text-sm text-slate-600">
                                        <span>Narudžba #{{ str($order->id)->upper()->substr(0, 8) }}</span>
                                        <span>Gateway: {{ $order->payment_gateway ?? 'nije odabran' }}</span>
                                        <span>{{ $order->created_at?->format('d.m.Y.') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2 text-right">
                                <div class="text-sm text-slate-500">Ukupno</div>
                                <div class="text-2xl font-semibold text-slate-900">{{ number_format($order->total_amount, 2, ',', '.') }} KM</div>
                                @if ($order->auction)
                                    <a href="{{ route('auctions.show', ['auction' => $order->auction->id]) }}" class="link">Otvori aukciju</a>
                                @endif
                            </div>
                        </div>

                        <div class="grid gap-3 border-t border-slate-100 pt-4 text-sm text-slate-600 md:grid-cols-3">
                            <div>
                                <p class="font-medium text-slate-900">Plaćanje</p>
                                <p>{{ $order->payment_status ?? 'pending' }}</p>
                            </div>
                            <div>
                                <p class="font-medium text-slate-900">Dostava</p>
                                <p>{{ $order->shipping_method ?? 'dogovor sa prodavačem' }}</p>
                            </div>
                            <div>
                                <p class="font-medium text-slate-900">Grad isporuke</p>
                                <p>{{ $order->shipping_city ?? 'nije postavljen' }}</p>
                            </div>
                        </div>

                        <div class="grid gap-4 lg:grid-cols-[0.8fr_1.2fr]">
                            <div class="space-y-3 rounded-2xl bg-slate-50 px-4 py-4 text-sm text-slate-700">
                                <p class="font-medium text-slate-900">Operativni status</p>
                                @if ($order->payment_deadline_at && $order->status === 'pending_payment')
                                    <p>Rok plaćanja: {{ $order->payment_deadline_at->format('d.m.Y. H:i') }}</p>
                                @endif
                                @if ($order->shipment?->tracking_number)
                                    <p>Tracking: {{ $order->shipment->tracking_number }}</p>
                                @endif
                                @if ($order->dispute)
                                    <x-badge variant="danger">Otvoren spor</x-badge>
                                    <p class="mt-2">{{ $order->dispute->reason }}</p>
                                    <a href="{{ route('orders.dispute.show', ['order' => $order->id]) }}" class="link">Otvori detalj spora</a>
                                @endif
                            </div>

                            <div class="space-y-3">
                                <p class="font-medium text-slate-900">Timeline</p>
                                <div class="grid gap-2 text-sm text-slate-700">
                                    <div class="rounded-2xl bg-slate-50 px-4 py-3">Narudžba kreirana · {{ $order->created_at?->format('d.m.Y. H:i') }}</div>
                                    @if ($order->paid_at)
                                        <div class="rounded-2xl bg-slate-50 px-4 py-3">Plaćanje evidentirano · {{ $order->paid_at->format('d.m.Y. H:i') }}</div>
                                    @endif
                                    @if ($order->shipment?->status_label)
                                        <div class="rounded-2xl bg-slate-50 px-4 py-3">Pošiljka · {{ $order->shipment->status_label }}</div>
                                    @elseif ($order->shipment?->status)
                                        <div class="rounded-2xl bg-slate-50 px-4 py-3">Pošiljka · {{ $order->shipment->status }}</div>
                                    @endif
                                    @if ($order->dispute)
                                        <div class="rounded-2xl bg-red-50 px-4 py-3">Spor otvoren · {{ $order->dispute->status }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </x-card>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection
