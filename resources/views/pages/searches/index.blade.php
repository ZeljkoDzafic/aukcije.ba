@extends('layouts.app')

@section('title', 'Moje pretrage')

@section('content')
<section class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="space-y-6">
        <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Discovery</p>
                <h1 class="mt-2 text-3xl font-semibold text-slate-900">Moje spremljene pretrage</h1>
                <p class="mt-2 text-slate-600">Sačuvaj interesovanja i dobijaj signal kada se pojavi relevantna aukcija.</p>
            </div>
            <x-button variant="ghost" :href="route('search')">Nova pretraga</x-button>
        </div>

        @if (session('status'))
            <x-alert variant="success">{{ session('status') }}</x-alert>
        @endif

        <x-card class="space-y-4">
            <div class="space-y-3">
                @forelse ($searches as $search)
                    <div class="flex flex-col gap-4 rounded-3xl border border-slate-200 bg-white px-5 py-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="font-semibold text-slate-900">{{ $search->query }}</h2>
                                <x-badge :variant="$search->alert_enabled ? 'trust' : 'info'">
                                    {{ $search->alert_enabled ? 'Alert uključen' : 'Alert isključen' }}
                                </x-badge>
                            </div>
                            <p class="mt-1 text-sm text-slate-500">
                                {{ $search->category_slug ? 'Kategorija: '.$search->category_slug.' · ' : '' }}
                                spremljeno {{ $search->created_at?->diffForHumans() }}
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <a href="{{ route('search', ['q' => $search->query, 'category' => $search->category_slug]) }}" class="link">Otvori pretragu</a>
                            <form method="POST" action="{{ route('searches.destroy', ['search' => $search->id]) }}">
                                @csrf
                                @method('DELETE')
                                <x-button variant="ghost">Ukloni</x-button>
                            </form>
                        </div>
                    </div>
                @empty
                    <x-alert variant="info">Još nema spremljenih pretraga. Pokreni pretragu i sačuvaj je za kasniji povratak.</x-alert>
                @endforelse
            </div>
        </x-card>
    </div>
</section>
@endsection
