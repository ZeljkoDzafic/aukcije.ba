@extends('layouts.guest')

@section('title', 'Pretraga')

@section('content')
<section class="mx-auto max-w-4xl px-4 py-16 text-center sm:px-6 lg:px-8">
    <x-card class="space-y-5">
        <h1 class="text-3xl font-semibold text-slate-900">Pametna pretraga aukcija</h1>
        <p class="text-slate-600">Search ekran će koristiti iste listing komponente, ali sa fokusom na relevanciju i saved searches.</p>
        <x-input name="search" label="Šta tražiš?" placeholder="Vintage sat, iPhone, fotoaparat..." />
        <x-button>Pokreni pretragu</x-button>
    </x-card>
</section>
@endsection
