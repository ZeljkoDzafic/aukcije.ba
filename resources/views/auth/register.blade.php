@extends('layouts.guest')

@section('title', 'Registracija')

@section('content')
<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-2xl space-y-8">
        <div class="space-y-3 text-center">
            <x-badge variant="trust">Kupac ili prodavac</x-badge>
            <h1 class="text-3xl font-semibold text-slate-900">Registruj novi račun</h1>
            <p class="text-slate-600">Seller registracija vodi u KYC i wallet setup, a buyer račun je spreman za licitiranje odmah nakon verifikacije emaila.</p>
        </div>

        <x-card class="space-y-6">
            <div class="grid gap-4 sm:grid-cols-2">
                <x-input name="name" label="Ime i prezime" placeholder="Amar Hadžić" />
                <x-input name="email" type="email" label="Email adresa" placeholder="ime@primjer.ba" />
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <x-input name="password" type="password" label="Lozinka" placeholder="Minimalno 8 karaktera" />
                <x-input name="password_confirmation" type="password" label="Potvrda lozinke" placeholder="Ponovi lozinku" />
            </div>

            <div class="space-y-3">
                <p class="label">Tip računa</p>
                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="card-hover cursor-pointer border-2 border-trust-600 bg-trust-50 p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-lg font-semibold text-slate-900">Kupac</p>
                                <p class="mt-2 text-sm text-slate-600">Pretraga, licitiranje, watchlist i poruke.</p>
                            </div>
                            <input checked type="radio" name="account_type" class="mt-1 text-trust-600 focus:ring-trust-500">
                        </div>
                    </label>
                    <label class="card-hover cursor-pointer p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-lg font-semibold text-slate-900">Prodavac</p>
                                <p class="mt-2 text-sm text-slate-600">KYC verifikacija, wallet setup i seller dashboard.</p>
                            </div>
                            <input type="radio" name="account_type" class="mt-1 text-trust-600 focus:ring-trust-500">
                        </div>
                    </label>
                </div>
            </div>

            <label class="inline-flex items-start gap-3 text-sm text-slate-600">
                <input type="checkbox" class="mt-1 rounded border-slate-300 text-trust-600 focus:ring-trust-500">
                <span>Prihvatam uslove korištenja i pravila platforme.</span>
            </label>

            <x-button class="w-full">Kreiraj račun</x-button>
        </x-card>
    </div>
</section>
@endsection
