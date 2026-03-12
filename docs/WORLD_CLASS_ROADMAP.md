# World-Class Roadmap

> **Operativni plan** za podizanje Aukcije.ba iz production-ready stanja u vrhunsku regionalnu aukcijsku platformu.
> Za status po fazama i konkretne task ID-eve gledati [TASKS.md](TASKS.md).

---

## Trenutno stanje (March 2026)

| Sloj | Status | Napomena |
|------|--------|----------|
| Bidding Engine | ✅ Production-ready | Redis lock, proxy, anti-sniping, UUID-safe |
| Events & Listeners | ✅ Ispravljen | BidPlaced nosi previousWinnerId, listeneri queued+chunked |
| API Controllers | ✅ Implementirani | Category, Search, Order, Rating, Statistics — svi stub-ovi zamijenjeni |
| Livewire Components | ✅ Implementirani | AuctionSearch, Watchlist, WalletManager, CreateAuctionWizard, OrderFulfillment |
| Vue BiddingConsole | ✅ Production-ready | UUID types, isExpired, proxyMax sync |
| Payment Services | ✅ Refaktorisan | DI injection, initiateDeposit flow, webhook controller |
| Shipping Services | ✅ Refaktorisan | DI injection, HMAC webhook verifikacija |
| Frontend UI | 🔴 In progress | Livewire/Blade skeletons postoje, trebaju backend hookup |
| Trust & Safety | 🟡 Osnova | Shill detection heuristike nedostaju, fraud scoring nije implementiran |
| GDPR / Compliance | 🔴 Kritično | Data export, erasure, cookie consent nisu implementirani |
| Observability | 🟡 Osnova | Prometheus/Grafana setup postoji, alerting pravila nedostaju |
| Seller Tools | 🔴 Nedostaje | Analytics, bulk ops, template-i, scheduled start time |
| Admin Operations | 🔴 In progress | Moderation UI skeleti postoje, bulk akcije nedostaju |

---

## Faza 1: Trust & Safety (M1 — kritično)

**Zašto je ovo prioritet 1:** Bez pouzdanog trust sloja platforma je ranjiva na shill bidding, abuse i financijske gubitke.

### Backend (🟣 Claude)

| ID | Task | Detalji |
|----|------|---------|
| T-1100 | FraudScoringService | Risk score po korisniku (0-100): account age, bid velocity, win/bid ratio, device fingerprint. Svaka bid akcija ga ažurira. |
| T-1101 | ShillBiddingDetector | Heuristika: isti IP, isti device, isti seller na aukciji. Auto-flag za admin review queue. |
| T-1102 | RiskReviewQueue | `AdminLog` proširiti sa `risk_level`, `risk_signals`, `review_status`. Admin API endpoint za review queue. |
| T-1103 | AuditTrailMiddleware | Middleware koji loguje sve POST/PUT/DELETE admin i seller akcije u `admin_logs` automatski. |
| T-1104 | SellerReputationScore | Izračun: fulfilment_rate × 0.4 + punctuality × 0.3 + dispute_rate × 0.2 + response_time × 0.1. Periodični job svaka 24h. |
| T-1105 | KycEnforcementService | Striktna implementacija ograničenja po KYC nivou: email=praćenje, sms=licitiranje, dokument=prodaja+withdrawal. |
| T-1106 | 2FA za prodavace | TOTP (Google Authenticator) obavezan za verified_seller i admin rolove. `laravel-google-2fa`. |

### Frontend (🟢 Codex)

| ID | Task | Detalji |
|----|------|---------|
| T-1150 | SellerReputationBadge | Badge komponenta sa tooltip-om: fulfilment rate, avg response time, disputes. Prikazati na auction detail i seller profilu. |
| T-1151 | 2FA Enrollment UI | Livewire wizard: QR kod generisanje → unos koda → backup codes prikaz. |
| T-1152 | KYC Status Dashboard | Buyer/seller vidi koji nivo ima, šta mu fali, CTA za upload dokumenta. |

---

## Faza 2: Discovery & Search (M1)

**Zašto je ovo prioritet 2:** Kupac koji ne nađe šta traži, ne kupuje. Homepage mora aktivno voditi ka kupovini.

### Backend (🟣 Claude)

| ID | Task | Detalji |
|----|------|---------|
| T-1200 | Meilisearch schema | Definisati `filterableAttributes` (category, status, location, condition, price_min/max, ends_at), `sortableAttributes` (current_price, ends_at, created_at, bid_count), `rankingRules` (words, typo, proximity, attribute, sort, exactness). Migration job koji reindeksira. |
| T-1201 | SavedSearchService | `saved_searches` tabela (user_id, query JSON, last_notified_at). Job koji svaka 4h poredi nove aukcije sa svim saved searches i šalje notifikacije. |
| T-1202 | HomepageDataService | `Cache::remember` za 4 sekcije: featured (is_featured=true), ending_soon (ends_at < now+2h, active), new_arrivals (created_at > now-24h), most_watched (watchlist count). TTL: 5min. |
| T-1203 | SellerDirectoryController | API endpoint sa seller profilima: ime, avatar, reputacija, broj aktivnih aukcija, kategorije. Paginacija, sort po reputaciji/aktivnosti. |
| T-1204 | ReservePrice API | `BiddingService` proširiti: ako je bid >= reserve_price, unlockovati "buy now" opciju. Seller može postaviti reserve pri kreiranju aukcije. |

### Frontend (🟢 Codex)

| ID | Task | Detalji |
|----|------|---------|
| T-1250 | HomepageSections | 4 Livewire sekcije (featured, ending_soon, new_arrivals, most_watched) sa lazy loadingom. |
| T-1251 | SavedSearchUI | Input "Sačuvaj ovu pretragu" sa bell iconom. Livewire: prikaz, brisanje, toggle notifikacija. |
| T-1252 | SellerDirectory | Javna stranica `/sellers` sa profilima, filterom po kategoriji i sortom po reputaciji. |
| T-1253 | CategoryLandingPages | Blade template za `/kategorije/{slug}` sa hero sekcijom, top artiklima i SEO copy. |
| T-1254 | ReservePriceBadge | BiddingConsole: badge "Rezervna cijena dostignuta" / "Rezervna cijena: ???" (prikazati samo ako je seller dozvolio prikaz). |

---

## Faza 3: Seller Command Center (M2)

**Zašto M2:** Seller zadovoljstvo direktno utiče na supply platforme.

### Backend (🟣 Claude)

| ID | Task | Detalji |
|----|------|---------|
| T-1300 | SellerStatsController | API: GMV po periodu, sell-through rate, avg days to sell, top kategorije, dispute rate, avg response time. Cache 1h. |
| T-1301 | AuctionTemplateService | `auction_templates` tabela. Seller može sačuvati auction kao template. `AuctionService::createFromTemplate()`. |
| T-1302 | BulkAuctionService | Service za bulk operacije: publish N draftova, end N aktivnih, clone N aukcija. Svaka operacija kao queued job. |
| T-1303 | ScheduledStartTime | `AuctionService::schedule()` — aukcija ostaje u `scheduled` statusu dok ne dođe `starts_at`. Cron job svaka minuta. |
| T-1304 | SecondChanceOffer | Nakon završene aukcije, seller može ponuditi 2. šansu drugom licitantu. `second_chance_offers` tabela, notifikacija kupcu. |
| T-1305 | SellerTierEnforcement | `AuctionService::store()` mora provjeriti tier limite (Free: 5, Premium: 50, Storefront: ∞) i komisiju (8/5/3%). |
| T-1306 | PaymentDeadlineAutoCancelJob | Job koji svaka sat provjeri ordere sa expired payment deadline i automatski ih otkaže + vrati escrow. |

### Frontend (🟢 Codex)

| ID | Task | Detalji |
|----|------|---------|
| T-1350 | SellerAnalyticsDashboard | Livewire: GMV chart (Chart.js), sell-through, top artikli tabela, dispute rate. Period: 7d/30d/90d. |
| T-1351 | AuctionTemplateUI | Seller može sačuvati aukciju kao template i kreirati novu iz template-a. Livewire wizard. |
| T-1352 | BulkOperationsUI | Seller checklist sa bulk publish/end/clone akcijama. Potvrda modala. |
| T-1353 | ScheduledStartPicker | DateTimePicker komponenta u CreateAuctionWizard koraku 3. |

---

## Faza 4: Buyer Experience (M2)

### Backend (🟣 Claude)

| ID | Task | Detalji |
|----|------|---------|
| T-1400 | GDPRDataExportJob | Job koji kompajlira sve podatke korisnika (profil, bids, orders, messages, watchlist) u JSON/ZIP. Download link šalje se emailom. |
| T-1401 | GDPRErasureService | `User::anonymize()` — zamjenjuje PII sa anonimiziranim podacima, čuva transakcione zapise za legal. |
| T-1402 | PushNotificationService | Firebase FCM za web push. Notifikacije: outbid, auction_won, payment_due, shipped. |
| T-1403 | WishlistReminderJob | Svaka 24h provjeri watchlist artikle koji uskoro ističu (< 2h) i pošalje push/email reminder. |

### Frontend (🟢 Codex)

| ID | Task | Detalji |
|----|------|---------|
| T-1450 | LiveBidFeedComponent | Vue komponenta koja prikazuje posljednjih 10 bidova u real-time (Echo listener). Animirani feed sa iznosom, username, timestamp. |
| T-1451 | PWAManifest | `manifest.json`, service worker za offline cache statičnih resursa, install prompt. |
| T-1452 | MobileOneTapBid | Na mobilnom, dugme za quick bid sa predefinisanim iznosom (minimum_bid). Haptic feedback. |
| T-1453 | BlurhashPlaceholders | Slike u listingu renderisati sa blurhash placeholder-om dok se učitavaju. |
| T-1454 | SimilarAuctionsSection | Na auction detail stranici: "Slične aukcije" (ista kategorija, active, ordered by ends_at). |
| T-1455 | CookieConsentBanner | GDPR-compliant cookie consent. Kategorije: necessary, analytics, marketing. |
| T-1456 | GDPRSettingsUI | Na profilu: "Preuzmi moje podatke" i "Izbriši račun" dugmad sa potvrdom modala. |

---

## Faza 5: Admin Operations (M2)

### Backend (🟣 Claude)

| ID | Task | Detalji |
|----|------|---------|
| T-1500 | BulkModerationService | Service za bulk approve/reject aukcija sa razlogom. Batch `AdminLog` zapis. Cache invalidacija. |
| T-1501 | KYCBackofficeController | Admin API: listanje korisnika sa pending KYC, pregled uploadovanih dokumenata, approve/reject sa napomenom. |
| T-1502 | CategoryMerchandizingService | Drag-and-drop sort order za kategorije. `featured` flag za homepage placement. |
| T-1503 | AdminAnalyticsDashboard | API endpoint: GMV daily/weekly/monthly, active auctions count, new users, conversion rate, top sellers, dispute rate. |
| T-1504 | DBIndicesMigration | Kritične migracije: `bids(auction_id, created_at)`, `auctions(status, ends_at)`, `auctions(seller_id, status)`, `wallet_transactions(wallet_id, created_at)`, `watchlist(user_id)`, `orders(buyer_id, status)`. |

### Frontend (🟢 Codex)

| ID | Task | Detalji |
|----|------|---------|
| T-1550 | AdminBulkModerationUI | Moderation queue sa checkbox selekcijom, bulk approve/reject, inline preview. |
| T-1551 | KYCBackofficeUI | Admin pregled dokumenata sa lightbox, approve/reject sa napomenom, status history. |
| T-1552 | AdminAnalyticsUI | Dashboard sa Chart.js grafovima: GMV trend, user growth, auction conversion. Period selector. |

---

## Faza 6: Performance & Observability (M3)

### Backend (🟣 Claude)

| ID | Task | Detalji |
|----|------|---------|
| T-1600 | HorizonQueueConfig | Konfigurisati Laravel Horizon: `default` (8 workers), `notifications` (4 workers), `high` za bids (2 workers, prioritet). Alerting za failed jobs > 10. |
| T-1601 | SLOMonitoringJob | Periodični job koji mjeri p99 response time za bidding API, search API i checkout. Šalje metriku na Grafana. Alert ako p99 > 500ms. |
| T-1602 | QueryOptimizationAudit | Telescope/Debugbar audit svih N+1 upita. Dodati `->with()` eager loads gdje nedostaju. |
| T-1603 | ImageOptimizationPipeline | `spatie/laravel-medialibrary` sa Imgix URL transformacijama (width, format=webp, quality). CDN-ready URL generation. |

---

## Redoslijed prioriteta

```
M1 (odmah):
  T-1100..1106  Trust & Safety backend
  T-1150..1152  Trust & Safety frontend
  T-1200..1204  Discovery backend
  T-1250..1254  Discovery frontend
  T-1504        DB Indices (kritično za performance pod opterećenjem)

M2 (sljedeće):
  T-1300..1306  Seller Command Center backend
  T-1350..1353  Seller Command Center frontend
  T-1400..1403  Buyer Experience backend
  T-1450..1456  Buyer Experience frontend
  T-1500..1503  Admin Operations backend
  T-1550..1552  Admin Operations frontend

M3 (przed launch):
  T-1600..1603  Performance & Observability
```

---

## Definicija "World-Class" za aukcijsku platformu

- **P99 bidding latency < 200ms** pod 500 concurrent bids
- **Zero wrong-user notifications** (fixed u March 2026 session)
- **Fraud detection** flaguje shill bidding u < 1 minuti
- **GDPR compliance** — data export/erasure u < 24h
- **Seller sell-through rate > 60%** zahvaljujući discovery featureima
- **Admin može moderirati 100 aukcija** za < 5 minuta (bulk ops)
- **Lighthouse score > 90** (performance, accessibility, SEO)

---

**Zadnje ažuriranje:** March 2026 (post-implementation audit)
**Naredni review:** Nakon M1 completion
