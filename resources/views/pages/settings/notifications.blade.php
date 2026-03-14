@extends('layouts.app')

@section('title', 'Postavke obavijesti')

@section('content')
<section class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="space-y-6">
        <div class="panel-hero-muted">
            <p class="panel-kicker">Preferences</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-900">Postavke obavijesti</h1>
            <p class="mt-2 max-w-2xl text-slate-600">Odredi koje email, push i SMS signale želiš primati sa platforme.</p>
        </div>

        @if (session('status'))
            <x-alert variant="success">{{ session('status') }}</x-alert>
        @endif

        <x-card class="panel-shell space-y-6">
            <form method="POST" action="{{ route('settings.notifications.update') }}" class="space-y-6">
                @csrf

                <div class="grid gap-4">
                    @foreach ([
                        'email_outbid' => 'Email za nadlicitiranje',
                        'email_auction_ended' => 'Email kada aukcija završi',
                        'email_ending_soon' => 'Email kad aukcija uskoro ističe',
                        'email_messages' => 'Email za nove poruke',
                        'email_saved_searches' => 'Email za spremljene pretrage',
                        'email_shipping_updates' => 'Email za shipping izmjene',
                        'email_disputes' => 'Email za sporove i eskalacije',
                        'push_enabled' => 'Push obavijesti',
                        'sms_enabled' => 'SMS obavijesti',
                    ] as $key => $label)
                        <label class="rounded-[1.5rem] border border-slate-200 bg-slate-50/70 px-4 py-4">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="font-medium text-slate-900">{{ $label }}</p>
                                    <p class="text-sm text-slate-500">Uključi ili isključi ovaj kanal obavijesti.</p>
                                </div>
                                <input type="checkbox" name="{{ $key }}" value="1" class="h-5 w-5 rounded border-slate-300 text-trust-700 focus:ring-trust-400" @checked($preferences[$key] ?? false)>
                            </div>
                        </label>
                    @endforeach
                </div>

                <div class="flex justify-end">
                    <x-button type="submit">Sačuvaj postavke</x-button>
                </div>
            </form>
        </x-card>
    </div>
</section>
@endsection
