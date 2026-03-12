# 08 - Buyer Dashboard Specification

## Scope

Ovaj dokument razrađuje `T-406: Buyer Dashboard`.

## File To Create

- `resources/views/pages/dashboard.blade.php`

## Layout

- Koristi `layouts.app`
- Desktop: stat cards gore, zatim dvije glavne kolone
- Mobile: sve sekcije stacked, najbitnije akcije prve

## Required Sections

### Stat Cards
- `Aktivne ponude`
- `Dobijene aukcije`
- `Praćene aukcije`
- `Wallet balans`

### Active Bids
- Aukcije gdje je korisnik lider ili je nedavno licitirao
- Prikaz: naslov, trenutna cijena, status korisnika, countdown, CTA

### Watchlist Preview
- Aukcije iz watchliste sortirane po `ending soon`
- Inline remove akcija nije obavezna ovdje ako postoji poseban watchlist screen

### Won Auctions
- Aukcije koje čekaju plaćanje ili isporuku
- Jasan status badge: `Čeka uplatu`, `Plaćeno`, `Poslano`, `Završeno`

### Quick Actions
- `Pregledaj aukcije`
- `Moje narudžbe`
- `Poruke`
- `Wallet`

## Priority UX Rules

- Ako postoji order koji čeka uplatu, taj blok ide iznad ostalih listi
- Ako je watchlist prazna, prikazati koristan empty state sa CTA ka pretrazi
- Wallet kartica treba jasno odvojiti raspoloživi balans od escrow iznosa ako backend to vrati

## Test Checklist

- Dashboard radi sa praznim podacima
- Najkritičnije akcije ostaju iznad prevoja na mobilnom
- Status badge-ovi su konzistentni sa order lifecycle terminologijom
