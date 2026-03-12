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
            @foreach (['Elektronika', 'Satovi', 'Kolekcionarstvo', 'Auto oprema', 'Moda', 'Dom'] as $category)
                <x-card class="space-y-3">
                    <h2 class="text-xl font-semibold text-slate-900">{{ $category }}</h2>
                    <p class="text-sm text-slate-600">Aktivne aukcije, seller rating signali i popularni filteri za ovu kategoriju.</p>
                </x-card>
            @endforeach
        </div>
    </div>
</section>
@endsection
