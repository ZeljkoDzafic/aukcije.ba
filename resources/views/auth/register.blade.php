@extends('layouts.guest')

@section('title', 'Registracija')

@section('content')
<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-2xl space-y-8">
        <div class="space-y-3 text-center">
            <x-badge variant="trust">Jedan nalog, obje uloge</x-badge>
            <h1 class="text-3xl font-semibold text-slate-900">Registruj novi račun</h1>
            <p class="text-slate-600">Na jednom nalogu možeš kupovati i prodavati. Ovdje samo biraš šta ćeš pretežno koristiti na početku.</p>
        </div>

        <x-card class="space-y-6">
            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <x-input name="name" label="Ime i prezime" :value="old('name')" placeholder="Aleksa Kovačević" />
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <x-input name="email" type="email" label="Email adresa" :value="old('email')" placeholder="ime@primjer.ba" />
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <x-input name="password" type="password" label="Lozinka" placeholder="Minimalno 8 karaktera" />
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <x-input name="password_confirmation" type="password" label="Potvrda lozinke" placeholder="Ponovi lozinku" />
                    </div>
                </div>

                <div class="space-y-3">
                    <p class="label">Primarni fokus naloga</p>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="card-hover cursor-pointer border-2 p-5 {{ old('marketplace_focus', old('role', 'buyer')) === 'buyer' ? 'border-trust-600 bg-trust-50' : '' }}">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-lg font-semibold text-slate-900">Kupovina</p>
                                    <p class="mt-2 text-sm text-slate-600">Pretraga, licitiranje, praćenje aukcija i poruke. Prodaju možeš uključiti kasnije na istom nalogu.</p>
                                </div>
                                <input {{ old('marketplace_focus', old('role', 'buyer')) === 'buyer' ? 'checked' : '' }} type="radio" name="marketplace_focus" value="buyer" class="mt-1 text-trust-600 focus:ring-trust-500">
                            </div>
                        </label>
                        <label class="card-hover cursor-pointer border-2 p-5 {{ old('marketplace_focus', old('role')) === 'seller' ? 'border-trust-600 bg-trust-50' : '' }}">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-lg font-semibold text-slate-900">Prodaja</p>
                                    <p class="mt-2 text-sm text-slate-600">Odmah dobijaš kupovne i prodajne funkcije, uz KYC, novčanik i prodajni panel kao početni fokus.</p>
                                </div>
                                <input {{ old('marketplace_focus', old('role')) === 'seller' ? 'checked' : '' }} type="radio" name="marketplace_focus" value="seller" class="mt-1 text-trust-600 focus:ring-trust-500">
                            </div>
                        </label>
                    </div>
                    @error('marketplace_focus')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('role')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <label class="inline-flex items-start gap-3 text-sm text-slate-600">
                    <input required type="checkbox" class="mt-1 rounded border-slate-300 text-trust-600 focus:ring-trust-500">
                    <span>Prihvatam uslove korištenja i pravila platforme.</span>
                </label>

                <x-button class="w-full" type="submit">Kreiraj račun</x-button>
            </form>
        </x-card>
    </div>
</section>
@endsection
