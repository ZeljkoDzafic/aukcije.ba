@extends('layouts.admin')

@section('title', 'Detalj korisnika')
@section('admin_heading', 'Detalj korisnika')

@section('content')
@php
    $userId = request()->route('user');
@endphp
<section class="space-y-8">
    <div class="grid gap-8 xl:grid-cols-[0.9fr_1.1fr]">
        <x-card class="space-y-5">
            <div class="flex items-center gap-4">
                <x-avatar name="Amar Hadžić" size="lg" />
                <div>
                    <h1 class="text-2xl font-semibold text-slate-900">Amar Hadžić</h1>
                    <p class="text-slate-600">seller · KYC nivo 3 · Premium tier</p>
                </div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div><p class="text-sm text-slate-500">Email</p><p class="mt-1 font-medium text-slate-900">amar@example.test</p></div>
                <div><p class="text-sm text-slate-500">Telefon</p><p class="mt-1 font-medium text-slate-900">+387 61 222 333</p></div>
                <div><p class="text-sm text-slate-500">Status</p><p class="mt-1 font-medium text-emerald-700">Aktivan</p></div>
                <div><p class="text-sm text-slate-500">Trust score</p><p class="mt-1 font-medium text-slate-900">4.9 / 5</p></div>
            </div>
        </x-card>

        <div class="space-y-6">
            <x-card class="space-y-4">
                <h2 class="text-xl font-semibold text-slate-900">Aktivnost</h2>
                @livewire('admin.user-profile-manager', ['userId' => $userId])
            </x-card>

            <x-card class="space-y-4">
                <h2 class="text-xl font-semibold text-slate-900">Admin log</h2>
                <div class="space-y-3">
                    <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-700">Moderator odobrio KYC dokumente.</div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-700">Seller plan podignut na Premium.</div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-700">Nema aktivnih sigurnosnih incidenata.</div>
                </div>
            </x-card>
        </div>
    </div>
</section>
@endsection
