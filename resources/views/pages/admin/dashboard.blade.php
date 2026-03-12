@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('admin_heading', 'Admin Dashboard')

@section('content')
<section class="space-y-8">
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach ($stats as $stat)
            <x-card>
                <p class="text-sm text-slate-500">{{ $stat['label'] }}</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $stat['value'] }}</p>
            </x-card>
        @endforeach
    </div>

    <div class="grid gap-8 xl:grid-cols-[1fr_0.8fr]">
        <x-card class="space-y-4">
            <h2 class="text-2xl font-semibold text-slate-900">Operativni prioriteti</h2>
            <div class="grid gap-3">
                @foreach ($priorities as $item)
                    <x-alert :variant="$item['variant']">{{ $item['text'] }}</x-alert>
                @endforeach
            </div>
        </x-card>

        <x-card class="space-y-4">
            <h2 class="text-2xl font-semibold text-slate-900">Nedavna aktivnost</h2>
            <div class="space-y-3 text-sm text-slate-700">
                @foreach ($activity as $entry)
                    <div class="rounded-2xl bg-slate-50 px-4 py-3">{{ $entry }}</div>
                @endforeach
            </div>
        </x-card>
    </div>
</section>
@endsection
