{{--
    Email: Outbid Notification
    Sent when user is outbid on an auction
--}}

@extends('emails.layouts.base')

@section('title', 'Nadjačan si na aukciji')

@section('content')
    <h1 class="greeting">Pozdrav {{ $user->name }}!</h1>
    
    <div class="content">
        <p>Nažalost, neko te je nadjačao na aukciji.</p>
    </div>
    
    <div class="info-box">
        <div class="info-row">
            <span class="info-label">Aukcija:</span>
            <span class="info-value">{{ $auction->title }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Tvoja ponuda:</span>
            <span class="info-value">{{ number_format($outbidBid->amount, 2) }} KM</span>
        </div>
        <div class="info-row">
            <span class="info-label">Nova vodeća ponuda:</span>
            <span class="info-value">{{ number_format($newLeadingBid->amount, 2) }} KM</span>
        </div>
        <div class="info-row">
            <span class="info-label">Preostalo vrijeme:</span>
            <span class="info-value">{{ $auction->time_remaining }}</span>
        </div>
    </div>
    
    <div class="alert alert-warning">
        <strong>⏰ Žuri!</strong> Aukcija završava uskoro. Postavi novu ponudu prije nego bude prekasno!
    </div>
    
    <div style="text-align: center;">
        <a href="{{ route('auctions.show', $auction) }}" class="btn">Ponovo licitiraj</a>
    </div>
    
    <div class="content">
        <p>Sretno!</p>
    </div>
@endsection
