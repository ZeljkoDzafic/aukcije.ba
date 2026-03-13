@extends('layouts.admin')

@section('title', 'Detalj korisnika')
@section('admin_heading', 'Detalj korisnika')

@section('content')
@php
    $userId = request()->route('user');
    $userRecord = $userRecord ?? null;
    $decisionHistory = $decisionHistory ?? collect();
    $kycQueue = $kycQueue ?? collect();
    $userName = $userRecord?->name ?? 'Amar Hadžić';
    $roleSummary = $userRecord && method_exists($userRecord, 'roleSummary') ? $userRecord->roleSummary() : 'seller';
    $kycLevel = $userRecord?->kycLevel() ?? 3;
    $tierLabel = $userRecord?->getTier()['name'] ?? 'Premium';
    $email = $userRecord?->email ?? 'amar@example.test';
    $phone = $userRecord?->phone ?? '+387 61 222 333';
    $statusLabel = ($userRecord?->is_banned ?? false) ? 'Suspendovan' : 'Aktivan';
    $statusClass = ($userRecord?->is_banned ?? false) ? 'text-red-700' : 'text-emerald-700';
    $trustScore = number_format((float) ($userRecord?->trust_score ?? 4.9), 1);
@endphp
<section class="space-y-8">
    <div class="grid gap-8 xl:grid-cols-[0.9fr_1.1fr]">
        <x-card class="space-y-5">
            <div class="flex items-center gap-4">
                <x-avatar :name="$userName" size="lg" />
                <div>
                    <h1 class="text-2xl font-semibold text-slate-900">{{ $userName }}</h1>
                    <p class="text-slate-600">{{ $roleSummary }} · KYC nivo {{ $kycLevel }} · {{ ucfirst((string) $tierLabel) }} tier</p>
                </div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div><p class="text-sm text-slate-500">Email</p><p class="mt-1 font-medium text-slate-900">{{ $email }}</p></div>
                <div><p class="text-sm text-slate-500">Telefon</p><p class="mt-1 font-medium text-slate-900">{{ $phone }}</p></div>
                <div><p class="text-sm text-slate-500">Status</p><p class="mt-1 font-medium {{ $statusClass }}">{{ $statusLabel }}</p></div>
                <div><p class="text-sm text-slate-500">Trust score</p><p class="mt-1 font-medium text-slate-900">{{ $trustScore }} / 5</p></div>
            </div>
        </x-card>

        <div class="space-y-6">
            <x-card class="space-y-4">
                <h2 class="text-xl font-semibold text-slate-900">Aktivnost</h2>
                @livewire('admin.user-profile-manager', ['userId' => $userId])
            </x-card>

            <x-card class="space-y-4">
                <h2 class="text-xl font-semibold text-slate-900">KYC queue</h2>
                <div class="space-y-3">
                    @forelse ($kycQueue as $entry)
                        <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-700">
                            <span class="font-semibold text-slate-900">{{ strtoupper((string) $entry->type) }}</span>
                            · {{ $entry->status }}
                            @if ($entry->notes)
                                <div class="mt-2 text-slate-600">{{ $entry->notes }}</div>
                            @endif
                        </div>
                    @empty
                        <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">Nema aktivnih KYC zahtjeva za ovog korisnika.</div>
                    @endforelse
                </div>
            </x-card>

            <x-card class="space-y-4">
                <h2 class="text-xl font-semibold text-slate-900">Decision history</h2>
                <div class="space-y-3">
                    @forelse ($decisionHistory as $entry)
                        <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-700">
                            <span class="font-semibold text-slate-900">{{ strtoupper($entry->action) }}</span>
                            · {{ $entry->admin?->name ?? 'Admin' }}
                            · {{ $entry->created_at?->format('d.m.Y. H:i') }}
                            @if (($entry->metadata['note'] ?? null) !== null)
                                <div class="mt-2 text-slate-600">Napomena: {{ $entry->metadata['note'] }}</div>
                            @endif
                        </div>
                    @empty
                        <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">Još nema zabilježenih admin odluka za ovog korisnika.</div>
                    @endforelse
                </div>
            </x-card>
        </div>
    </div>
</section>
@endsection
