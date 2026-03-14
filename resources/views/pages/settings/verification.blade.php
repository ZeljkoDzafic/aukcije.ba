@extends('layouts.app')

@section('title', 'Verifikacija naloga')

@section('content')
<section class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="space-y-6">
        <div class="panel-hero-muted">
            <p class="panel-kicker">Verifikacija</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-900">KYC i pristup funkcijama</h1>
            <p class="mt-2 max-w-2xl text-slate-600">Pregledaj trenutni nivo verifikacije i dovrši korake koji otključavaju licitiranje, prodaju i isplate.</p>
        </div>

        <div class="panel-shell">
            @livewire('kyc.status-dashboard')
        </div>
    </div>
</section>
@endsection
