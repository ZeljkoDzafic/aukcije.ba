@extends('layouts.app')

@section('title', 'Detalj spora')

@section('content')
<section class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="space-y-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Narudžbe i zaštita kupca</p>
                <h1 class="mt-2 text-3xl font-semibold text-slate-900">Detalj spora</h1>
                <p class="mt-2 text-slate-600">Pregled razloga, toka isporuke i komunikacije za spor na narudžbi.</p>
            </div>
            <a href="{{ route('orders.index') }}" class="link">Nazad na narudžbe</a>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1.05fr_0.95fr]">
            <x-card class="space-y-4">
                <div class="flex items-start gap-4">
                    <div class="h-24 w-24 overflow-hidden rounded-2xl bg-slate-100">
                        @if ($order->auction?->primaryImage?->url)
                            <img src="{{ $order->auction->primaryImage->url }}" alt="{{ $order->auction?->title }}" class="h-full w-full object-cover" loading="lazy" />
                        @else
                            <div class="flex h-full items-center justify-center text-xs font-medium uppercase tracking-[0.18em] text-slate-500">Spor</div>
                        @endif
                    </div>
                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center gap-3">
                            <h2 class="text-xl font-semibold text-slate-900">{{ $order->auction?->title ?? 'Aukcija bez naslova' }}</h2>
                            <x-badge variant="danger">{{ ucfirst((string) $dispute->status) }}</x-badge>
                        </div>
                        <p class="text-sm text-slate-600">Prodavač: {{ $order->seller?->name ?? $order->seller?->email ?? 'Nepoznat prodavač' }}</p>
                        <p class="text-sm text-slate-600">Razlog: {{ str($dispute->reason)->replace('_', ' ')->headline() }}</p>
                        <p class="text-sm text-slate-600">Otvoren: {{ $dispute->created_at?->format('d.m.Y. H:i') }}</p>
                    </div>
                </div>

                <div class="rounded-3xl bg-red-50 px-5 py-4 text-sm text-slate-700">
                    <p class="font-medium text-slate-900">Opis spora</p>
                    <p class="mt-2">{{ $dispute->description ?: 'Kupac je otvorio spor i čeka daljnju obradu.' }}</p>
                </div>

                <div class="grid gap-3 md:grid-cols-3 text-sm text-slate-700">
                    <div class="rounded-2xl bg-slate-50 px-4 py-4">
                        <p class="font-medium text-slate-900">Plaćanje</p>
                        <p class="mt-1">{{ $order->payment_status ?? 'pending' }}</p>
                        @if ($order->payment_deadline_at)
                            <p class="mt-2 text-xs text-slate-500">Rok: {{ $order->payment_deadline_at->format('d.m.Y. H:i') }}</p>
                        @endif
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-4">
                        <p class="font-medium text-slate-900">Dostava</p>
                        <p class="mt-1">{{ $order->shipping_method ?? 'dogovor sa prodavačem' }}</p>
                        @if ($order->shipment?->tracking_number)
                            <p class="mt-2 text-xs text-slate-500">Tracking: {{ $order->shipment->tracking_number }}</p>
                        @endif
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-4">
                        <p class="font-medium text-slate-900">Vrijednost</p>
                        <p class="mt-1">{{ number_format((float) $order->total_amount, 2, ',', '.') }} KM</p>
                        <p class="mt-2 text-xs text-slate-500">Narudžba #{{ str($order->id)->upper()->substr(0, 8) }}</p>
                    </div>
                </div>
            </x-card>

            <div class="space-y-6">
                <x-card class="space-y-4">
                    <h2 class="text-lg font-semibold text-slate-900">Sljedeći koraci</h2>
                    <div class="space-y-3">
                        @foreach ($disputeActions as $action)
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
                    <h2 class="text-lg font-semibold text-slate-900">Timeline</h2>
                    <div class="space-y-3 text-sm text-slate-700">
                        <div class="rounded-2xl bg-slate-50 px-4 py-3">Narudžba kreirana · {{ $order->created_at?->format('d.m.Y. H:i') }}</div>
                        @if ($order->paid_at)
                            <div class="rounded-2xl bg-slate-50 px-4 py-3">Plaćanje evidentirano · {{ $order->paid_at->format('d.m.Y. H:i') }}</div>
                        @endif
                        @if ($order->shipment?->status_label || $order->shipment?->status)
                            <div class="rounded-2xl bg-slate-50 px-4 py-3">Pošiljka · {{ $order->shipment->status_label ?? $order->shipment->status }}</div>
                        @endif
                        <div class="rounded-2xl bg-red-50 px-4 py-3">Spor otvoren · {{ $dispute->created_at?->format('d.m.Y. H:i') }}</div>
                        @if ($dispute->escalated_at)
                            <div class="rounded-2xl bg-amber-50 px-4 py-3">Eskalirano moderatoru · {{ $dispute->escalated_at->format('d.m.Y. H:i') }}</div>
                        @endif
                        @if ($dispute->resolved_at)
                            <div class="rounded-2xl bg-emerald-50 px-4 py-3">Spor riješen · {{ $dispute->resolved_at->format('d.m.Y. H:i') }}</div>
                        @endif
                    </div>
                </x-card>

                <x-card class="space-y-4">
                    <h2 class="text-lg font-semibold text-slate-900">Komunikacija i evidencija</h2>
                    <div class="space-y-3 text-sm text-slate-700">
                        @forelse ($disputeMessages as $message)
                            <div class="rounded-2xl border border-slate-200 px-4 py-3">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="font-medium text-slate-900">{{ $message->user?->name ?? $message->user?->email ?? 'Sistem / moderator' }}</p>
                                    <p class="text-xs text-slate-500">{{ $message->created_at?->format('d.m.Y. H:i') }}</p>
                                </div>
                                <p class="mt-2">{{ $message->message }}</p>
                            </div>
                        @empty
                            <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">Još nema dodatnih poruka u sporu. Sve važne promjene prati kroz obavijesti i narudžbe.</div>
                        @endforelse
                    </div>
                </x-card>
            </div>
        </div>
    </div>
</section>
@endsection
