@extends('layouts.guest')

@section('title', 'Pretraga')
@section('meta')
    <x-seo-meta
        title="Pretraga aukcija - Aukcije.ba"
        description="Pronađi aktivne aukcije po ključnoj riječi, kategoriji i lokaciji."
    />
@endsection

@section('content')
<section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
    <div class="grid gap-8 xl:grid-cols-[320px_1fr]">
        <x-card class="space-y-5">
            <div class="space-y-2">
                <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Pretraga</p>
                <h1 class="text-3xl font-semibold text-slate-900">Pronađi aukciju</h1>
                <p class="text-sm text-slate-600">Pretražuj po nazivu, opisu ili lokaciji i brzo otvori rezultat.</p>
            </div>

            <form method="GET" action="{{ route('search') }}" class="space-y-4">
                <x-input name="q" label="Upit" :value="$query" placeholder="Vintage sat, iPhone, fotoaparat..." />

                <label class="block space-y-2 text-sm font-medium text-slate-700">
                    <span>Kategorija</span>
                    <select name="category" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-trust-400 focus:outline-none focus:ring-2 focus:ring-trust-100">
                        <option value="">Sve kategorije</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->slug }}" @selected($selectedCategory === $category->slug)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </label>

                <x-button type="submit" class="w-full">Pokreni pretragu</x-button>
            </form>

            @auth
                @if ($query !== '')
                    <form method="POST" action="{{ route('searches.store') }}" class="rounded-2xl border border-trust-200 bg-trust-50 p-4">
                        @csrf
                        <input type="hidden" name="query" value="{{ $query }}">
                        <input type="hidden" name="category_slug" value="{{ $selectedCategory }}">
                        <p class="text-sm font-medium text-trust-900">Sačuvaj ovu pretragu</p>
                        <p class="mt-1 text-sm text-trust-700">Uključi alert i brzo se vrati na interesantne upite.</p>
                        <x-button type="submit" variant="ghost" class="mt-3 w-full">Spremi pretragu</x-button>
                    </form>
                @endif
            @endauth

            @if ($query !== '')
                <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">
                    {{ $results->count() }} rezultata za upit <span class="font-semibold text-slate-900">"{{ $query }}"</span>.
                </div>
            @endif
        </x-card>

        <div class="space-y-6">
            @if ($query === '')
                <x-card class="space-y-3">
                    <h2 class="text-xl font-semibold text-slate-900">Kako koristiti pretragu</h2>
                    <p class="text-slate-600">Upiši ključnu riječ, suzi rezultat kategorijom i otvori artikl direktno iz rezultata.</p>
                    <div class="flex flex-wrap gap-2 text-sm text-slate-600">
                        @foreach (['Rolex', 'PlayStation', 'Canon', 'Bosanski ćilim'] as $hint)
                            <a href="{{ route('search', ['q' => $hint]) }}" class="rounded-full bg-slate-100 px-3 py-2 transition hover:bg-slate-200">{{ $hint }}</a>
                        @endforeach
                    </div>
                </x-card>
            @elseif ($results->isEmpty())
                <x-alert variant="info">Nema aktivnih aukcija za zadani upit. Pokušaj sa širom ključnom riječi ili drugom kategorijom.</x-alert>
            @else
                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($results as $auction)
                        <x-auction-card
                            :title="$auction['title']"
                            :category="$auction['category']"
                            :price="$auction['price']"
                            :bids="$auction['bids']"
                            :watchers="$auction['watchers']"
                            :location="$auction['location']"
                            :time="$auction['time']"
                            :image-url="$auction['image_url']"
                            :badge="$auction['badge']"
                            :href="route('auctions.show', ['auction' => $auction['id']])"
                        />
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
