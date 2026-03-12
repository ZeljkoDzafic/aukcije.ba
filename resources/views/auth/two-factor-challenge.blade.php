@extends('layouts.guest')

@section('title', 'Dvofaktorska provjera')

@section('content')
<section class="mx-auto max-w-md px-4 py-16">
    <x-card class="space-y-5">
        <h1 class="text-2xl font-semibold text-slate-900">Unesi MFA kod</h1>
        <p class="text-sm text-slate-600">Seller i verified seller nalozi koriste dodatnu zaštitu kroz authenticator aplikaciju ili recovery code.</p>
        <x-input name="code" label="Jednokratni kod" placeholder="123 456" />
        <x-input name="recovery_code" label="Recovery code" hint="Koristi samo ako nemaš pristup authenticator aplikaciji." />
        <x-button class="w-full">Potvrdi pristup</x-button>
    </x-card>
</section>
@endsection
