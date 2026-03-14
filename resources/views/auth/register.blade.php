@extends('layouts.guest')

@section('title', 'Registracija')

@section('content')
<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="mx-auto grid max-w-6xl gap-8 xl:grid-cols-[0.95fr_1.05fr]">
        <div class="market-sheen rounded-[2rem] border border-slate-200 bg-[radial-gradient(circle_at_top_left,_rgba(14,165,233,0.14),_transparent_28%),radial-gradient(circle_at_bottom_right,_rgba(245,158,11,0.15),_transparent_26%),linear-gradient(180deg,#ffffff_0%,#f8fafc_100%)] p-8 shadow-sm">
            <div class="space-y-4">
                <x-badge variant="trust">Jedan nalog, obje uloge</x-badge>
                <h1 class="text-4xl font-semibold text-slate-900">Registruj novi račun</h1>
                <p class="text-slate-600">Na jednom nalogu možeš kupovati i prodavati. Registracija je sada dio istog marketplace jezika, sa jasnijim buyer/seller izborom i manje generičkim onboardingom.</p>
            </div>

            <div class="mt-8 grid gap-4">
                <div class="rounded-2xl bg-emerald-50 px-4 py-4 text-sm text-emerald-900">Buyer dashboard, seller studio i wallet žive na istom računu.</div>
                <div class="rounded-2xl bg-sky-50 px-4 py-4 text-sm text-sky-900">Seller onboarding može početi odmah, bez otvaranja drugog profila.</div>
                <div class="rounded-2xl bg-amber-50 px-4 py-4 text-sm text-amber-900">Kasnije možeš širiti nalog sa verifikacijom, ratingom i prodajnim trust signalima.</div>
            </div>
        </div>

        <x-card class="market-sheen space-y-6 bg-[linear-gradient(180deg,#ffffff_0%,#f8fafc_100%)]">
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
