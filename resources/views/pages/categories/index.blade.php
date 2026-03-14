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
        <div class="market-sheen rounded-[2rem] border border-slate-200 bg-[radial-gradient(circle_at_top_left,_rgba(14,165,233,0.14),_transparent_28%),radial-gradient(circle_at_bottom_right,_rgba(245,158,11,0.15),_transparent_26%),linear-gradient(180deg,#ffffff_0%,#f8fafc_100%)] p-6 shadow-sm sm:p-8">
            <div class="grid gap-8 xl:grid-cols-[1fr_0.95fr] xl:items-end">
                <div>
                    <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Kategorije</p>
                    <h1 class="mt-2 text-4xl font-semibold text-slate-900">Pregledaj aukcije po kategoriji</h1>
                    <p class="mt-3 max-w-3xl text-slate-600">Kategorije su sada marketplace navigation layer: odavde korisnik ulazi u segmente sa najviše aktivnosti, seller interesa i aukcija koje imaju stvarnu likvidnost.</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="rounded-[1.5rem] border border-white/70 bg-white/80 px-4 py-4 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Kategorija</p>
                        <p class="mt-2 text-3xl font-semibold text-slate-950">{{ count($categories) }}</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-white/70 bg-white/80 px-4 py-4 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Discovery</p>
                        <p class="mt-2 text-lg font-semibold text-trust-700">Buyer-first</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-white/70 bg-white/80 px-4 py-4 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Seller fit</p>
                        <p class="mt-2 text-lg font-semibold text-emerald-700">Više niša</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($categories as $category)
                <div class="market-sheen rounded-[1.75rem] border border-slate-200 bg-[linear-gradient(180deg,#ffffff_0%,#f8fafc_100%)] p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <div class="space-y-4">
                        <a href="{{ route('categories.show', ['category' => $category['slug']]) }}" class="inline-flex items-start gap-4 text-slate-900 transition hover:text-trust-700">
                            <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-sky-100 to-amber-50 text-sm font-semibold uppercase text-slate-700">
                                {{ str($category['name'])->substr(0, 2)->upper() }}
                            </span>
                            <span>
                                <span class="block text-2xl font-semibold">{{ $category['name'] }}</span>
                                <span class="mt-1 block text-sm text-slate-500">{{ $category['auctions_count'] }} aktivnih aukcija</span>
                            </span>
                        </a>
                        <p class="text-sm leading-7 text-slate-600">Klikni na glavnu kategoriju ili podkategoriju za pregled aukcija, seller fokusa i tržišne aktivnosti u tom segmentu.</p>
                    </div>

                    @if (! empty($category['children']))
                        <div class="mt-5 rounded-2xl bg-slate-50 p-4">
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
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
