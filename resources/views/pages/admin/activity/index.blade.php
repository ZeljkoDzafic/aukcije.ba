@extends('layouts.admin')

@section('title', 'Admin Aktivnost')
@section('admin_heading', 'Admin Aktivnost')

@section('content')
<section class="space-y-6">
    <x-card class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Audit trail</h1>
                <p class="mt-2 text-sm text-slate-600">Pregled osjetljivih moderatorskih i administrativnih akcija.</p>
            </div>
            <x-badge variant="trust">{{ $entries->count() }} zapisa</x-badge>
        </div>

        <div class="space-y-3">
            @forelse ($entries as $entry)
                <div class="rounded-2xl border border-slate-200 bg-white px-4 py-4">
                    <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="font-semibold uppercase tracking-[0.12em] text-slate-900">{{ str_replace('-', ' ', $entry->action) }}</p>
                            <p class="text-sm text-slate-600">
                                {{ $entry->admin?->name ?? 'Sistem' }} · {{ $entry->target_type ?? 'sistem' }} · {{ $entry->target_id ? substr((string) $entry->target_id, 0, 8) : 'n/a' }}
                            </p>
                        </div>
                        <p class="text-sm text-slate-500">{{ $entry->created_at?->format('d.m.Y. H:i') }}</p>
                    </div>

                    @if (! empty($entry->metadata))
                        <div class="mt-3 rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-700">
                            @foreach ($entry->metadata as $key => $value)
                                <p><span class="font-medium">{{ str_replace('_', ' ', (string) $key) }}:</span> {{ is_scalar($value) ? $value : json_encode($value) }}</p>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <x-alert variant="info">Audit log je trenutno prazan.</x-alert>
            @endforelse
        </div>
    </x-card>
</section>
@endsection
