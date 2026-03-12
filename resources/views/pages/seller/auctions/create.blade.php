@extends('layouts.seller')

@section('title', 'Kreiraj aukciju')
@section('seller_heading', 'Kreiraj novu aukciju')

@section('content')
<section class="space-y-8">
    @livewire('seller.create-auction-wizard', ['auctionId' => request()->route('auction')])
</section>
@endsection
