@extends('layouts.guest')

@section('title', 'Verifikuj email')

@section('content')
<section class="mx-auto max-w-md px-4 py-16">
    <x-card class="space-y-5">
        <x-badge variant="warning">Obavezno prije licitiranja</x-badge>
        <h1 class="text-2xl font-semibold text-slate-900">Potvrdi email adresu</h1>
        <p class="text-sm text-slate-600">Klikni na link koji smo poslali na email prije nastavka rada na platformi.</p>
        <div class="space-y-3">
            <x-button class="w-full">Pošalji verifikacijski email ponovo</x-button>
            <x-button variant="ghost" class="w-full">Odjavi se</x-button>
        </div>
    </x-card>
</section>
@endsection
