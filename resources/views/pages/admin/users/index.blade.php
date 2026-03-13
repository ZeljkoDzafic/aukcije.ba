@extends('layouts.admin')

@section('title', 'Korisnici')
@section('admin_heading', 'Upravljanje korisnicima')

@section('content')
<section class="space-y-6">
    @if (($adminUserInbox ?? collect())->isNotEmpty())
        <div class="grid gap-3 md:grid-cols-3">
            @foreach ($adminUserInbox as $item)
                <div class="rounded-3xl border border-slate-200 bg-white px-5 py-4">
                    <p class="text-sm text-slate-500">{{ $item['label'] }}</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $item['value'] }}</p>
                    <p class="mt-2 text-sm text-slate-600">{{ $item['hint'] }}</p>
                </div>
            @endforeach
        </div>
    @endif

    @livewire('admin.user-directory')
</section>
@endsection
