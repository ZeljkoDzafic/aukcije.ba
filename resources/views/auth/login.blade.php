@extends('layouts.guest')

@section('title', 'Prijava')

@section('content')
<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="mx-auto grid max-w-5xl gap-8 xl:grid-cols-[0.95fr_1.05fr]">
        <div class="market-sheen rounded-[2rem] border border-slate-200 bg-[radial-gradient(circle_at_top_left,_rgba(14,165,233,0.14),_transparent_28%),radial-gradient(circle_at_bottom_right,_rgba(245,158,11,0.15),_transparent_26%),linear-gradient(180deg,#ffffff_0%,#f8fafc_100%)] p-8 shadow-sm">
            <div class="space-y-4">
                <x-badge variant="trust">Povratak na platformu</x-badge>
                <h1 class="text-4xl font-semibold text-slate-900">Prijavi se na svoj račun</h1>
                <p class="text-slate-600">Kupci nastavljaju na dashboard, selleri u seller studio, a administracija u moderacijski panel. Login ekran sada je dio istog marketplace ritma, ne odvojeni utilitarni korak.</p>
            </div>

            <div class="mt-8 grid gap-4">
                <div class="rounded-2xl bg-emerald-50 px-4 py-4 text-sm text-emerald-900">Escrow i trust funkcije nastavljaju odmah po prijavi.</div>
                <div class="rounded-2xl bg-sky-50 px-4 py-4 text-sm text-sky-900">Seller studio i buyer dashboard dijele isti nalog.</div>
                <div class="rounded-2xl bg-amber-50 px-4 py-4 text-sm text-amber-900">Test nalozi su dostupni za lokalni QA i pregled role-based flowa.</div>
            </div>
        </div>

        <div class="space-y-6">
            <x-card class="market-sheen space-y-5 bg-[linear-gradient(180deg,#ffffff_0%,#f8fafc_100%)]">
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf
                    <x-input name="email" type="email" label="Email adresa" :value="old('email')" placeholder="ime@primjer.ba" />
                    @error('email')
                        <p class="-mt-3 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <x-input name="password" type="password" label="Lozinka" placeholder="••••••••" />
                    @error('password')
                        <p class="-mt-3 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <div class="flex items-center justify-between text-sm">
                        <label class="inline-flex items-center gap-2 text-slate-600">
                            <input type="checkbox" name="remember" class="rounded border-slate-300 text-trust-600 focus:ring-trust-500">
                            <span>Zapamti me</span>
                        </label>
                        <a href="{{ url('/forgot-password') }}" class="link">Zaboravili ste lozinku?</a>
                    </div>

                    <div class="space-y-3">
                        <x-button class="w-full" type="submit">Prijavi se</x-button>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                            Demo test korisnici koriste lozinku <span class="font-semibold text-slate-900">Test12345!</span>.
                        </div>
                    </div>
                </form>
            </x-card>

            <p class="text-center text-sm text-slate-600">Nemaš račun? <a href="{{ route('register') }}" class="link">Registruj se</a></p>
        </div>
    </div>
</section>
@endsection
