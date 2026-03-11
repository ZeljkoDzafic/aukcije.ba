# 09 - Activity Plan (Roadmap)

## 5-Fazni Razvoj

```
Faza 1          Faza 2          Faza 3          Faza 4          Faza 5
Foundation      Auction Engine  Real-time       Regional Edge   Launch
(3 sedmice)     (4 sedmice)     (2 sedmice)     (3 sedmice)     (2 sedmice)
────────────    ────────────    ────────────    ────────────    ────────────
Laravel setup   Bidding logic   WebSockets      Payments        Beta test
Database        Proxy bids      Reverb + Echo   Logistics API   Bug fixes
Auth + RBAC     Anti-sniping    Live updates    KYC system      Performance
Basic CRUD      Search          Notifications   Escrow/Wallet   Soft launch
```

**Ukupno: ~14 sedmica (3.5 mjeseca)**

---

## Faza 1: Foundation (Sedmica 1-3)

### Cilj: Funkcionalan skeleton sa auth i CRUD-om

| Sedmica | Task | Deliverable |
|---------|------|-------------|
| 1 | Laravel setup, Docker (Sail), PostgreSQL, Redis | Running dev environment |
| 1 | Database migrations (users, profiles, categories) | Core schema |
| 1 | Auth (Breeze/Jetstream), Spatie roles setup | Login, register, roles |
| 2 | Auction CRUD (create, edit, list, detail) | Seller može kreirati aukciju |
| 2 | Category management (admin) | Hijerarhijske kategorije |
| 2 | Image upload (S3 / local) | Multi-image za aukcije |
| 3 | User dashboard (buyer + seller views) | Osnovni dashboard |
| 3 | Basic search (Eloquent, bez Meilisearch) | Pretraga po naslovu/kategoriji |
| 3 | Blade layouts + Tailwind styling | Guest, App, Admin layouts |

### Milestone: Prodavac može kreirati aukciju sa slikama, kupac može pregledati listing.

---

## Faza 2: Auction Engine (Sedmica 4-7)

### Cilj: Kompletna bidding logika sa svim mehanizmima

| Sedmica | Task | Deliverable |
|---------|------|-------------|
| 4 | BiddingService — basic bid placement | Korisnik može licitirati |
| 4 | Redis locking za concurrency | Race condition zaštita |
| 4 | Bid increments tabela + logika | Dinamički koraci licitacije |
| 5 | Proxy Bidding sistem | Auto-bidding do max iznosa |
| 5 | Anti-Sniping mehanizam | Produženje aukcije |
| 5 | Auction state machine (draft→active→finished→sold) | Lifecycle management |
| 6 | EndExpiredAuctions scheduled command | Auto-završetak aukcija |
| 6 | Meilisearch integracija (Scout) | Full-text search sa filterima |
| 6 | Watchlist funkcionalnost | Praćenje aukcija |
| 7 | Buy Now (fixed price) podrška | Hybrid aukcije |
| 7 | Auction promotion/featured | Plaćeno izdvajanje |
| 7 | Testing — bidding edge cases | PHPUnit + Pest tests |

### Milestone: Kompletna aukcijska logika — proxy, anti-sniping, search, watchlist.

---

## Faza 3: Real-time (Sedmica 8-9)

### Cilj: Live updates bez page refresh-a

| Sedmica | Task | Deliverable |
|---------|------|-------------|
| 8 | Laravel Reverb setup + Echo frontend | WebSocket infrastruktura |
| 8 | BidPlaced event → live price update | Real-time cijena |
| 8 | AuctionExtended event → timer update | Anti-sniping live feedback |
| 8 | AuctionEnded event → rezultat | Live završetak |
| 9 | OutbidNotification (private channel) | Outbid alert |
| 9 | Push notifications (Firebase) | Mobile push |
| 9 | Email notifications (Mailgun/Resend) | Outbid, won, ended emails |
| 9 | Notification preferences (user settings) | Kontrola notifikacija |

### Milestone: Korisnik vidi live ažuriranje cijena i prima instant obavještenja.

---

## Faza 4: Regional Edge (Sedmica 10-12)

### Cilj: Plaćanje, dostava, povjerenje

| Sedmica | Task | Deliverable |
|---------|------|-------------|
| 10 | Wallet sistem (deposit, withdraw) | Interni novčanik |
| 10 | Escrow mehanizam | Zaštita kupaca |
| 10 | Payment gateway (Monri/CorvusPay/Stripe) | Online plaćanje |
| 11 | KYC verifikacija (SMS + dokument upload) | Verified seller badge |
| 11 | Rating sistem (buyer ↔ seller) | Dvosmjerno ocjenjivanje |
| 11 | Dispute management (admin) | Sporovi i rješenja |
| 12 | Logistics API (EuroExpress, PostExpress) | Auto tovarni listovi |
| 12 | Shipment tracking | Praćenje pošiljke |
| 12 | Messaging (buyer ↔ seller) | In-app poruke |

### Milestone: End-to-end flow: licitiranje → plaćanje → dostava → ocjena.

---

## Faza 5: Launch (Sedmica 13-14)

### Cilj: Beta test i soft launch

| Sedmica | Task | Deliverable |
|---------|------|-------------|
| 13 | Beta testing (invited sellers, 50-100 korisnika) | Bug reports |
| 13 | Performance optimization (N+1, caching, indexes) | < 200ms response time |
| 13 | Security audit (OWASP top 10 check) | Security fixes |
| 14 | SEO setup (meta tags, sitemap, structured data) | Google indexing |
| 14 | Analytics (GA4 / Plausible) | Tracking |
| 14 | Soft launch | Platforma live! |

### Milestone: Platforma dostupna javnosti sa prvim aktivnim aukcijama.

---

## Post-Launch Roadmap

| Feature | Prioritet | ETA |
|---------|-----------|-----|
| Mobile app (React Native / Flutter) | High | +3 mjeseca |
| Dutch auctions | Medium | +1 mjesec |
| Seller API (inventory sync) | Medium | +2 mjeseca |
| Multi-currency support (EUR, RSD) | High | +1 mjesec |
| Storefront (custom subdomains) | Low | +4 mjeseca |
| AI preporuke (similar items) | Low | +6 mjeseci |
