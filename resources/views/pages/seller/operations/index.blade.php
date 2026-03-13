@extends('layouts.seller')

@section('title', 'Bulk operacije')
@section('seller_heading', 'Bulk operacije')

@section('content')
<section class="space-y-6">
    <div>
        <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Seller tools</p>
        <h1 class="mt-2 text-3xl font-semibold text-slate-900">Rad nad više aukcija odjednom</h1>
        <p class="mt-2 text-slate-600">Označi draft ili aktivne aukcije i pokreni objavu, završavanje ili kloniranje u jednom koraku.</p>
    </div>

    @livewire('seller.bulk-operations')
</section>
@endsection
