# 06 - Auction Detail Specification

## Scope

Ovaj dokument razrađuje `T-404: Auction Detail Page`.

## File To Create

- `resources/views/pages/auctions/show.blade.php`

## Layout

- Desktop: gallery i detalji lijevo, bidding panel desno
- Mobile: bidding bar sticky pri dnu, gallery i detalji stacked

## Required Sections

### Gallery
- Glavna slika
- Thumbnail navigacija
- Lightbox / full-screen pregled

### Auction Meta
- Naslov
- Breadcrumb kategorije
- Stanje artikla
- Lokacija
- Kratki seller trust indikatori

### Bidding Panel
- Trenutna cijena
- Minimalni sljedeći bid
- Countdown
- Bid input
- Proxy bid toggle
- `Licitiraj` CTA
- Watchlist akcija

### Description
- Pun opis artikla
- Karakteristike i stanje

### Seller Card
- Avatar
- Ime
- Rating
- Verified badge ako postoji
- CTA `Kontaktiraj prodavca`

### Shipping
- Dostupne metode
- Procjena cijene
- Lokacija slanja

### Bid History
- Collapsible lista zadnjih bidova
- Sakriveni identiteti po potrebi, npr. `a***3`

### Related Auctions
- Grid od 4 do 8 srodnih aukcija

## SEO Requirements

- Dynamic title i description
- Product structured data
- Breadcrumb structured data
- OG image iz primarne slike aukcije
- Canonical URL

## State Handling

- Ako je aukcija završena, bidding panel prelazi u read-only rezultat
- Ako je korisnik vodeći bidder, prikazati pozitivan status
- Ako je korisnik nadlicitiran, status mora biti jasan i akcionabilan

## Test Checklist

- Sticky mobile bid bar ostaje funkcionalan pri scrollu
- Gallery radi sa 1 slikom i sa više slika
- Završena aukcija ne prikazuje aktivan bid form
- Related auctions i seller kartica imaju graceful empty state
