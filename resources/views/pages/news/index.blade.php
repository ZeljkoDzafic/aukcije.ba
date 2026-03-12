@extends('layouts.guest')

@section('title', 'Vijesti')
@section('meta')
    <x-seo-meta title="Vijesti - Aukcije.ba" description="Službene obavijesti, sigurnosne preporuke i novosti platforme Aukcije.ba." />
@endsection

@section('content')
<section class="mx-auto max-w-6xl px-4 py-14 sm:px-6 lg:px-8">
    <div class="space-y-8">
        <div class="space-y-3">
            <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Vijesti i obavijesti</p>
            <h1 class="text-4xl font-semibold text-slate-950">Novosti platforme</h1>
            <p class="max-w-3xl text-lg text-slate-600">Promjene pravila, sigurnosne preporuke, razvoj platforme i važne operativne obavijesti.</p>
        </div>

        <div class="grid gap-6">
            @foreach ($articles as $article)
                <x-card class="space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h2 class="text-2xl font-semibold text-slate-900">{{ $article->title }}</h2>
                        <span class="text-sm text-slate-500">{{ optional($article->published_at)->format('d.m.Y.') ?? 'Objavljeno' }}</span>
                    </div>
                    <p class="text-slate-600">{{ $article->excerpt }}</p>
                    <a href="{{ route('news.show', ['article' => $article->slug]) }}" class="link">Otvori vijest</a>
                </x-card>
            @endforeach
        </div>
    </div>
</section>
@endsection
