@extends('layouts.admin')

@section('title', 'Detalj aukcije')
@section('admin_heading', 'Detalj aukcije')

@section('content')
@php
    $auctionId = request()->route('auction');
    $auctionRecord = $auctionRecord ?? null;
    $decisionHistory = $decisionHistory ?? collect();
    $auctionTitle = $auctionRecord?->title ?? 'Rolex Datejust 36';
    $auctionStatus = $auctionRecord?->status ? str($auctionRecord->status)->replace('_', ' ')->headline() : 'Reported';
    $auctionCategory = $auctionRecord?->category?->name ?? 'Satovi';
    $auctionSeller = $auctionRecord?->seller?->name ?? 'Aleksa K.';
    $auctionPrice = number_format((float) ($auctionRecord?->current_price ?? 5850), 2, ',', '.');
@endphp
<section class="space-y-8">
    <x-card class="space-y-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm uppercase tracking-[0.18em] text-slate-500">Auction #{{ str((string) ($auctionRecord?->id ?? 2041))->upper()->substr(0, 8) }}</p>
                <h1 class="mt-2 text-2xl font-semibold text-slate-900">{{ $auctionTitle }}</h1>
            </div>
        </div>
        <div class="grid gap-4 sm:grid-cols-4">
            <div><p class="text-sm text-slate-500">Status</p><p class="mt-1 font-medium text-slate-900">{{ $auctionStatus }}</p></div>
            <div><p class="text-sm text-slate-500">Kategorija</p><p class="mt-1 font-medium text-slate-900">{{ $auctionCategory }}</p></div>
            <div><p class="text-sm text-slate-500">Seller</p><p class="mt-1 font-medium text-slate-900">{{ $auctionSeller }}</p></div>
            <div><p class="text-sm text-slate-500">Trenutna cijena</p><p class="mt-1 font-medium text-slate-900">{{ $auctionPrice }} BAM</p></div>
        </div>
    </x-card>

    <div class="grid gap-8 xl:grid-cols-[1fr_0.9fr]">
        <div class="space-y-8">
            <x-card class="space-y-4">
                <h2 class="text-xl font-semibold text-slate-900">Bidovi</h2>
                @livewire('admin.auction-detail-manager', ['auctionId' => $auctionId])
            </x-card>

            <x-card class="space-y-4">
                <h2 class="text-xl font-semibold text-slate-900">Decision history</h2>
                <div class="space-y-3">
                    @forelse ($decisionHistory as $entry)
                        <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-700">
                            <span class="font-semibold text-slate-900">{{ strtoupper($entry->action) }}</span>
                            · {{ $entry->admin?->name ?? 'Admin' }}
                            · {{ $entry->created_at?->format('d.m.Y. H:i') }}
                            @if (($entry->metadata['note'] ?? null) !== null)
                                <div class="mt-2 text-slate-600">Napomena: {{ $entry->metadata['note'] }}</div>
                            @endif
                        </div>
                    @empty
                        <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">Još nema evidentiranih moderatorskih odluka za ovu aukciju.</div>
                    @endforelse
                </div>
            </x-card>
        </div>

        <div class="space-y-8">
            <x-card class="space-y-4">
                <h2 class="text-xl font-semibold text-slate-900">Moderation queue</h2>
                <x-alert variant="warning">Aukcija je prijavljena zbog sumnje na netačan opis i traži moderator review.</x-alert>
            </x-card>

            <x-card class="space-y-4">
                <h2 class="text-xl font-semibold text-slate-900">Admin CTA</h2>
                <div class="space-y-3">
                    <div class="rounded-2xl border border-slate-200 px-4 py-4">
                        <p class="font-medium text-slate-900">Traži dodatne dokaze</p>
                        <p class="mt-1 text-sm text-slate-600">Ako listing ima rizične elemente, zatraži serijski broj i dodatne fotografije.</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 px-4 py-4">
                        <p class="font-medium text-slate-900">Provjeri istoriju prodavca</p>
                        <p class="mt-1 text-sm text-slate-600">Koristi user i audit ekran za provjeru prethodnih odluka.</p>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</section>
@endsection
