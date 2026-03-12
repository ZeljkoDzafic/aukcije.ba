# 05 - Auction Listing Specification

## Scope

Ovaj dokument razrađuje `T-403: Auction Listing Page`.

## Files To Create

- `app/Livewire/AuctionSearch.php`
- `resources/views/livewire/auction-search.blade.php`
- `resources/views/livewire/auction-card.blade.php`

## Main Layout

- Desktop: sidebar filteri lijevo, rezultati desno
- Mobile: filteri u bottom-sheet ili slide-over panelu
- Listing treba podržati grid i list prikaz

## Filters

- Kategorija
- Cijena od-do
- Stanje artikla
- Lokacija
- Tip aukcije
- Samo sa slikom

Svaka promjena filtera treba imati jasan loading feedback.

## Sort Options

- `Uskoro završava`
- `Najnovije`
- `Cijena: niža ka višoj`
- `Cijena: viša ka nižoj`
- `Najviše ponuda`

## Auction Card Contract

Svaka kartica treba prikazati:

- naslov
- primarnu sliku
- kategoriju
- trenutnu cijenu
- countdown
- broj bidova
- broj watchera
- lokaciju
- CTA `Licitiraj`
- sekundarni CTA `Dodaj u praćenje`

## Real-Time Expectations

- Echo update za `BidPlaced` osvježava cijenu, bid count i end time
- `AuctionEnded` mijenja state kartice u završeno
- Ako aukcija bude produžena, countdown koristi novi `ends_at`

## Empty / Loading / Error States

- Empty: `Nema aukcija za odabrane filtere.`
- Loading skeleton za listu i filtere
- Error state sa retry akcijom ako Livewire zahtjev ne uspije

## Performance Notes

- Početno učitavanje 24 aukcije
- Slike lazy-load
- Realtime update ne smije rerenderovati cijelu stranicu ako se promijenila jedna kartica

## Test Checklist

- Filteri i sort pravilno mijenjaju query string
- Grid/list toggle zadržava trenutno filtriranje
- Empty state i pagination rade korektno
- Kartica se ne lomi za duge naslove i velike cijene
