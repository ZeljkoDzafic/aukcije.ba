@extends('layouts.guest')

@section('title', 'Prijava')

@section('content')
<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-md space-y-6">
        <div class="space-y-3 text-center">
            <x-badge variant="trust">Povratak na platformu</x-badge>
            <h1 class="text-3xl font-semibold text-slate-900">Prijavi se na svoj račun</h1>
            <p class="text-slate-600">Kupci nastavljaju na dashboard, selleri u seller studio, a administracija u moderacijski panel.</p>
        </div>

        <x-card class="space-y-5">
            <x-input name="email" type="email" label="Email adresa" placeholder="ime@primjer.ba" />
            <x-input name="password" type="password" label="Lozinka" placeholder="••••••••" />

            <div class="flex items-center justify-between text-sm">
                <label class="inline-flex items-center gap-2 text-slate-600">
                    <input type="checkbox" class="rounded border-slate-300 text-trust-600 focus:ring-trust-500">
                    <span>Zapamti me</span>
                </label>
                <a href="{{ url('/forgot-password') }}" class="link">Zaboravili ste lozinku?</a>
            </div>

            <div class="space-y-3">
                <x-button class="w-full">Prijavi se</x-button>
                <x-button variant="ghost" class="w-full">Prijava putem Google naloga</x-button>
            </div>
        </x-card>

        <p class="text-center text-sm text-slate-600">Nemaš račun? <a href="{{ route('register') }}" class="link">Registruj se</a></p>
    </div>
</section>
@endsection
