@extends('layouts.app')

@section('title', 'Wallet')
@section('meta')
    <x-seo-meta
        title="Wallet - Aukcije.ba"
        description="Pregled raspoloživog balansa, escrow sredstava, uplata, isplata i historije wallet transakcija."
    />
@endsection

@section('content')
<section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="space-y-6">
        <div class="panel-hero-muted">
            <p class="panel-kicker">Wallet desk</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-900">Novčanik i transakcije</h1>
            <p class="mt-2 max-w-2xl text-slate-600">Pregled raspoloživog balansa, escrow sredstava, uplata, isplata i historije wallet transakcija.</p>
        </div>

        <div class="panel-shell">
            @livewire('wallet-manager')
        </div>
    </div>
</section>
@endsection
