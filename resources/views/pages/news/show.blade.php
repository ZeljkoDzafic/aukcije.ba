@extends('layouts.guest')

@section('title', $article->title.' - Vijesti')
@section('meta')
    <x-seo-meta :title="$article->title.' - Aukcije.ba'" :description="$article->excerpt ?: $article->title" />
@endsection

@section('content')
<section class="mx-auto max-w-4xl px-4 py-14 sm:px-6 lg:px-8">
    <article class="space-y-8">
        <div class="space-y-3">
            <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Vijesti</p>
            <h1 class="text-4xl font-semibold text-slate-950">{{ $article->title }}</h1>
            <p class="text-sm text-slate-500">{{ optional($article->published_at)->format('d.m.Y. H:i') ?? 'Objavljeno' }}</p>
            @if (! empty($article->excerpt))
                <p class="max-w-3xl text-lg text-slate-600">{{ $article->excerpt }}</p>
            @endif
        </div>

        <x-card class="prose prose-slate max-w-none prose-headings:text-slate-900 prose-p:text-slate-700 prose-strong:text-slate-900">
            {!! $article->body !!}
        </x-card>
    </article>
</section>
@endsection
