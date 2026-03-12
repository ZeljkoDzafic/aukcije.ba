<?php

return [

    // Navigacija
    'nav' => [
        'home' => 'Početna',
        'auctions' => 'Aukcije',
        'categories' => 'Kategorije',
        'dashboard' => 'Dashboard',
        'watchlist' => 'Watchlist',
        'messages' => 'Poruke',
        'wallet' => 'Novčanik',
        'profile' => 'Profil',
        'settings' => 'Postavke',
        'logout' => 'Odjavi se',
        'login' => 'Prijavi se',
        'register' => 'Registruj se',
    ],

    // Aukcije
    'auctions' => [
        'title' => 'Aukcije',
        'active' => 'Aktivne aukcije',
        'ending_soon' => 'Ubrzo završava',
        'new' => 'Nove aukcije',
        'featured' => 'Izdvojene',
        'create' => 'Kreiraj aukciju',
        'edit' => 'Uredi aukciju',
        'view' => 'Pogledaj aukciju',
        'details' => 'Detalji aukcije',
        'description' => 'Opis',
        'category' => 'Kategorija',
        'condition' => 'Stanje',
        'start_price' => 'Početna cijena',
        'current_price' => 'Trenutna cijena',
        'buy_now' => 'Kupi odmah',
        'reserve_price' => 'Rezervna cijena',
        'duration' => 'Trajanje',
        'ends_at' => 'Završava',
        'time_remaining' => 'Preostalo vrijeme',
        'bids' => 'Ponude',
        'bid_count' => ':count ponuda',
        'watchers' => 'Pratilaca',
        'views' => 'Pregleda',
        'seller' => 'Prodavac',
        'winner' => 'Pobjednik',
        'status' => 'Status',
        'no_auctions' => 'Nema aukcija',
        'search_placeholder' => 'Pretraži aukcije...',
    ],

    // Licitanje
    'bidding' => [
        'place_bid' => 'Licitiraj',
        'your_bid' => 'Vaša ponuda',
        'minimum_bid' => 'Minimalna ponuda',
        'bid_amount' => 'Iznos ponude',
        'bid_history' => 'Historija ponuda',
        'highest_bid' => 'Najviša ponuda',
        'you_are_winning' => 'Vi vodite!',
        'you_are_outbid' => 'Nadjačani ste!',
        'bid_placed' => 'Ponuda uspješno postavljena',
        'bid_too_low' => 'Iznos ponude je prenizak',
        'cannot_bid_own' => 'Ne možete licitirati na svojoj aukciji',
        'auction_ended' => 'Ova aukcija je završena',
        'proxy_bid' => 'Proxy ponuda',
        'max_bid' => 'Maksimalna ponuda',
        'auto_bid' => 'Automatsko licitiranje do vašeg maksimuma',
    ],

    // Autentifikacija
    'auth' => [
        'login' => 'Prijavi se',
        'register' => 'Registruj se',
        'logout' => 'Odjavi se',
        'email' => 'Email',
        'password' => 'Lozinka',
        'password_confirm' => 'Potvrdi lozinku',
        'remember_me' => 'Zapamti me',
        'forgot_password' => 'Zaboravili ste lozinku?',
        'reset_password' => 'Resetuj lozinku',
        'send_reset_link' => 'Pošalji link za reset',
        'name' => 'Ime',
        'phone' => 'Telefon',
        'register_as' => 'Registruj se kao',
        'buyer' => 'Kupac',
        'seller' => 'Prodavac',
        'already_have_account' => 'Već imate nalog?',
        'dont_have_account' => 'Nemate nalog?',
        'verify_email' => 'Verifikuj email',
        'verification_sent' => 'Link za verifikaciju poslan',
        'login_success' => 'Uspješna prijava',
        'logout_success' => 'Uspješna odjava',
        'register_success' => 'Uspješna registracija',
    ],

    // Tipovi korisnika
    'user_types' => [
        'buyer' => 'Kupac',
        'seller' => 'Prodavac',
        'verified_seller' => 'Verifikovani prodavac',
        'admin' => 'Administrator',
        'moderator' => 'Moderator',
    ],

    // Dashboard
    'dashboard' => [
        'title' => 'Dashboard',
        'welcome' => 'Dobrodošli, :name!',
        'active_bids' => 'Aktivne ponude',
        'won_auctions' => 'Dobijene aukcije',
        'watchlist_count' => 'Stavki na watchlisti',
        'wallet_balance' => 'Stanje novčanika',
        'recent_activity' => 'Nedavne aktivnosti',
        'quick_links' => 'Brzi linkovi',
    ],

    // Novčanik
    'wallet' => [
        'title' => 'Novčanik',
        'balance' => 'Stanje',
        'available' => 'Dostupno',
        'in_escrow' => 'U escrow-u',
        'total' => 'Ukupno',
        'deposit' => 'Uplata',
        'withdraw' => 'Isplata',
        'transactions' => 'Transakcije',
        'transaction_history' => 'Historija transakcija',
        'amount' => 'Iznos',
        'type' => 'Tip',
        'date' => 'Datum',
        'status' => 'Status',
        'deposit_success' => 'Uplata uspješna',
        'withdraw_success' => 'Isplata uspješna',
        'insufficient_funds' => 'Nedovoljno sredstava',
    ],

    // Narudžbe
    'orders' => [
        'title' => 'Narudžbe',
        'order' => 'Narudžba',
        'my_orders' => 'Moje narudžbe',
        'order_number' => 'Narudžba br. :number',
        'total' => 'Ukupno',
        'status' => 'Status',
        'pending_payment' => 'Čeka plaćanje',
        'paid' => 'Plaćeno',
        'awaiting_shipment' => 'Čeka slanje',
        'shipped' => 'Poslano',
        'delivered' => 'Dostavljeno',
        'completed' => 'Završeno',
        'cancelled' => 'Otkazano',
        'disputed' => 'U sporu',
        'pay_now' => 'Plati odmah',
        'track_order' => 'Prati narudžbu',
        'confirm_delivery' => 'Potvrdi dostavu',
    ],

    // Dostava
    'shipping' => [
        'title' => 'Dostava',
        'method' => 'Metod dostave',
        'address' => 'Adresa dostave',
        'city' => 'Grad',
        'postal_code' => 'Poštanski broj',
        'country' => 'Država',
        'tracking' => 'Praćenje',
        'tracking_number' => 'Broj za praćenje',
        'courier' => 'Kurir',
        'estimated_delivery' => 'Procijenjena dostava',
        'shipped_by' => 'Poslao: :seller',
    ],

    // Kategorije
    'categories' => [
        'all' => 'Sve kategorije',
        'electronics' => 'Elektronika',
        'vehicles' => 'Vozila',
        'fashion' => 'Moda',
        'home_garden' => 'Dom i bašta',
        'sports' => 'Sport i napolju',
        'collectibles' => 'Kolekcionarstvo',
        'toys' => 'Igračke i hobiji',
        'other' => 'Ostalo',
    ],

    // Stanja
    'conditions' => [
        'new' => 'Novo',
        'used' => 'Korišteno',
        'refurbished' => 'Obnovljeno',
        'excellent' => 'Odlično',
        'good' => 'Dobro',
        'fair' => 'Zadovoljavajuće',
        'poor' => 'Loše',
    ],

    // Poruke
    'messages' => [
        'title' => 'Poruke',
        'send' => 'Pošalji',
        'reply' => 'Odgovori',
        'message' => 'Poruka',
        'from' => 'Od',
        'to' => 'Za',
        'subject' => 'Naslov',
        'no_messages' => 'Nema poruka',
        'write_message' => 'Napiši poruku',
    ],

    // Notifikacije
    'notifications' => [
        'title' => 'Notifikacije',
        'mark_read' => 'Označi kao pročitano',
        'mark_unread' => 'Označi kao nepročitano',
        'delete' => 'Obriši',
        'no_notifications' => 'Nema notifikacija',
        'outbid' => 'Nadjačani ste',
        'won' => 'Dobili ste aukciju',
        'payment_received' => 'Plaćanje primljeno',
        'item_shipped' => 'Artikal poslan',
    ],

    // Validacija
    'validation' => [
        'required' => 'Ovo polje je obavezno',
        'email' => 'Unesite ispravan email',
        'min' => 'Minimum :min karaktera',
        'max' => 'Maksimum :max karaktera',
        'numeric' => 'Unesite broj',
        'confirmed' => 'Potvrda se ne poklapa',
        'unique' => 'Ova vrijednost je već zauzeta',
        'accepted' => 'Mora biti prihvaćeno',
    ],

    // Greške
    'errors' => [
        'not_found' => 'Nije pronađeno',
        'unauthorized' => 'Neovlašten pristup',
        'forbidden' => 'Zabranjeno',
        'server_error' => 'Greška na serveru',
        'page_not_found' => 'Stranica nije pronađena',
        'go_home' => 'Na početnu',
    ],

    // Dugmad
    'buttons' => [
        'save' => 'Sačuvaj',
        'cancel' => 'Otkaži',
        'delete' => 'Obriši',
        'edit' => 'Uredi',
        'view' => 'Pogledaj',
        'search' => 'Pretraži',
        'filter' => 'Filtriraj',
        'reset' => 'Resetuj',
        'submit' => 'Pošalji',
        'confirm' => 'Potvrdi',
        'back' => 'Nazad',
        'next' => 'Dalje',
        'previous' => 'Nazad',
        'close' => 'Zatvori',
        'yes' => 'Da',
        'no' => 'Ne',
    ],

    // Vrijeme
    'time' => [
        'days' => 'dana',
        'hours' => 'sati',
        'minutes' => 'minuta',
        'seconds' => 'sekundi',
        'day' => 'dan',
        'hour' => 'sat',
        'minute' => 'minut',
        'second' => 'sekund',
        'ago' => 'prije',
        'just_now' => 'Upravo sada',
    ],

    // Statusi
    'status' => [
        'active' => 'Aktivno',
        'inactive' => 'Neaktivno',
        'pending' => 'Na čekanju',
        'approved' => 'Odobreno',
        'rejected' => 'Odbijeno',
        'success' => 'Uspjeh',
        'error' => 'Greška',
        'warning' => 'Upozorenje',
        'info' => 'Info',
    ],

    // Footer
    'footer' => [
        'about' => 'O nama',
        'contact' => 'Kontakt',
        'terms' => 'Uslovi korištenja',
        'privacy' => 'Politika privatnosti',
        'help' => 'Pomoć',
        'faq' => 'FAQ',
        'copyright' => '© :year Aukcije.ba. Sva prava zadržana.',
    ],

    // Ostalo
    'misc' => [
        'loading' => 'Učitavanje...',
        'no_results' => 'Nema rezultata',
        'show_more' => 'Prikaži više',
        'show_less' => 'Prikaži manje',
        'read_more' => 'Pročitaj više',
        'share' => 'Podijeli',
        'copy' => 'Kopiraj',
        'copied' => 'Kopirano!',
        'success' => 'Uspjeh',
        'error' => 'Greška',
    ],

];
