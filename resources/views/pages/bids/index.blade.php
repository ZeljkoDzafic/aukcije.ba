@extends('layouts.app')

@section('title', 'Moje licitacije')

@section('content')
<section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="space-y-6">
        <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
            <div>
                <h1 class="text-3xl font-semibold text-slate-900">Moje licitacije</h1>
                <p class="mt-2 text-sm text-slate-600">Pregled svih aukcija na kojima učestvujete, sa jasnim statusom i brzim ulazom na detalj.</p>
            </div>
            <x-button :href="route('auctions.index')" variant="ghost">Istraži nove aukcije</x-button>
        </div>

        <x-card class="space-y-4">
            <div class="flex flex-wrap gap-3">
                @foreach ($filters as $filter)
                    <a
                        href="{{ route('bids.index', ['status' => $filter['value'] === 'all' ? null : $filter['value']]) }}"
                        class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-medium transition {{ $status === $filter['value'] ? 'border-trust-500 bg-trust-50 text-trust-800' : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300' }}"
                    >
                        <span>{{ $filter['label'] }}</span>
                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-600">{{ $filter['count'] }}</span>
                    </a>
                @endforeach
            </div>

            <div class="grid gap-4 lg:grid-cols-2">
                @forelse ($bids as $bid)
                    <a href="{{ route('auctions.show', ['auction' => $bid['id']]) }}" class="block rounded-3xl border border-slate-200 bg-white p-5 transition hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-lg">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">{{ $bid['category'] }}</p>
                                <h2 class="mt-2 text-xl font-semibold text-slate-900">{{ $bid['title'] }}</h2>
                                <p class="mt-2 text-sm text-slate-600">{{ $bid['location'] }} · {{ $bid['time'] }}</p>
                            </div>
                            <x-badge :variant="$bid['state'] === 'vodite' ? 'success' : ($bid['state'] === 'nadmaseni' ? 'warning' : 'neutral')">
                                {{ $bid['state'] === 'vodite' ? 'Vodite' : ($bid['state'] === 'nadmaseni' ? 'Nadmašeni' : 'Završeno') }}
                            </x-badge>
                        </div>

                        <div class="mt-5 grid gap-3 sm:grid-cols-3">
                            <div class="rounded-2xl bg-slate-50 px-4 py-3">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Vaša ponuda</p>
                                <p class="mt-1 text-base font-semibold text-slate-900">{{ number_format($bid['your_bid'], 2, ',', '.') }} BAM</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-3">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Trenutno</p>
                                <p class="mt-1 text-base font-semibold text-slate-900">{{ number_format($bid['price'], 2, ',', '.') }} BAM</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-3">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Aktivnost</p>
                                <p class="mt-1 text-base font-semibold text-slate-900">{{ $bid['bids'] }} ponuda · {{ $bid['watchers'] }} prati</p>
                            </div>
                        </div>
                    </a>
                @empty
                    <x-alert variant="info">Nema licitacija za odabrani filter.</x-alert>
                @endforelse
            </div>
        </x-card>
    </div>
</section>
@endsection
