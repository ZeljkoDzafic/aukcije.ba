@extends('layouts.app')

@section('title', 'Praćene aukcije')

@section('content')
<section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    @livewire('watchlist')
</section>
@endsection
