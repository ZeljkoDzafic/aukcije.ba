@extends('layouts.seller')

@section('title', 'Template-i aukcija')
@section('seller_heading', 'Template-i aukcija')

@section('content')
<section class="space-y-6">
    <div>
        <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Seller tools</p>
        <h1 class="mt-2 text-3xl font-semibold text-slate-900">Template-i za brže objave</h1>
        <p class="mt-2 text-slate-600">Sačuvaj uspješne aukcije kao predložak i kreiraj novu draft aukciju jednim klikom.</p>
    </div>

    @livewire('seller.templates-manager')
</section>
@endsection
