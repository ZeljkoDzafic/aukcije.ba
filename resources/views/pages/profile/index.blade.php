@extends('layouts.app')

@section('title', 'Profil')

@section('content')
<section class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
    <x-card class="space-y-6">
        <div class="flex items-center gap-4">
            <x-avatar name="Kupac Demo" size="lg" />
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Kupac Demo</h1>
                <p class="text-slate-600">Email verified · KYC nivo 1 · 4.8 trust score</p>
            </div>
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <x-input name="name" label="Ime i prezime" value="Kupac Demo" />
            <x-input name="email" type="email" label="Email" value="kupac@example.test" />
        </div>
    </x-card>
</section>
@endsection
