@extends('layouts.admin')

@section('title', 'Moderacija aukcija')
@section('admin_heading', 'Moderacija aukcija')

@section('content')
<section class="space-y-6">
    @if (($moderationInbox ?? collect())->isNotEmpty())
        <div class="grid gap-3 md:grid-cols-3">
            @foreach ($moderationInbox as $item)
                <div class="rounded-3xl border border-slate-200 bg-white px-5 py-4">
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
