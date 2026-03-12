@extends('layouts.seller')

@section('title', 'Moje aukcije')
@section('seller_heading', 'Moje aukcije')

@section('content')
<section class="space-y-6">
    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        <x-auction-card title="Tissot PRX Powermatic" category="Satovi" price="1220.00" bids="7" watchers="18" location="Tuzla" time="2d 05h" />
        <x-auction-card title="Fender Telecaster Player" category="Muzička oprema" price="1780.00" bids="13" watchers="21" location="Sarajevo" time="1d 04h" />
        <x-auction-card title="DJI Mini 4 Pro" category="Dronovi" price="1440.00" bids="9" watchers="15" location="Zenica" time="15h 22m" />
    </div>
</section>
@endsection
