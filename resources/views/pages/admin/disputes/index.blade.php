@extends('layouts.admin')

@section('title', 'Sporovi')
@section('admin_heading', 'Rješavanje sporova')

@section('content')
<section class="space-y-6">
    <x-card class="space-y-4">
        <div class="flex items-center justify-between rounded-2xl bg-red-50 px-4 py-4">
            <div>
                <p class="font-semibold text-slate-900">Dispute #D-91 · item_not_as_described</p>
                <p class="text-sm text-slate-600">Order #A-881 · Kupac traži partial refund</p>
            </div>
            <x-button variant="secondary" :href="route('admin.disputes.show', ['dispute' => 91])">Pregled</x-button>
        </div>
        <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-4">
            <div>
                <p class="font-semibold text-slate-900">Dispute #D-90 · item_not_received</p>
                <p class="text-sm text-slate-600">Čeka odgovor prodavca</p>
            </div>
            <x-badge variant="warning">Na čekanju</x-badge>
        </div>
    </x-card>
</section>
@endsection
