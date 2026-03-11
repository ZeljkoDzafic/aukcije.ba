# 10 - Competitive Analysis (Regionalna Analiza)

## Pregled Tržišta

Region bivše Jugoslavije ima nekoliko aktivnih platformi za kupoprodaju, ali nijedna ne nudi modernu, kompletnu aukcijsku platformu sa svim mehanizmima.

## Direktni Konkurenti

### 1. Limundo (Srbija)

| Aspekt | Detalji |
|--------|---------|
| **Tržište** | Srbija (primarno), region |
| **Model** | Najbliži eBay modelu |
| **Ključne feature** | Aukcije od 10 dinara, Limundo Cash zaštita kupaca |
| **Prednosti** | Etablirana baza korisnika, prepoznat brand |
| **Slabosti** | Zastarjeli UI/UX, spor razvoj novih feature-a |
| **Šta učiti** | Limundo Cash (escrow) je ključan za povjerenje |

### 2. Aukcije.hr (Hrvatska)

| Aspekt | Detalji |
|--------|---------|
| **Tržište** | Hrvatska |
| **Model** | Klasični aukcijski interfejs |
| **Ključne feature** | Fokus na kolekcionarstvo i filateliju |
| **Prednosti** | Nišna zajednica kolekcionara |
| **Slabosti** | Klasičan (zastarjeli) interfejs, usko tržište |
| **Šta učiti** | Nišni fokus može izgraditi lojalnu bazu |

### 3. OLX.ba (BiH)

| Aspekt | Detalji |
|--------|---------|
| **Tržište** | Bosna i Hercegovina |
| **Model** | Hibrid oglasnika i licitacija |
| **Ključne feature** | Ogromna baza korisnika, jednostavnost |
| **Prednosti** | Dominantan u regionu, brand awareness |
| **Slabosti** | Aukcijski mehanizam sekundaran, nema escrow |
| **Šta učiti** | Jednostavnost UX-a je ključna za masovnu adopciju |

### 4. Bolha.com (Slovenija)

| Aspekt | Detalji |
|--------|---------|
| **Tržište** | Slovenija |
| **Model** | Primarno oglasnik |
| **Ključne feature** | Odlična integracija sa logističkim servisima |
| **Prednosti** | Logistics integracija, pouzdan |
| **Slabosti** | Više oglasnik nego aukcijska kuća |
| **Šta učiti** | Logistička integracija = konkurentska prednost |

## Indirektni Konkurenti

| Platforma | Tržište | Prijetnja |
|-----------|---------|-----------|
| eBay | Globalno | Niska — korisnici žele lokalno |
| Facebook Marketplace | Globalno | Srednja — veliku bazu, ali nema aukcije |
| Kupujem Prodajem | Srbija | Srednja — oglasnik, ali poznata platforma |
| Njuškalo | Hrvatska | Niska — čist oglasnik |

## Competitive Gap Analysis

```
Feature              Limundo  Aukcije.hr  OLX.ba  Bolha  NAŠA PLATFORMA
──────────────────   ───────  ──────────  ──────  ─────  ──────────────
Real-time bidding      ◐         ○          ○       ○         ●
Proxy bidding          ●         ◐          ○       ○         ●
Anti-sniping           ◐         ○          ○       ○         ●
Escrow/Wallet          ●         ○          ○       ○         ●
Mobile-first UI        ○         ○          ◐       ◐         ●
Logistics API          ○         ○          ○       ●         ●
KYC Verification       ◐         ○          ○       ○         ●
Modern tech stack      ○         ○          ○       ◐         ●
Multi-country          ◐         ○          ○       ○         ●
Seller tiers           ○         ○          ◐       ○         ●

● = Potpuno    ◐ = Djelimično    ○ = Ne postoji
```

## Naša Konkurentska Prednost

### 1. Tehnološka superiornost
- Real-time WebSocket update (ne polling)
- Sub-second bid processing sa Redis lock-ovima
- Modern, mobile-first UI sa Tailwind + Livewire

### 2. Regional fokus
- Lokalizirani payment gateway-i (Monri, CorvusPay)
- Integrirani regionalni kuriri (EuroExpress, PostExpress)
- BHS lokalizacija sa razumijevanjem regionalnih dijalekta (Meilisearch)

### 3. Trust & Safety
- Kompletna KYC verifikacija za prodavce
- Escrow sistem koji štiti obje strane
- Transparentan rating sistem

### 4. Seller-friendly ekosistem
- Tiered sistem (Free → Premium → Storefront)
- API za inventar sync (verified sellers)
- Promotion tools za veću vidljivost

## Go-to-Market Strategija

1. **Početni fokus:** BiH tržište (OLX korisnici koji žele pravi aukcijski mehanizam)
2. **Ekspanzija 1:** Srbija (Limundo alternative za korisnike nezadovoljne starim UI-em)
3. **Ekspanzija 2:** Hrvatska + Slovenija (cross-border logistika)
4. **Nišne zajednice:** Kolekcionari, vintage, auto-dijelovi, elektronika
