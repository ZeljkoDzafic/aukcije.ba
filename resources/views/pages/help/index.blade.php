@extends('layouts.guest')

@section('title', 'Pomoć')
@section('meta')
    <x-seo-meta title="Pomoć - Aukcije.ba" description="Centar pomoći za kupovinu, prodaju, sigurnost naloga i podršku platforme Aukcije.ba." />
@endsection

@section('content')
<section class="mx-auto max-w-6xl px-4 py-14 sm:px-6 lg:px-8">
    <div class="space-y-8">
        <div class="space-y-3">
            <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Centar pomoći</p>
            <h1 class="text-4xl font-semibold text-slate-950">Pomoć za kupce i prodavače</h1>
            <p class="max-w-3xl text-lg text-slate-600">Brzi pristup pravilima kupovine i prodaje, sigurnosnim preporukama, ocjenama korisnika i kontaktu sa podrškom.</p>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            @foreach ($sections as $section => $titles)
                <x-card class="space-y-4">
                    <h2 class="text-xl font-semibold text-slate-900">{{ $section }}</h2>
                    <div class="space-y-3">
                        @foreach ($helpPages->filter(fn ($page) => in_array($page->title, $titles, true)) as $page)
                            <a href="{{ route('content.show', ['slug' => $page->slug]) }}" class="block rounded-2xl bg-slate-50 px-4 py-3 transition hover:bg-slate-100">
                                <p class="font-medium text-slate-900">{{ $page->title }}</p>
                                <p class="mt-1 text-sm text-slate-600">{{ $page->excerpt }}</p>
                            </a>
                        @endforeach
                    </div>
                </x-card>
            @endforeach
        </div>
    </div>
</section>
@endsection
