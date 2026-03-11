# 01 - System Overview

## Šta je Aukcijska Platforma?

High-concurrency aukcijski engine dizajniran za tržište Balkana, sa podrškom za real-time licitiranje, automatske proxy ponude i integriranu regionalnu logistiku.

Tri tipa korisnika: **kupci**, **prodavci** i **administratori/moderatori**.

## Glavni Podsistemi

### 1. Auction Engine (Core)
- Upravljanje bid incrementima i state tranzicijama aukcija
- Proxy Bidding — automatsko licitiranje u ime korisnika do maksimuma
- Anti-Sniping — automatsko produženje aukcije pri kasnim ponudama
- Dinamički bid increments na osnovu trenutne cijene
- **Tech:** Laravel Services + Redis Locks + PostgreSQL transactions

### 2. Notification Hub
- Real-time WebSocket update-ovi za outbid alerte
- Push notifikacije za završetak aukcija (Firebase)
- Email obavještenja (Mailgun/Resend)
- SMS notifikacije za kritične evente (Infobip/Twilio)
- **Tech:** Laravel Reverb + Laravel Echo + Firebase

### 3. Trust & Safety
- KYC verifikacija korisnika (SMS + upload dokumenata)
- Dvosmjerni rating sistem (kupac ↔ prodavac)
- Escrow / interni novčanik za zaštitu transakcija
- Dispute resolution za admina
- **Tech:** Spatie Permissions + Custom Escrow Service

### 4. Logistics Bridge
- API integracija sa regionalnim kuririma (EuroExpress, PostExpress, Overseas)
- Automatsko kreiranje tovarnih listova
- Tracking pošiljki unutar platforme
- **Tech:** Laravel HTTP Client + Courier API adapters

### 5. Javni Website (bez autentifikacije)
- Landing page, kategorije, pretraga
- SEO optimizirane stranice za svaku aukciju
- Blog / novosti
- **Tech:** Laravel Blade + Tailwind CSS

### 6. User Portal (autentificirano)
- Dashboard sa aktivnim aukcijama i watchlistom
- Historija kupovina i prodaja
- Upravljanje internim novčanikom (wallet)
- Poruke između kupaca i prodavaca

### 7. Admin Panel
- Moderacija sadržaja i korisnika
- Upravljanje kategorijama
- Statistika i analitika
- Dispute resolution
- Feature flag upravljanje

## Ključne Metrike Uspjeha

| Metrika | Target (MVP) | Target (6 mj) |
|---------|-------------|---------------|
| Registrirani korisnici | 200 | 2,000 |
| Aktivne aukcije | 100 | 1,000 |
| Uspješno završene transakcije/mj | 50 | 500 |
| Prosječno vrijeme odgovora API-ja | < 200ms | < 100ms |
| WebSocket latency (bid update) | < 500ms | < 200ms |
| Uptime | 99% | 99.5% |

## Ograničenja i Pretpostavke

- Tim: 1 developer + AI asistent (Claude)
- Korisnici primarno iz BiH, Srbije, Hrvatske, Slovenije
- Internet pristup: pretežno mobilni (70%+ saobraćaja)
- Hosting: AWS Frankfurt ili DigitalOcean Frankfurt (nizak latency ka EX-YU)
- Cloudflare obavezan za DDoS zaštitu tokom kritičnih aukcija
- GDPR-kompatibilna obrada ličnih podataka
- Multivalutna podrška: KM, EUR, RSD, HRK
