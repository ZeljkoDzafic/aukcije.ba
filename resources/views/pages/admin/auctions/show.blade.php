@extends('layouts.admin')

@section('title', 'Detalj aukcije')
@section('admin_heading', 'Detalj aukcije')

@section('content')
@php
    $auctionId = request()->route('auction');
@endphp
<section class="space-y-8">
    <x-card class="space-y-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm uppercase tracking-[0.18em] text-slate-500">Auction #2041</p>
                <h1 class="mt-2 text-2xl font-semibold text-slate-900">Rolex Datejust 36</h1>
            </div>
        </div>
        <div class="grid gap-4 sm:grid-cols-4">
            <div><p class="text-sm text-slate-500">Status</p><p class="mt-1 font-medium text-slate-900">Reported</p></div>
            <div><p class="text-sm text-slate-500">Kategorija</p><p class="mt-1 font-medium text-slate-900">Satovi</p></div>
            <div><p class="text-sm text-slate-500">Seller</p><p class="mt-1 font-medium text-slate-900">Amar Hadžić</p></div>
            <div><p class="text-sm text-slate-500">Trenutna cijena</p><p class="mt-1 font-medium text-slate-900">5.850,00 BAM</p></div>
        </div>
    </x-card>

    <div class="grid gap-8 xl:grid-cols-[1fr_0.9fr]">
        <x-card class="space-y-4">
            <h2 class="text-xl font-semibold text-slate-900">Bidovi</h2>
            @livewire('admin.auction-detail-manager', ['auctionId' => $auctionId])
        </x-card>

        <x-card class="space-y-4">
            <h2 class="text-xl font-semibold text-slate-900">Moderation queue</h2>
            <x-alert variant="warning">Aukcija je prijavljena zbog sumnje na netačan opis i traži moderator review.</x-alert>
        </x-card>
    </div>
</section>
@endsection
