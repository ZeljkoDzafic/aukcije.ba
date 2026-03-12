# 10 - Seller Dashboard Specification

## Scope

Ovaj dokument razrađuje `T-500: Seller Dashboard`.

## File To Create

- `resources/views/pages/seller/dashboard.blade.php`

## Layout

- Koristi `layouts.seller`
- Stat cards gore, zatim aktivne aukcije i operativni blokovi ispod

## Required Sections

### Stat Cards
- `Aktivne aukcije`
- `Ukupna prodaja`
- `Wallet balans`
- `Prosječna ocjena`

### Active Auctions
- Lista ili tabela sa:
  - naslov
  - trenutna cijena
  - broj ponuda
  - countdown
  - status
  - quick link na detalj

### Orders To Ship
- Narudžbe koje čekaju slanje
- Tracking/status signal mora biti jasan i vizualno izdvojen

### Recently Ended
- Nedavno završene aukcije sa ishodom

### Revenue Snapshot
- Placeholder za chart zadnjih 30 dana
- Ako chart data ne postoji, fallback summary kartica

### Tier Block
- Trenutni tier: `Free`, `Premium`, `Storefront`
- Limit aktivnih aukcija
- Komisija
- Upgrade CTA ako korisnik nije na višem tieru

## Tier Messaging

- Free tier korisniku banner prikazuje kad se približi limitu
- Premium korisniku može prikazati benefite storefront nivoa

## Test Checklist

- Free tier banner reaguje na blizinu limita
- Dashboard ostaje koristan i kad seller nema nijednu aktivnu aukciju
- Orders to ship blok ima prioritet kad postoje neobrađene narudžbe
