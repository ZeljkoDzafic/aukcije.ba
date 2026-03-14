@extends('layouts.app')

@section('title', 'Praćene aukcije')

@section('content')
<section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="space-y-6">
        <div class="panel-hero-muted">
            <p class="panel-kicker">Watchlist</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-900">Praćene aukcije</h1>
            <p class="mt-2 max-w-2xl text-slate-600">Brzi pregled svih artikala koje pratiš, sa signalom gdje treba reagovati prije isteka.</p>
        </div>

        <div class="panel-shell">
            @livewire('watchlist')
        </div>
    </div>
</section>
@endsection
