@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('admin_heading', 'Admin Dashboard')

@section('content')
<section class="space-y-8">
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach ([['Ukupno korisnika', '18.540'], ['Aktivne aukcije', '1.240'], ['Današnjih bidova', '4.820'], ['Prihod ovog mjeseca', '32.400 BAM']] as [$label, $value])
            <x-card>
                <p class="text-sm text-slate-500">{{ $label }}</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $value }}</p>
            </x-card>
        @endforeach
    </div>

    <div class="grid gap-8 xl:grid-cols-[1fr_0.8fr]">
        <x-card class="space-y-4">
            <h2 class="text-2xl font-semibold text-slate-900">Operativni prioriteti</h2>
            <div class="grid gap-3">
                <x-alert variant="warning">12 KYC zahtjeva čeka pregled.</x-alert>
                <x-alert variant="danger">3 spora zahtijevaju odluku moderatora u narednih 24h.</x-alert>
                <x-alert variant="info">8 aukcija je auto-flagged zbog sumnjivog opisa ili cijene.</x-alert>
            </div>
        </x-card>

        <x-card class="space-y-4">
            <h2 class="text-2xl font-semibold text-slate-900">Nedavna aktivnost</h2>
            <div class="space-y-3 text-sm text-slate-700">
                <div class="rounded-2xl bg-slate-50 px-4 py-3">Moderator odobrio KYC za seller račun Haris B.</div>
                <div class="rounded-2xl bg-slate-50 px-4 py-3">Aukcija #2041 je označena kao featured.</div>
                <div class="rounded-2xl bg-slate-50 px-4 py-3">Otvoren novi dispute za order #A-881.</div>
            </div>
        </x-card>
    </div>
</section>
@endsection
