@extends('layouts.admin')

@section('title', 'Uredi stranicu')
@section('admin_heading', 'Uredi stranicu')

@section('content')
<section class="space-y-6">
    <x-card class="space-y-6">
        <form method="POST" action="{{ route('admin.content.pages.store') }}" class="space-y-5">
            @csrf
            <div class="grid gap-4 md:grid-cols-2">
                <x-input name="title" label="Naslov" :value="old('title', $page->title)" />
                <x-input name="slug" label="Slug" :value="old('slug', $page->slug)" />
                <x-select name="page_type" label="Tip stranice" :options="['legal' => 'Pravna', 'help' => 'Pomoć', 'company' => 'Kompanija']" :value="old('page_type', $page->page_type)" />
                <label class="mt-8 inline-flex items-center gap-3 text-sm text-slate-700">
                    <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $page->is_published))>
                    <span>Objavi odmah</span>
                </label>
            </div>

            <x-input name="excerpt" label="Sažetak" :value="old('excerpt', $page->excerpt)" />
            <x-wysiwyg-editor
                name="body"
                label="Sadržaj"
                :value="old('body', $page->body)"
                hint="Podržani su naslovi, bold, italic, liste, citati i linkovi."
            />

            <div class="flex gap-3">
                <x-button type="submit">Sačuvaj stranicu</x-button>
                <x-button variant="ghost" :href="route('admin.content.pages.index')">Nazad</x-button>
            </div>
        </form>
    </x-card>
</section>
@endsection
