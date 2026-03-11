# 11 - Trust & Safety

## Pregled

Povjerenje je najvažniji faktor za uspjeh aukcijske platforme. Bez njega, niti kupci niti prodavci neće koristiti platformu.

## KYC (Know Your Customer)

### Nivoi Verifikacije

```
Nivo 0: Neregistriran        → Može pregledati aukcije
Nivo 1: Registriran (email)  → Može licitirati (limit 500 BAM)
Nivo 2: Telefon verificiran  → Može licitirati bez limita, može prodavati (limit 5 aukcija)
Nivo 3: Dokument verificiran → Verified Seller badge, bez limita, API pristup
```

### Verifikacija Telefona (SMS)

```
1. Korisnik unese broj telefona
2. Sistem šalje 6-cifreni OTP (Infobip / Twilio)
3. Korisnik unese OTP u roku od 5 minuta
4. Verifikacija uspješna → Nivo 2
```

### Verifikacija Dokumenta

```
1. Korisnik uploada lični dokument (lična karta / pasoš)
2. Moderator pregleda dokument (manual review)
3. Odobri ili odbije sa komentarom
4. Odobreno → Nivo 3 (Verified Seller)
```

**Napomena:** Za MVP koristimo manual review. Phase 2: integracija sa automatiziranim KYC servisom.

## Escrow Sistem

### Kako Funkcioniše

```
1. Aukcija završava → Kupac pobijedio
     │
     ▼
2. Kupac uplati na Escrow (wallet ili payment gateway)
     │
     ▼
3. Sredstva ZAMRZNUTA na platformi
     │
     ▼
4. Prodavac šalje artikal → unese tracking broj
     │
     ▼
5. Kupac primi artikal
     │
     ├── Potvrdi prijem → Sredstva RELEASED prodavcu (minus komisija)
     │
     └── Otvori dispute → Sredstva ZADRŽANA do rješenja
          │
          ├── Admin odluči u korist kupca → REFUND kupcu
          │
          └── Admin odluči u korist prodavca → RELEASE prodavcu
```

### Escrow Timeline

| Event | Rok |
|-------|-----|
| Kupac mora platiti | 3 dana od završetka aukcije |
| Prodavac mora poslati | 5 dana od plaćanja |
| Kupac mora potvrditi prijem | 7 dana od dostave |
| Auto-release (ako kupac ne reaguje) | 14 dana od dostave |
| Dispute window | 30 dana od transakcije |

## Rating Sistem

### Dvosmjerno Ocjenjivanje

Nakon svake uspješno završene transakcije, **oba učesnika** ocjenjuju jedan drugog.

```
Skala: 1-5 zvjezdica + komentar

Kupac ocjenjuje prodavca:
- Da li je opis bio tačan?
- Brzina slanja
- Komunikacija
- Ukupno iskustvo

Prodavac ocjenjuje kupca:
- Brzina plaćanja
- Komunikacija
- Ukupno iskustvo
```

### Trust Score Kalkulacija

```
Trust Score = (Prosječna ocjena × 0.6) + (Broj transakcija bonus × 0.3) + (Verifikacija bonus × 0.1)

Verifikacija bonus:
- Email verified: +0.2
- Phone verified: +0.5
- Document verified: +1.0

Transakcija bonus (logaritamski):
- 0 transakcija: 0
- 10 transakcija: +0.5
- 50 transakcija: +0.8
- 100+ transakcija: +1.0
```

### Trust Badges

| Badge | Uslov |
|-------|-------|
| Verified Seller | KYC Nivo 3 |
| Top Rated | Trust Score > 4.5 + 50 transakcija |
| Power Seller | 100+ uspješnih prodaja u zadnjih 6 mjeseci |
| New Member | < 30 dana na platformi (upozorenje, ne badge) |

## Dispute Management

### Flow

```
1. Kupac otvara dispute → Razlog + opis + dokaz (slike)
     │
     ▼
2. Prodavac obavješten → 48h da odgovori
     │
     ▼
3. Moderator pregleda
     │
     ├── Dodatni dokazi potrebni → Zahtjev od obje strane
     │
     ├── Rješenje u korist kupca → Full/Partial refund
     │
     └── Rješenje u korist prodavca → Escrow released
```

### Dispute Razlozi

| Razlog | Opis |
|--------|------|
| `item_not_received` | Artikal nije stigao |
| `item_not_as_described` | Artikal značajno drugačiji od opisa |
| `item_damaged` | Artikal oštećen u transportu |
| `counterfeit` | Falsifikat |
| `seller_cancelled` | Prodavac otkazao nakon prodaje |

## Anti-Fraud Mehanizmi

| Mehanizam | Implementacija |
|-----------|---------------|
| Shill bidding detection | ML model koji detektuje sumnjive bid pattern-e |
| IP throttling | Max 3 bid-a sa iste IP u 1 min |
| Account linking | Detekcija višestrukih naloga (IP + device fingerprint) |
| Bid retraction limits | Max 2 retrakcije u 30 dana |
| Listing quality check | Auto-flag za copy-paste opise i stocke slike |
| Velocity checks | Alert za neobično brzo kreiranje mnogo aukcija |

## Content Moderation

```
Automatska moderacija:
1. Keyword filter → Blokirani termini
2. Image analysis → NSFW detekcija (API)
3. Price anomaly → Alert za sumnjive cijene

Manual moderacija (queue):
1. Svi novi prodavci → Prve 3 aukcije idu na review
2. Reportovani listinzi → Moderator queue
3. Editovani listinzi sa aktivnim bidovima → Auto review
```
