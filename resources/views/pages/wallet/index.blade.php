@extends('layouts.app')

@section('title', 'Wallet')
@section('meta')
    <x-seo-meta
        title="Wallet - Aukcije.ba"
        description="Pregled raspoloživog balansa, escrow sredstava, uplata, isplata i historije wallet transakcija."
    />
@endsection

@section('content')
<section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    @livewire('wallet-manager')
</section>
@endsection
