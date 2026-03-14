@extends('layouts.guest')

@section('title', $page->title.' - Aukcije.ba')
@section('meta')
    <x-seo-meta :title="$page->title.' - Aukcije.ba'" :description="$page->excerpt ?: $page->title" />
@endsection

@section('content')
<section class="mx-auto max-w-4xl px-4 py-14 sm:px-6 lg:px-8">
    <article class="space-y-8">
        <div class="space-y-3">
            <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Informacije i pravila</p>
            <h1 class="text-4xl font-semibold text-slate-950">{{ $page->title }}</h1>
            @if (! empty($page->excerpt))
                <p class="max-w-3xl text-lg text-slate-600">{{ $page->excerpt }}</p>
            @endif
        </div>

        <x-card>
            <x-rich-text :html="$page->body" />
        </x-card>
    </article>
</section>
@endsection
