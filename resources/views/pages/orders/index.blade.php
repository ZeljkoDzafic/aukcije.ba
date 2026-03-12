@extends('layouts.app')

@section('title', 'Narudžbe')

@section('content')
<section class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
    <x-card class="space-y-4">
        <h1 class="text-2xl font-semibold text-slate-900">Moje narudžbe</h1>
        <div class="space-y-3">
            <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                <span>Order #A-2041 · Fujifilm X-T5</span>
                <x-badge variant="warning">Čeka uplatu</x-badge>
            </div>
            <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                <span>Order #A-2011 · Vinyl kolekcija</span>
                <x-badge variant="success">Poslano</x-badge>
            </div>
        </div>
    </x-card>
</section>
@endsection
