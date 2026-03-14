@extends('layouts.app')

@section('title', 'Sigurnost naloga')

@section('content')
<section class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="space-y-6">
        <div class="panel-hero-muted">
            <p class="panel-kicker">Sigurnost</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-900">Zaštita naloga</h1>
            <p class="mt-2 max-w-2xl text-slate-600">Uključi dodatnu zaštitu za prijavu i sačuvaj recovery kodove.</p>
        </div>

        <div class="panel-shell">
            @livewire('auth.two-factor-enrollment')
        </div>
    </div>
</section>
@endsection
