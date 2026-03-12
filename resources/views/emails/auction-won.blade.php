@extends('emails.layouts.base')

@section('title', 'Čestitamo - Dobili ste aukciju')

@section('content')
    <h1 class="greeting">🎉 Čestitamo {{ $user->name }}!</h1>
    
    <div class="content">
        <p>Pobijedili ste na aukciji!</p>
    </div>
    
    <div class="info-box">
        <div class="info-row">
            <span class="info-label">Aukcija:</span>
            <span class="info-value">{{ $auction->title }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Finalna cijena:</span>
            <span class="info-value">{{ number_format($finalPrice, 2) }} KM</span>
        </div>
        <div class="info-row">
            <span class="info-label">Prodavac:</span>
            <span class="info-value">{{ $auction->seller->name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Status:</span>
            <span class="badge badge-success">Čeka plaćanje</span>
        </div>
    </div>
    
    <div class="alert alert-success">
        <strong>Sljedeći koraci:</strong><br>
        1. Otiđite na "Moje narudžbe"<br>
        2. Izvršite plaćanje u roku od 3 dana<br>
        3. Nakon potvrde dostave, sredstva se oslobađaju prodavcu
    </div>
    
    <div style="text-align: center;">
        <a href="{{ route('orders.pay', $order) }}" class="btn">Plati odmah</a>
    </div>
    
    <div class="content">
        <p>Još jednom čestitamo na pobjedi!</p>
    </div>
@endsection
