@extends('layouts.admin')

@section('title', 'Vijesti')
@section('admin_heading', 'Vijesti')

@section('content')
<section class="space-y-6">
    @if (session('status'))
        <x-alert variant="success">{{ session('status') }}</x-alert>
    @endif

    <div class="flex items-center justify-between gap-4">
        <p class="text-slate-600">Objave za javni feed vijesti, obavijesti i produktne promjene.</p>
        <x-button :href="route('admin.content.news.edit')">Nova vijest</x-button>
    </div>

    <x-card>
        <x-data-table :headers="['Naslov', 'Slug', 'Status', 'Akcije']">
            @foreach ($articles as $article)
                <tr class="table-row">
                    <td class="px-4 py-3 font-medium text-slate-900">{{ $article->title ?? $article['title'] }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $article->slug ?? $article['slug'] }}</td>
                    <td class="px-4 py-3">
                        <x-badge :variant="($article->is_published ?? $article['is_published']) ? 'success' : 'warning'">{{ ($article->is_published ?? $article['is_published']) ? 'Objavljeno' : 'Skica' }}</x-badge>
                    </td>
                    <td class="px-4 py-3">
                        <x-button variant="secondary" :href="route('admin.content.news.edit', ['slug' => $article->slug ?? $article['slug']])">Uredi</x-button>
                    </td>
                </tr>
            @endforeach
        </x-data-table>
    </x-card>
</section>
@endsection
