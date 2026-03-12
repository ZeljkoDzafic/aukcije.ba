@extends('layouts.admin')

@section('title', 'Statične stranice')
@section('admin_heading', 'Statične stranice')

@section('content')
<section class="space-y-6">
    @if (session('status'))
        <x-alert variant="success">{{ session('status') }}</x-alert>
    @endif

    <div class="flex items-center justify-between gap-4">
        <p class="text-slate-600">Upravljanje pravnim, informativnim i pomoćnim stranicama dostupnim javnosti.</p>
        <x-button :href="route('admin.content.pages.edit')">Nova stranica</x-button>
    </div>

    <x-card>
        <x-data-table :headers="['Naslov', 'Slug', 'Tip', 'Status', 'Akcije']">
            @foreach ($pages as $page)
                <tr class="table-row">
                    <td class="px-4 py-3 font-medium text-slate-900">{{ $page->title ?? $page['title'] }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $page->slug ?? $page['slug'] }}</td>
                    <td class="px-4 py-3">{{ $page->page_type ?? $page['page_type'] }}</td>
                    <td class="px-4 py-3">
                        <x-badge :variant="($page->is_published ?? $page['is_published']) ? 'success' : 'warning'">{{ ($page->is_published ?? $page['is_published']) ? 'Objavljeno' : 'Skica' }}</x-badge>
                    </td>
                    <td class="px-4 py-3">
                        <x-button variant="secondary" :href="route('admin.content.pages.edit', ['slug' => $page->slug ?? $page['slug']])">Uredi</x-button>
                    </td>
                </tr>
            @endforeach
        </x-data-table>
    </x-card>
</section>
@endsection
