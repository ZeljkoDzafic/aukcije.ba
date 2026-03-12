# 09 - Watchlist Specification

## Scope

Ovaj dokument razrađuje `T-407: Watchlist`.

## Files To Create

- `app/Livewire/Watchlist.php`
- odgovarajući Blade view za watchlist ekran

## Main Requirements

- Prikaz aukcija kao grid `AuctionCard` komponenti
- Filteri:
  - `Aktivne`
  - `Završavaju danas`
  - `Završene`
- Sort:
  - `Uskoro završava`
  - `Nedavno dodano`

## Interaction Rules

- Inline uklanjanje iz watchliste bez full page reloada
- Realtime update cijene i statusa preko Echo događaja
- Ako aukcija završi, kartica ostaje vidljiva u odgovarajućem filteru uz jasan status

## Empty State

Poruka:

`Nemaš praćenih aukcija. Započni pretragu i dodaj artikle koji te zanimaju.`

CTA:

- `Pregledaj aukcije`

## Test Checklist

- Filter i sort kombinacije rade konzistentno
- Uklanjanje iz watchliste odmah sklanja karticu iz trenutnog prikaza
- Empty state se prikazuje kada korisnik nema nijednu praćenu aukciju
