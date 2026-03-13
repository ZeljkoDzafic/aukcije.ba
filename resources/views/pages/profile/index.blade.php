@extends('layouts.app')

@section('title', 'Profil')

@section('content')
<section class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
        <x-card class="space-y-6">
            <div class="flex items-center gap-4">
                <x-avatar :name="$profile['name']" size="lg" />
                <div>
                    <h1 class="text-2xl font-semibold text-slate-900">{{ $profile['name'] }}</h1>
                    <p class="text-slate-600">{{ ucfirst($profile['role_summary']) }} · KYC nivo {{ $profile['kyc_level'] }} · Trust {{ $profile['trust_score'] }}</p>
                    <p class="mt-1 text-sm text-slate-500">Primarni fokus: {{ $profile['primary_focus_label'] }}</p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl bg-slate-50 px-4 py-3">
                    <p class="text-sm text-slate-500">Wallet balans</p>
                    <p class="mt-1 text-lg font-semibold text-slate-900">{{ $profile['wallet_balance'] }}</p>
                </div>
                <div class="rounded-2xl bg-slate-50 px-4 py-3">
                    <p class="text-sm text-slate-500">Grad / država</p>
                    <p class="mt-1 text-lg font-semibold text-slate-900">{{ $profile['city'] ?: 'Nije postavljeno' }}{{ $profile['country'] ? ', '.$profile['country'] : '' }}</p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-2xl border border-slate-200 px-4 py-3">
                    <p class="text-sm text-slate-500">Javne ocjene</p>
                    <p class="mt-1 text-lg font-semibold text-slate-900">{{ $profile['ratings_count'] }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 px-4 py-3">
                    <p class="text-sm text-slate-500">Seller bedž</p>
                    <p class="mt-1 text-lg font-semibold text-slate-900">{{ $profile['seller_badge'] ?: 'Standard' }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 px-4 py-3">
                    <p class="text-sm text-slate-500">Aktivne seller aukcije</p>
                    <p class="mt-1 text-lg font-semibold text-slate-900">{{ $profile['active_seller_auctions'] }}</p>
                </div>
            </div>
        </x-card>

        <x-card class="space-y-6">
            <h2 class="text-xl font-semibold text-slate-900">Podaci naloga</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                <x-input name="name" label="Ime i prezime" :value="$profile['name']" />
                <x-input name="email" type="email" label="Email" :value="$profile['email']" />
                <x-input name="phone" label="Telefon" :value="$profile['phone'] ?: 'Nije postavljen'" />
                <x-input name="roles" label="Uloge" :value="$profile['role_summary']" />
                <x-input name="focus" label="Primarni fokus" :value="$profile['primary_focus_label']" />
            </div>

            <div class="flex flex-wrap gap-3">
                <x-button variant="ghost" :href="route('wallet.index')">Otvori wallet</x-button>
                <x-button variant="ghost" :href="route('orders.index')">Moje narudžbe</x-button>
                <x-button variant="ghost" :href="route('searches.index')">Moje pretrage</x-button>
                <x-button variant="ghost" :href="route('notifications.index')">Obavijesti</x-button>
                <x-button variant="ghost" :href="route('settings.notifications')">Postavke obavijesti</x-button>
                <x-button variant="ghost" :href="route('settings.security')">Sigurnost naloga</x-button>
                <x-button variant="ghost" :href="route('settings.verification')">Verifikacija</x-button>
                <x-button variant="ghost" :href="route('settings.gdpr')">Privatnost i podaci</x-button>
                @if ($profile['can_sell'])
                    <x-button variant="ghost" :href="route('sellers.show', ['user' => auth()->id()])">Javni profil</x-button>
                    <x-button :href="route('seller.dashboard')">Prodajni panel</x-button>
                @endif
            </div>
        </x-card>
    </div>
</section>
@endsection
