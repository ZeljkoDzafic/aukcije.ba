@extends('layouts.admin')

@section('title', 'Sporovi')
@section('admin_heading', 'Rješavanje sporova')

@section('content')
<section class="space-y-6">
    @if (($disputeInbox ?? collect())->isNotEmpty())
        <div class="grid gap-3 md:grid-cols-3">
            @foreach ($disputeInbox as $item)
                <div class="rounded-3xl border border-slate-200 bg-white px-5 py-4">
                    <p class="text-sm text-slate-500">{{ $item['label'] }}</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $item['value'] }}</p>
                    <p class="mt-2 text-sm text-slate-600">{{ $item['hint'] }}</p>
                </div>
            @endforeach
        </div>
    @endif

    <x-card class="space-y-4">
        @foreach (($disputes ?? collect()) as $entry)
            <div class="flex flex-col gap-4 rounded-2xl {{ in_array($entry['status'], ['open', 'escalated', 'in_review'], true) ? 'bg-red-50' : 'bg-slate-50' }} px-4 py-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="font-semibold text-slate-900">Dispute #{{ $entry['id'] }} · {{ str_replace('_', ' ', $entry['reason']) }}</p>
                    <p class="text-sm text-slate-600">Order #{{ $entry['order'] }} · {{ $entry['summary'] }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <x-badge :variant="in_array($entry['status'], ['open', 'escalated', 'in_review'], true) ? 'danger' : 'info'">{{ str_replace('_', ' ', $entry['status']) }}</x-badge>
                    <x-button variant="secondary" :href="$entry['href']">{{ $entry['cta'] }}</x-button>
                </div>
            </div>
        @endforeach
    </x-card>
</section>
@endsection
