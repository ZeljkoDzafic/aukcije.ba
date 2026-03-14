@extends('layouts.admin')

@section('title', 'Uredi vijest')
@section('admin_heading', 'Uredi vijest')

@section('content')
<section class="space-y-6">
    <x-card class="space-y-6">
        <form method="POST" action="{{ route('admin.content.news.store') }}" class="space-y-5">
            @csrf
            <div class="grid gap-4 md:grid-cols-2">
                <x-input name="title" label="Naslov" :value="old('title', $article->title)" />
                <x-input name="slug" label="Slug" :value="old('slug', $article->slug)" />
            </div>

            <label class="inline-flex items-center gap-3 text-sm text-slate-700">
                <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $article->is_published))>
                <span>Objavi odmah</span>
            </label>

            <x-input name="excerpt" label="Sažetak" :value="old('excerpt', $article->excerpt)" />
            <x-wysiwyg-editor
                name="body"
                label="Sadržaj vijesti"
                :value="old('body', $article->body)"
                hint="Koristi formatirani tekst za lead, podnaslove, liste i linkove ka objavama."
            />

            <div class="flex gap-3">
                <x-button type="submit">Sačuvaj vijest</x-button>
                <x-button variant="ghost" :href="route('admin.content.news.index')">Nazad</x-button>
            </div>
        </form>
    </x-card>
</section>
@endsection
