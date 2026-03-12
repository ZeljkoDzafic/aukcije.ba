@extends('layouts.guest')

@section('title', 'Reset lozinke')

@section('content')
<section class="mx-auto max-w-md px-4 py-16">
    <x-card class="space-y-5">
        <h1 class="text-2xl font-semibold text-slate-900">Zaboravili ste lozinku?</h1>
        <p class="text-sm text-slate-600">Unesite email adresu i poslat ćemo vam link za reset lozinke.</p>
        <x-input name="email" type="email" label="Email adresa" />
        <x-button class="w-full">Pošalji link za reset</x-button>
    </x-card>
</section>
@endsection
