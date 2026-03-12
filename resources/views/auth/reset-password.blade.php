@extends('layouts.guest')

@section('title', 'Nova lozinka')

@section('content')
<section class="mx-auto max-w-md px-4 py-16">
    <x-card class="space-y-5">
        <h1 class="text-2xl font-semibold text-slate-900">Postavi novu lozinku</h1>
        <x-input name="email" type="email" label="Email adresa" />
        <x-input name="password" type="password" label="Nova lozinka" />
        <x-input name="password_confirmation" type="password" label="Potvrda lozinke" />
        <x-button class="w-full">Sačuvaj novu lozinku</x-button>
    </x-card>
</section>
@endsection
