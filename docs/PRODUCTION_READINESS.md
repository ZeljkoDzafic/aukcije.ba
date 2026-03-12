# Production Readiness Checklist

> Za world-class roadmap i konkretne task ID-eve narednih faza gledati [WORLD_CLASS_ROADMAP.md](WORLD_CLASS_ROADMAP.md).

---

## Executive Summary

**Trenutni status:** 82% Complete (post-implementation audit, March 2026)

| Component | Status | Napomena |
|-----------|--------|----------|
| Backend API | ✅ 100% | Svi endpoint-i implementirani, stub-ovi zamijenjeni |
| Business Logic | ✅ 95% | Services, models, migrations, DI injection — kompletni |
| Events & Real-time | ✅ 100% | BidPlaced ispravljen, listeneri queued+chunked |
| Payment Flow | ✅ 100% | initiateDeposit, webhook controller, singleton DI |
| Shipping Flow | ✅ 100% | DI injection, HMAC webhook verifikacija |
| Frontend UI | ⚠️ 40% | Blade/Livewire skeletons postoje, backend hookup u toku |
| Infrastructure | ⚠️ 65% | Core setup done, CDN/SSL/indices pending |
| Trust & Safety | ❌ 30% | Osnova postoji, fraud scoring + shill detection pending |
| GDPR / Compliance | ❌ 20% | Legal dokumenti su drafts, data export/erasure nije implementiran |
| Testing | ⚠️ 75% | 139 testova, 402 assertions — prolaze; E2E pending |
| Observability | ⚠️ 70% | Prometheus/Grafana setup, alerting pravila nedostaju |

---

## Production Readiness Score

| Kategorija | Score | Status |
|------------|-------|--------|
| Backend API | 100% | ✅ Production Ready |
| Database Schema | 100% | ✅ Production Ready |
| Business Logic Services | 95% | ✅ Near Ready |
| Events & Notifications | 100% | ✅ Production Ready |
| Payment & Escrow Flow | 100% | ✅ Production Ready |
| Shipping & Logistics | 100% | ✅ Production Ready |
| DevOps/CI/CD | 90% | ✅ Near Ready |
| Frontend UI | 40% | ⚠️ In Progress |
| Trust & Safety | 30% | ❌ Critical Gap |
| Security | 80% | ⚠️ Needs Audit |
| GDPR / Compliance | 20% | ❌ Critical Gap |
| Testing (unit/feature) | 90% | ✅ Near Ready |
| Testing (E2E/load) | 30% | ⚠️ Needs UI completion |
| Monitoring & Alerting | 70% | ⚠️ Needs Alert Rules |
| DB Performance Indices | 40% | ❌ Critical — migracije pending |
| **OVERALL** | **82%** | 🚧 **Near Production Ready** |

---

## Što je urađeno (March 2026 session)

### Ispravke kritičnih bugova

- [x] `BidPlaced` event sada nosi `previousWinnerId` — eliminisan bug pogrešnog korisnika u outbid notifikaciji
- [x] `SendOutbidNotification` prepisan: `ShouldQueue`, koristi `previousWinnerId`, provjerava notification preferences
- [x] `NotifyWatchers` prepisan: `ShouldQueue`, `chunk(100)`, preskače winnera
- [x] `WalletManager::deposit()` — ispravljen flow: redirect na payment gateway umjesto direktnog kreditiranja

### Stub controlleri implementirani

- [x] `CategoryController` — `Cache::remember('categories:tree', 300)`, active parent categories, children, auctions_count
- [x] `SearchController` — Scout branch + Eloquent fallback, svi filteri, paginate(20)
- [x] `OrderController` (buyer) — paginate, eager loads, available_actions, Policy authorization
- [x] `RatingController` — `RatingService::rateUser()`, validates completed order, recalculates trust score
- [x] `Admin\StatisticsController` — `Cache::remember('admin_stats', 300)`
- [x] `Admin\UserController` — `?is_banned=` filter, paginate(50), withCount + limit(5) za show()
- [x] `Seller\OrderController` — status/search filteri, double-ship zaštita, courier validation, ItemShippedNotification

### Livewire refaktori

- [x] `AuctionSearch` — `WithPagination`, `#[Url]` na svim filterima, Scout/Eloquent branch, `->through()` mapping
- [x] `Watchlist` — `WithPagination`, DB-level status filter, `->through()` mapping
- [x] `WalletManager` — `WithPagination`, ispravljen deposit flow, transakcije DB-filtrirane
- [x] `CreateAuctionWizard` — S3-aware storage, `$durationDays` opcije, shipping validation, Schema guards uklonjeni
- [x] `OrderFulfillment` — courier validation, buyer notification, Schema::hasColumn samo za opcionalne kolone

### Services & Architecture

- [x] `PaymentService` — DI constructor injection, `initiateDeposit()` metoda, `getAvailableGateways()` sa `$amount`
- [x] `ShippingService` — DI constructor injection, courier mapa
- [x] `AppServiceProvider` — singleton binding za sve gateway i courier klase
- [x] `CourierWebhookController` — HMAC signature verifikacija, poziva `ShippingService::handleWebhook()`
- [x] `routes/api.php` — webhook route bez auth middleware
- [x] `BiddingConsole.vue` — UUID types (String), `isExpired` computed, `proxyMax` sync sa `bid_increment`

---

## Kritični path do launchа

### Faza 1: DB Indices (ODMAH — blokira performance)

```sql
-- Kritične migracije koje nedostaju
CREATE INDEX idx_bids_auction_created ON bids(auction_id, created_at DESC);
CREATE INDEX idx_auctions_status_ends ON auctions(status, ends_at);
CREATE INDEX idx_auctions_seller_status ON auctions(seller_id, status);
CREATE INDEX idx_wallet_tx ON wallet_transactions(wallet_id, created_at DESC);
CREATE INDEX idx_watchlist_user ON watchlist(user_id);
CREATE INDEX idx_orders_buyer_status ON orders(buyer_id, status);
```

**Task:** T-1504 (🟣 Claude)

### Faza 2: Frontend hookup (HIGH PRIORITY) 🟡

**Codex taskovi koji blokiraju user testing:**

- [ ] **T-403: AuctionSearch** — hookup Eloquent queries sa Scout search branch
- [ ] **T-404: AuctionDetail** — prikaz seller reputation badge, bidding console integracija
- [ ] **T-406: BuyerDashboard** — My bids, orders, watchlist, escrow status
- [ ] **T-500: SellerDashboard** — draft/active/ended aukcije, fulfilment queue
- [ ] **T-502: SellerOrders** — ship, track, message buyer
- [ ] **T-503: WalletFrontend** — deposit (redirect flow), withdraw, transakcije

### Faza 3: Trust & Safety (KRITIČNO za launch) 🔴

- [ ] **T-1100: FraudScoringService** (🟣 Claude)
- [ ] **T-1101: ShillBiddingDetector** (🟣 Claude)
- [ ] **T-1150: SellerReputationBadge** (🟢 Codex)

### Faza 4: GDPR / Compliance (OBAVEZNO za EU tržište) 🔴

- [ ] **T-1400: GDPRDataExportJob** (🟣 Claude)
- [ ] **T-1401: GDPRErasureService** (🟣 Claude)
- [ ] **T-1455: CookieConsentBanner** (🟢 Codex)
- [ ] **T-1456: GDPRSettingsUI** (🟢 Codex)
- [ ] Privacy Policy (finalizovati) — Legal Team
- [ ] Terms of Service (finalizovati) — Legal Team
- [ ] Cookie Policy (kreirati) — Legal Team

### Faza 5: Infrastructure (PARALLEL) 🟢

#### SSL/TLS
- [ ] Let's Encrypt certifikati
- [ ] Auto-renewal (certbot)
- [ ] HSTS headers
- [ ] TLS 1.3 enforcement

#### CDN & Performance
- [ ] CloudFlare/CloudFront setup
- [ ] Image CDN (Imgix)
- [ ] Cache invalidation strategija

#### Database
- [ ] Read replica konfiguracija
- [ ] Connection pooling (PgBouncer)
- [ ] Backup verifikacija testovi

#### Redis
- [ ] Sentinel za HA
- [ ] Persistence konfiguracija
- [ ] Memory optimization

#### Load Balancing
- [ ] Rate limiting per IP (nginx)
- [ ] Health check endpoints

---

## Launch Strategy

### Stage 1: Alpha (Internal) — 3-4 sedmice
- Frontend hookup kompletirati (Faza 2)
- DB indices migracije (T-1504)
- Interna testiranja bidding flow-a

### Stage 2: Beta (Invite-only) — 2-3 sedmice
- Trust & Safety osnova (T-1100, T-1101)
- Seller reputation badge (T-1150)
- 50-100 pozvanih korisnika

### Stage 3: Soft Launch (Public) — 2 sedmice
- GDPR compliance (T-1400, T-1401, T-1455, T-1456)
- Admin bulk moderation (T-1500, T-1550)
- Cookie consent banner
- Ograničeni marketing

### Stage 4: Full Launch — Ongoing
- Sve faze WORLD_CLASS_ROADMAP
- Full marketing push
- 24/7 monitoring

---

## Test Status

| Suite | Testovi | Assertions | Status |
|-------|---------|------------|--------|
| Unit/Feature (Pest) | 139 | 402 | ✅ Sve prolaze |
| Vue (Vitest) | Framework ready | — | ⚠️ Pending UI |
| E2E (Playwright) | Framework ready | — | ⚠️ Pending UI |
| Load (k6) | Framework ready | — | ⚠️ Pending live env |

---

## Risk Assessment

| Risk | Impact | Vjerovatnoća | Mitigacija |
|------|--------|-------------|------------|
| DB performance bez indices | HIGH | HIGH | T-1504 — odmah |
| Shill bidding bez detection-a | HIGH | MEDIUM | T-1100, T-1101 — M1 |
| GDPR neusklađenost | HIGH | HIGH | T-1400, T-1401 — pre launcha |
| Wrong-user notifications | HIGH | ~~HIGH~~ **FIXED** | BidPlaced refaktor završen |
| Payment double-credit | HIGH | ~~MEDIUM~~ **FIXED** | initiateDeposit flow ispravljen |
| Frontend delays | MEDIUM | MEDIUM | Codex hookup taskovi |
| Security vulnerabilities | HIGH | MEDIUM | Zakazati penetration testing |

---

**Zadnje ažuriranje:** March 2026 (post-implementation audit)
**Naredni review:** Nakon Stage 1 (Alpha) completion
