@extends('layouts.admin')

@section('title', 'Moderacija aukcija')
@section('admin_heading', 'Moderacija aukcija')

@section('content')
<section class="space-y-6">
    @if (($moderationInbox ?? collect())->isNotEmpty())
        <div class="panel-hero-muted">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="panel-kicker">Moderation queue</p>
                    <h2 class="mt-2 text-3xl font-semibold text-slate-900">Aukcije pod provjerom</h2>
                    <p class="mt-2 max-w-2xl text-slate-600">Reported, pending review i featured listing nadzor u jednom operativnom ulazu.</p>
                </div>
                <x-button variant="ghost" :href="route('admin.dashboard')">Nazad na dashboard</x-button>
            </div>
        </div>

        <div class="grid gap-3 md:grid-cols-3">
            @foreach ($moderationInbox as $item)
                <div class="panel-shell-soft market-sheen">
                    <p class="text-sm text-slate-500">{{ $item['label'] }}</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $item['value'] }}</p>
                    <p class="mt-2 text-sm text-slate-600">{{ $item['hint'] }}</p>
                </div>
            @endforeach
        </div>
    @endif

    @livewire('admin.auction-moderation')
</section>
@endsection
