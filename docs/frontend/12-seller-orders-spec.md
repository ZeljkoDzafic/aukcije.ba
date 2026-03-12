# 12 - Seller Orders Specification

## Scope

Ovaj dokument razrađuje `T-502: Seller Orders Management`.

## Files To Create

- `resources/views/pages/seller/orders/index.blade.php`
- `resources/views/pages/seller/orders/show.blade.php`

## Orders Index

### Tabs
- `Čeka uplatu`
- `Spremno za slanje`
- `Poslano`
- `Završeno`
- `Sporovi`

### Table / Cards
- Broj narudžbe
- Kupac
- Artikal
- Iznos
- Status
- Datum
- Quick action

Na mobilnom koristiti card list umjesto široke tabele.

## Order Detail

### Required Sections
- Buyer info
- Adresa dostave
- Artikal i finalna cijena
- Komisija i neto iznos za sellera
- Timeline transakcije
- Tracking broj i shipping update akcije

### Main Actions
- `Označi kao poslano`
- unos tracking broja
- eventualni export / print shipping summary

## Trust Considerations

- Ako postoji dispute ili problem sa plaćanjem, to mora biti istaknuto iznad folda
- Seller ne smije previdjeti rokove za slanje

## Test Checklist

- Tab filteri ispravno grupišu narudžbe
- Order detail prikazuje timeline u ispravnom redoslijedu
- Shipping akcija je dostupna samo u validnom statusu
