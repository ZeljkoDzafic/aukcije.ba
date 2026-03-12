@extends('layouts.guest')

@section('title', 'Kategorije')
@section('meta')
    <x-seo-meta
        title="Kategorije aukcija - Aukcije.ba"
        description="Pregledaj aukcije po kategorijama kao što su elektronika, satovi, kolekcionarstvo, auto oprema i druge."
    />
@endsection

@section('content')
<section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
    <div class="space-y-8">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Kategorije</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-900">Pregledaj aukcije po kategoriji</h1>
        </div>
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($categories as $category)
                <x-card class="space-y-4">
                    <div class="space-y-2">
                        <a href="{{ route('categories.show', ['category' => $category['slug']]) }}" class="inline-flex items-start gap-3 text-slate-900 transition hover:text-trust-700">
                            <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-sm font-semibold uppercase text-slate-600">
                                {{ str($category['name'])->substr(0, 2)->upper() }}
                            </span>
                            <span>
                                <span class="block text-xl font-semibold">{{ $category['name'] }}</span>
                                <span class="mt-1 block text-sm text-slate-500">{{ $category['auctions_count'] }} aktivnih aukcija</span>
                            </span>
                        </a>
                        <p class="text-sm text-slate-600">Klikni na glavnu kategoriju ili podkategoriju za pregled aukcija, featured artikala i filtera.</p>
                    </div>

                    @if (! empty($category['children']))
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="mb-3 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Podkategorije</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($category['children'] as $child)
                                    <a href="{{ route('categories.show', ['category' => $child['slug']]) }}" class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 transition hover:border-trust-300 hover:text-trust-700">
                                        {{ $child['name'] }}
                                        <span class="ml-2 text-xs text-slate-400">{{ $child['auctions_count'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </x-card>
            @endforeach
        </div>
    </div>
</section>
@endsection
