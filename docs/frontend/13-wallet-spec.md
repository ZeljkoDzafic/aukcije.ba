# 13 - Wallet Specification

## Scope

Ovaj dokument razrađuje `T-503: Wallet Management`.

## File To Create

- `resources/views/pages/wallet/index.blade.php`

## Required Sections

### Balance Summary
- `Raspoloživo`
- `U escrowu`
- `Ukupno`

### Deposit
- Brzi iznosi: `10`, `25`, `50`, `100`
- Custom iznos
- Izbor gateway-a:
  - `Wallet`
  - `Stripe`
  - `Monri`
  - `CorvusPay`

Ako neki gateway nije dostupan za trenutni market ili feature flag, ne prikazivati ga.

### Withdrawal
- Iznos
- Bankovni podaci
- Napomena o minimalnom iznosu i mogućem admin odobrenju za veće isplate

### Transactions
- Sortable/filterable lista
- Tipovi:
  - depozit
  - isplata
  - escrow hold
  - escrow release
  - refund
  - komisija

## UX Rules

- Payment i withdrawal status poruke moraju biti vrlo jasne
- Nedovoljan balans mora biti objašnjen, ne samo označen kao error
- Transakcije trebaju imati razumljive BHS oznake umjesto internih enum vrijednosti

## Test Checklist

- Deposit i withdrawal forme prikazuju validation greške uz polje
- Balance summary radi i kada nema transakcija
- Transaction filteri ne lome prikaz na mobilnom
