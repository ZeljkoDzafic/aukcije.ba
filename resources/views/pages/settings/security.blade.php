@extends('layouts.app')

@section('title', 'Sigurnost naloga')

@section('content')
<section class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="space-y-6">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Sigurnost</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-900">Zaštita naloga</h1>
            <p class="mt-2 text-slate-600">Uključi dodatnu zaštitu za prijavu i sačuvaj recovery kodove.</p>
        </div>

        @livewire('auth.two-factor-enrollment')
    </div>
</section>
@endsection
