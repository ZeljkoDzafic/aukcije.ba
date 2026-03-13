@extends('layouts.app')

@section('title', 'Privatnost i podaci')

@section('content')
<section class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="space-y-6">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Privatnost</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-900">Privatnost i podaci</h1>
            <p class="mt-2 text-slate-600">Preuzmi svoje podatke, podesi vidljivost profila i pošalji zahtjev za brisanje računa kad nema otvorenih transakcija ili sporova.</p>
        </div>

        @if (session('status'))
            <x-alert variant="success">{{ session('status') }}</x-alert>
        @endif

        @if (session('error'))
            <x-alert variant="danger">{{ session('error') }}</x-alert>
        @endif

        <x-card class="space-y-6">
            <div class="flex flex-col gap-4 rounded-3xl bg-blue-50 p-5 md:flex-row md:items-start md:justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-slate-900">Preuzimanje podataka</h2>
                    <p class="mt-2 text-sm text-slate-600">Preuzmi osnovni izvoz naloga u JSON formatu za ličnu arhivu ili dalju obradu.</p>
                    <p class="mt-2 text-xs text-slate-500">Sadrži profil, postavke obavijesti i osnovne zbirne podatke o aktivnostima naloga.</p>
                </div>
                <form method="POST" action="{{ route('settings.gdpr.export') }}">
                    @csrf
                    <x-button type="submit">Preuzmi podatke</x-button>
                </form>
            </div>

            <form method="POST" action="{{ route('settings.gdpr.settings') }}" class="space-y-4">
                @csrf

                <h2 class="text-xl font-semibold text-slate-900">Postavke privatnosti</h2>

                <label class="flex items-center justify-between gap-4 rounded-2xl border border-slate-200 px-4 py-4">
                    <div>
                        <p class="font-medium text-slate-900">Javni profil</p>
                        <p class="text-sm text-slate-500">Dozvoli da reputacija, osnovni profil i seller aktivnost budu vidljivi drugim korisnicima.</p>
                    </div>
                    <input type="checkbox" name="profile_public" value="1" class="h-5 w-5 rounded border-slate-300 text-trust-700 focus:ring-trust-400" @checked($settings['profile_public'])>
                </label>

                <label class="flex items-center justify-between gap-4 rounded-2xl border border-slate-200 px-4 py-4">
                    <div>
                        <p class="font-medium text-slate-900">Email obavještenja</p>
                        <p class="text-sm text-slate-500">Zadrži operativne signale i sistemske poruke na emailu.</p>
                    </div>
                    <input type="checkbox" name="email_notifications" value="1" class="h-5 w-5 rounded border-slate-300 text-trust-700 focus:ring-trust-400" @checked($settings['email_notifications'])>
                </label>

                <label class="flex items-center justify-between gap-4 rounded-2xl border border-slate-200 px-4 py-4">
                    <div>
                        <p class="font-medium text-slate-900">SMS obavještenja</p>
                        <p class="text-sm text-slate-500">Koristi SMS samo za važne signale ako je telefon već verifikovan.</p>
                    </div>
                    <input type="checkbox" name="sms_notifications" value="1" class="h-5 w-5 rounded border-slate-300 text-trust-700 focus:ring-trust-400" @checked($settings['sms_notifications'])>
                </label>

                <div class="flex justify-end">
                    <x-button type="submit">Sačuvaj postavke</x-button>
                </div>
            </form>
        </x-card>

        <x-card class="space-y-5">
            <div>
                <h2 class="text-xl font-semibold text-slate-900">Brisanje računa</h2>
                <p class="mt-2 text-sm text-slate-600">Brisanje računa anonimizira lične podatke. Finansijski i transakcioni trag koji zakon nalaže ostaje sačuvan u anonimizovanom obliku.</p>
            </div>

            <div class="rounded-3xl border border-red-200 bg-red-50 p-5 text-sm text-red-800">
                Zahtjev prolazi samo ako nema otvorenih sporova, uplata koje čekaju i aktivnih transakcija.
            </div>

            <form method="POST" action="{{ route('settings.gdpr.delete') }}" onsubmit="return confirm('Ova akcija anonimizira račun i odjavljuje te sa platforme. Želiš li nastaviti?');">
                @csrf
                @method('DELETE')
                <x-button type="submit" variant="danger">Obriši račun</x-button>
            </form>
        </x-card>
    </div>
</section>
@endsection
