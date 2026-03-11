# Aukcijska Platforma - Task Breakdown

## Strategy

**Framework:** Laravel 11.x + Livewire v3 + Vue.js 3
**Database:** PostgreSQL 16+ + Redis
**Real-time:** Laravel Reverb + Echo
**Search:** Meilisearch
**Each task is self-contained** — designed for independent AI agent pickup.

### AI Agent Assignment

Taskovi su podijeljeni na **3 AI agenta** koji rade paralelno:

| Agent | Fokus | Razlog |
|-------|-------|--------|
| **🟣 Claude** | Arhitektura, biznis logika, Bidding Engine, Trust & Safety | Najbolji za kompleksnu logiku, transakcije, security |
| **🟢 Codex** | Frontend, UI komponente, Livewire/Vue, Blade layouts | Brz u generisanju UI koda, šablona, komponenti |
| **🔵 Qwen** | DevOps, testovi, API, integracije, search, cron jobs | Dobar za infrastrukturu, integracije, skripte |

### Testing Rules (OBAVEZNO — NEMA IZUZETAKA)

> **Svaka nova feature, bugfix ili promjena koda MORA imati odgovarajući test.**
> **PR bez testa se NE MERGEA. CI pipeline odbija PR ako testovi ne prođu.**

| Tip | Tool | Kada | Coverage |
|-----|------|------|----------|
| **Unit Tests** | Pest PHP | Svaki service, model, helper | BiddingService: 100%, EscrowService: 100%, Overall: 80%+ |
| **Feature Tests** | Pest PHP | Svaki API endpoint, svaki Livewire component | 50+ feature tests |
| **E2E Tests** | Playwright | Svaki kritični user flow | 15+ scenarija |
| **Vue Component Tests** | Vitest | BiddingConsole, AuctionTimer | Sve Vue komponente |
| **Load Tests** | k6 | Bidding concurrency, search stress | p99 < 500ms za bidding |

**Agent pravilo:** Svaki agent koji napravi feature MORA napraviti i test za taj feature u istom PR-u.

### Launch Strategy: Staged Rollout
- Platforma se lansira u sekcijama, ne sve odjednom
- Admin može toggle-ovati feature-e: **Active** / **Coming Soon** / **Hidden**
- 3 seller tiera: **Free** (5 aukcija) → **Premium** (50 aukcija, 29 BAM/mj) → **Storefront** (neograničeno, 99 BAM/mj)
- Komisija: 8% (Free) → 5% (Premium) → 3% (Storefront)
- See `docs/arhitektura/14-feature-flags-and-tiers.md`

---

## PHASE 0: Architecture & Documentation ✅ DONE

| ID | Task | Agent | Status | Output |
|----|------|-------|--------|--------|
| T-001 | System overview document | 🟣 Claude | ✅ DONE | `docs/arhitektura/01-system-overview.md` |
| T-002 | Tech stack document | 🟣 Claude | ✅ DONE | `docs/arhitektura/02-tech-stack.md` |
| T-003 | Database schema document | 🟣 Claude | ✅ DONE | `docs/arhitektura/03-database-schema.md` |
| T-004 | Auth & roles document | 🟣 Claude | ✅ DONE | `docs/arhitektura/04-auth-and-roles.md` |
| T-005 | API design document | 🟣 Claude | ✅ DONE | `docs/arhitektura/05-api-design.md` |
| T-006 | Bidding engine document | 🟣 Claude | ✅ DONE | `docs/arhitektura/06-bidding-engine.md` |
| T-007 | Frontend structure document | 🟣 Claude | ✅ DONE | `docs/arhitektura/07-frontend-structure.md` |
| T-008 | Deployment document | 🟣 Claude | ✅ DONE | `docs/arhitektura/08-deployment.md` |
| T-009 | Activity plan document | 🟣 Claude | ✅ DONE | `docs/arhitektura/09-activity-plan.md` |
| T-010 | Competitive analysis document | 🟣 Claude | ✅ DONE | `docs/arhitektura/10-competitive-analysis.md` |
| T-011 | Trust & safety document | 🟣 Claude | ✅ DONE | `docs/arhitektura/11-trust-and-safety.md` |
| T-012 | Laravel architecture document | 🟣 Claude | ✅ DONE | `docs/arhitektura/12-laravel-architecture.md` |
| T-013 | Feature flags & tiers document | 🟣 Claude | ✅ DONE | `docs/arhitektura/14-feature-flags-and-tiers.md` |
| T-014 | Growth & engagement document | 🟣 Claude | ✅ DONE | `docs/arhitektura/16-growth-engagement-strategy.md` |
| T-015 | UI design guidelines document | 🟣 Claude | ✅ DONE | `docs/arhitektura/18-ui-design-guidelines.md` |
| T-016 | Task breakdown (this file) | 🟣 Claude | ✅ DONE | `docs/TASKS.md` |
| T-017 | Security architecture document | 🟣 Claude | ✅ DONE | `docs/arhitektura/13-security-architecture.md` |
| T-018 | Monitoring & observability document | 🟣 Claude | ✅ DONE | `docs/arhitektura/15-monitoring-observability.md` |
| T-019 | Testing strategy document | 🟣 Claude | ✅ DONE | `docs/arhitektura/17-testing-strategy.md` |
| T-020 | Scaling & infrastructure document | 🟣 Claude | ✅ DONE | `docs/arhitektura/19-scaling-infrastructure.md` |
| T-021 | Payment integration document | 🟣 Claude | ✅ DONE | `docs/arhitektura/20-payment-integration.md` |
| T-022 | Main README.md | 🟣 Claude | ✅ DONE | `README.md` |

---

## PHASE 1: Project Scaffolding & DevOps

### T-100: Initialize Laravel Project 🔵 Qwen
- **Depends on:** Nothing
- **Agent:** 🔵 Qwen
- **Scope:** Create Laravel 11 project sa svim zavisnostima
- **Acceptance criteria:**
  - `composer create-project laravel/laravel` u root direktoriju
  - PHP 8.3+ requirement
  - Tailwind CSS 4 + Vite configured
  - Livewire v3 installed and configured
  - Laravel Reverb installed
  - Laravel Horizon installed
  - Laravel Scout + Meilisearch driver installed
  - Spatie Laravel-Permission installed
  - Spatie Laravel-Feature-Flags installed
  - ESLint + Pint (PHP linter) configured
  - `.gitignore` updated
  - `.env.example` sa svim varijablama
- **Output:** Working Laravel project, `composer.json`, `package.json`, `vite.config.js`
- **Test:** `php artisan serve` + `npm run dev` rade

### T-101: Docker Compose Setup 🔵 Qwen
- **Depends on:** Nothing
- **Agent:** 🔵 Qwen
- **Scope:** Docker setup za lokalni i produkcioni environment
- **Acceptance criteria:**
  - `docker-compose.yml` — dev environment (Laravel Sail-based)
    - Services: PHP-FPM, Nginx, PostgreSQL 16, Redis 7, Meilisearch, Reverb
    - Volume mounts za persistence
  - `docker-compose.prod.yml` — production
    - Nginx sa SSL termination
    - Horizon worker
    - Scheduler worker
    - Reverb WebSocket server
  - `Dockerfile` za PHP application
  - `nginx/default.conf` — Nginx konfiguracija sa WebSocket proxy
  - `.env.docker` sa svim potrebnim tajnama
  - `scripts/setup-local.sh` — one-command setup
- **Output:** `docker-compose*.yml`, `Dockerfile`, `nginx/`, `scripts/`
- **Test:** `docker compose up -d` → svi servisi healthy

### T-102: Database Migrations 🟣 Claude
- **Depends on:** T-100
- **Agent:** 🟣 Claude
- **Scope:** Convert schema doc to executable Laravel migrations
- **Input:** `docs/arhitektura/03-database-schema.md` (22+ tabela)
- **Acceptance criteria:**
  - `database/migrations/` sa ordered migration fajlovima:
    - `001_create_user_profiles_table.php`
    - `002_create_user_verifications_table.php`
    - `003_create_categories_table.php`
    - `004_create_auctions_table.php`
    - `005_create_auction_images_table.php`
    - `006_create_bids_table.php`
    - `007_create_proxy_bids_table.php`
    - `008_create_bid_increments_table.php`
    - `009_create_auction_extensions_table.php`
    - `010_create_auction_watchers_table.php`
    - `011_create_wallets_table.php`
    - `012_create_wallet_transactions_table.php`
    - `013_create_payments_table.php`
    - `014_create_orders_table.php`
    - `015_create_shipments_table.php`
    - `016_create_user_ratings_table.php`
    - `017_create_disputes_table.php`
    - `018_create_messages_table.php`
    - `019_create_notifications_table.php`
    - `020_create_feature_flags_table.php`
    - `021_create_admin_logs_table.php`
    - `022_create_seller_subscriptions_table.php`
  - Svi indexi iz schema dokumenta
  - Foreign key constraints
  - Enum validacije via CHECK constraints
- **Output:** `database/migrations/*.php`
- **Test:** `php artisan migrate` — no errors

### T-103: Seed Data 🟣 Claude
- **Depends on:** T-102
- **Agent:** 🟣 Claude
- **Scope:** Create seed data za development/demo
- **Acceptance criteria:**
  - `database/seeders/`:
    - `RoleSeeder.php` — 5 rola + permissions (Spatie)
    - `CategorySeeder.php` — 15+ kategorija (Elektronika, Auto, Kolekcionarstvo, itd.)
    - `BidIncrementSeeder.php` — 7 razina bid incrementa
    - `UserSeeder.php` — 1 admin, 2 moderatora, 5 sellera (2 verified), 10 buyera
    - `AuctionSeeder.php` — 30+ aukcija (mixed statusi, tipovi)
    - `BidSeeder.php` — 100+ bidova na aktivnim aukcijama
    - `FeatureFlagSeeder.php` — 11 predefinisanih flagova
  - Sve na BHS jeziku
  - Realistični podaci (Samsung, iPhone, vintage satovi, itd.)
- **Output:** `database/seeders/`
- **Test:** `php artisan db:seed` — no errors

### T-104: GitHub Actions CI/CD 🔵 Qwen
- **Depends on:** T-100
- **Agent:** 🔵 Qwen
- **Scope:** CI/CD pipeline
- **Acceptance criteria:**
  - `.github/workflows/ci.yml` — runs on every PR:
    - PHP lint (Pint)
    - JS lint (ESLint)
    - PHPStan (static analysis)
    - `php artisan test` (Pest/PHPUnit)
    - `npm run build` — frontend build check
  - `.github/workflows/deploy.yml` — on merge to main:
    - Build Docker images
    - SSH deploy to server
    - `php artisan migrate --force`
    - Cache clear + optimize
    - Queue restart + Reverb restart
  - `.github/workflows/db-check.yml` — validate migrations
- **Output:** `.github/workflows/*.yml`

### T-105: Environment & Config 🔵 Qwen
- **Depends on:** T-100, T-101
- **Agent:** 🔵 Qwen
- **Scope:** Environment files, config, README
- **Acceptance criteria:**
  - `.env.example` — fully documented template
  - `.env.docker` — Docker Compose environment
  - `config/auction.php` — custom auction config (sniping window, extension time, etc.)
  - `config/escrow.php` — escrow config (payment deadline, auto-release days, etc.)
  - `config/tiers.php` — tier limits and commissions
  - `README.md` — how to setup locally
- **Output:** Config files, README

---

## PHASE 2: Authentication & Authorization

### T-200: Auth Setup (Breeze + Spatie) 🟣 Claude
- **Depends on:** T-100, T-102
- **Agent:** 🟣 Claude
- **Scope:** Complete auth system with roles
- **Acceptance criteria:**
  - Laravel Breeze installed (Blade + Livewire stack)
  - Spatie roles & permissions configured:
    - 5 roles: `buyer`, `seller`, `verified_seller`, `moderator`, `super_admin`
    - Granularne permissions (30+ permissions from doc 04)
  - Registration: izbor buyer/seller tipa
  - Post-registration: onboarding wizard
    - Buyer: preferirane kategorije, lokacija
    - Seller: KYC prompt, wallet setup
  - Email verification obavezan
  - MFA setup za seller role (Fortify TOTP)
  - Rate limiting na auth endpointima
- **Output:** Auth controllers, middleware, Spatie config, registration flow

### T-201: Auth Pages (Frontend) 🟢 Codex
- **Depends on:** T-200
- **Agent:** 🟢 Codex
- **Scope:** Login, register, forgot password, verify email stranice
- **Acceptance criteria:**
  - `resources/views/auth/login.blade.php` — email/password, Google OAuth button
  - `resources/views/auth/register.blade.php` — buyer/seller izbor, form
  - `resources/views/auth/forgot-password.blade.php` — email input
  - `resources/views/auth/reset-password.blade.php` — new password form
  - `resources/views/auth/verify-email.blade.php` — verification page
  - `resources/views/auth/two-factor-challenge.blade.php` — MFA input
  - Tailwind styling, mobile responsive
  - Form validation sa error messages (BHS)
  - Redirect after login: buyer → /dashboard, seller → /seller/dashboard, admin → /admin
- **Output:** `resources/views/auth/*.blade.php`

### T-202: Middleware 🟣 Claude
- **Depends on:** T-200
- **Agent:** 🟣 Claude
- **Scope:** Route protection middleware
- **Acceptance criteria:**
  - `EnsureKycVerified` — blokira seller akcije bez KYC
  - `EnsureSellerRole` — blokira ne-sellere od kreiranja aukcija
  - `ThrottleBids` — rate limit: 10 bid/min po korisniku
  - `EnsureAuctionActive` — provjera statusa aukcije
  - Route groups sa middleware:
    - `/seller/*` → auth + seller/verified_seller
    - `/admin/*` → auth + moderator/super_admin
    - `/api/v1/*` → sanctum + appropriate permissions
- **Output:** `app/Http/Middleware/*.php`, route definitions

---

## PHASE 3: Auction Engine (Core) — 🟣 Claude Primary

### T-300: Eloquent Models 🟣 Claude
- **Depends on:** T-102
- **Agent:** 🟣 Claude
- **Scope:** All Eloquent models with relationships, scopes, casts
- **Acceptance criteria:**
  - Models za svih 22+ tabela
  - `Auction` model:
    - Relations: seller, category, bids, images, watchers, extensions, proxyBids
    - Scopes: active(), ending_soon(), featured(), inCategory(), search()
    - Casts: status → AuctionStatus enum, type → AuctionType enum
    - Accessors: time_remaining, minimum_bid, is_ending_soon
  - `Bid` model:
    - Relations: auction, user
    - Scopes: forAuction(), byUser(), winning()
  - `User` model:
    - Relations: profile, auctions, bids, wallet, ratings, orders, watchlist
    - Accessors: trust_score, tier, commission_rate, can_create_auction
  - `Wallet`, `Order`, `Payment`, `Shipment`, `ProxyBid` — svi sa relacijama
  - `Category` — self-referencing (parent_id), nested set ili adjacency list
- **Output:** `app/Models/*.php`

### T-301: BiddingService 🟣 Claude
- **Depends on:** T-300
- **Agent:** 🟣 Claude
- **Scope:** Core bidding logic — najkritičniji servis na platformi
- **Input:** `docs/arhitektura/06-bidding-engine.md`
- **Acceptance criteria:**
  - `app/Services/BiddingService.php`:
    - `placeBid()` — atomic bid placement sa Redis lock + DB transaction
    - `processProxyBids()` — auto-bid do max iznosa za aktivne proxy bidove
    - `validateBid()` — sve validacije (min bid, not own auction, auction active, etc.)
  - `app/Services/BidIncrementService.php`:
    - `getMinimumBid()` — dinamički minimum na osnovu trenutne cijene
    - `getIncrement()` — lookup iz bid_increments tabele
  - `app/Services/AuctionService.php`:
    - `checkAntiSniping()` — produženje aukcije ako je bid u zadnje 2 min
    - `endAuction()` — završetak, određivanje pobjednika, kreiranje Order-a
    - `cancelAuction()` — otkazivanje (samo ako nema bidova)
    - `createAuction()` — kreiranje sa validacijom tier limita
  - Concurrency zaštita: Redis lock → DB transaction → Advisory lock fallback
  - Custom exceptions: BidTooLowException, AuctionNotActiveException, CannotBidOwnAuctionException
- **Output:** `app/Services/BiddingService.php`, `BidIncrementService.php`, `AuctionService.php`
- **Test:** Unit tests za sve edge cases (concurrent bids, proxy, anti-sniping)

### T-302: Auction State Machine 🟣 Claude
- **Depends on:** T-300
- **Agent:** 🟣 Claude
- **Scope:** Auction lifecycle management
- **Acceptance criteria:**
  - `app/Enums/AuctionStatus.php` — Draft, Active, Finished, Sold, Cancelled
  - `app/Enums/AuctionType.php` — Standard, BuyNow, Dutch
  - State transitions sa validacijom (canTransitionTo)
  - `EndExpiredAuctions` artisan command — runs every minute
  - `EndAuctionJob` — queued job za svaku aukciju koja ističe:
    - Odredi pobjednika (highest bid)
    - Provjeri reserve price
    - Kreiraj Order record
    - Zamrzni escrow sredstva
    - Pošalji notifikacije (winner, seller, watchers)
    - Broadcast AuctionEnded event
  - Scheduler registration u `routes/console.php`
- **Output:** Enums, commands, jobs

### T-303: Events & Listeners 🟣 Claude
- **Depends on:** T-301
- **Agent:** 🟣 Claude
- **Scope:** Domain events i event handlers
- **Acceptance criteria:**
  - Events:
    - `BidPlaced` — auction, bid, user data
    - `AuctionExtended` — auction, new end time
    - `AuctionEnded` — auction, winner, final price
    - `AuctionWon` — winner user, auction, amount
    - `OrderCreated` — order, buyer, seller
    - `PaymentReceived` — order, payment
    - `ItemShipped` — order, shipment, tracking
    - `DisputeOpened` — dispute, order
  - Listeners:
    - `BroadcastBidUpdate` — WebSocket push (cijenu svima)
    - `SendOutbidNotification` — email + push prethodnom lideru
    - `CreateOrderOnAuctionEnd` — kreira Order + escrow hold
    - `NotifyWatchers` — obavijesti sve koji prate aukciju
    - `UpdateTrustScore` — recalculate after rating
  - Event → Listener mapping u `EventServiceProvider`
- **Output:** `app/Events/*.php`, `app/Listeners/*.php`

### T-304: WebSocket Channels 🔵 Qwen
- **Depends on:** T-100, T-303
- **Agent:** 🔵 Qwen
- **Scope:** Laravel Reverb konfiguracija + broadcast kanali
- **Acceptance criteria:**
  - `routes/channels.php`:
    - `auction.{id}` — public channel za live auction updates
    - `user.{id}` — private channel za personalne notifikacije
  - `config/broadcasting.php` — Reverb konfiguracija
  - `config/reverb.php` — WebSocket server config
  - Frontend Echo setup (`resources/js/echo.js`)
  - Broadcasting events korektno serializovani
  - Test: bid placement → WebSocket event primljen na frontendu
- **Output:** Channel definitions, Echo config, broadcasting setup

---

## PHASE 4: Frontend — Buyer Experience — 🟢 Codex Primary

### T-400: Base Layouts 🟢 Codex
- **Depends on:** T-100
- **Agent:** 🟢 Codex
- **Scope:** Blade layouts za sve sekcije
- **Input:** `docs/arhitektura/18-ui-design-guidelines.md`
- **Acceptance criteria:**
  - `resources/views/layouts/guest.blade.php` — landing, auth stranice
    - Header sa logom, navigacija, mobile hamburger
    - Footer sa linkovima
  - `resources/views/layouts/app.blade.php` — authenticated user layout
    - Top nav: logo, search bar, notifications bell, user menu
    - Sidebar: Dashboard, Aukcije, Watchlist, Poruke, Wallet, Profil
    - Mobile: hamburger → slide-out nav
  - `resources/views/layouts/admin.blade.php` — admin panel
    - Sidebar: Dashboard, Aukcije, Korisnici, Kategorije, Sporovi, Statistike, Settings
  - `resources/views/layouts/seller.blade.php` — seller panel
    - Sidebar: Dashboard, Moje Aukcije, Narudžbe, Wallet, Statistike, Profil
  - Svi layouts koriste Tailwind CSS, "Trust Blue" paletu
  - Responsive, mobile-first
  - Dark mode support (optional, Phase 2)
- **Output:** `resources/views/layouts/*.blade.php`

### T-401: UI Components Library 🟢 Codex
- **Depends on:** T-400
- **Agent:** 🟢 Codex
- **Scope:** Reusable Blade/Livewire komponente
- **Input:** `docs/arhitektura/18-ui-design-guidelines.md`
- **Acceptance criteria:**
  - `resources/views/components/`:
    - `button.blade.php` — primary, secondary, danger, success, ghost varijante
    - `input.blade.php` — text, email, password, textarea sa validacijom
    - `select.blade.php` — dropdown sa search opcijom
    - `modal.blade.php` — dialog sa backdrop
    - `card.blade.php` — white, rounded-xl, shadow-sm
    - `badge.blade.php` — status (Featured, Verified, Ending Soon, New)
    - `alert.blade.php` — info, success, warning, error
    - `price-display.blade.php` — formatted cijena sa valutom
    - `countdown-timer.blade.php` — Alpine.js powered countdown
    - `image-gallery.blade.php` — lightbox za aukcijske slike
    - `data-table.blade.php` — sortable, filterable tabela
    - `pagination.blade.php` — styled pagination
    - `avatar.blade.php` — user avatar sa fallback inicijala
    - `toast.blade.php` — notification toast (Alpine.js)
    - `progress-bar.blade.php` — za countdown i loading
  - Sve komponente koriste Inter font, Trust Blue paletu
  - Rounded-lg corners, soft shadows
  - Accessible (ARIA, focus states, contrast)
  - Mobile-first, touch-friendly (min 44px tap targets)
- **Output:** `resources/views/components/*.blade.php`

### T-402: Landing Page 🟢 Codex
- **Depends on:** T-400, T-401
- **Agent:** 🟢 Codex
- **Scope:** Homepage — SEO optimizirano
- **Acceptance criteria:**
  - `resources/views/pages/home.blade.php`
  - Hero sekcija: headline, tagline, CTA "Počni Licitirati" + "Prodaj Odmah"
  - Featured aukcije grid (ending soon)
  - Kategorije grid sa ikonama
  - Kako funkcioniše (3 koraka: Registruj se → Licitiraj → Pobijedi)
  - Trust sekcija: Escrow zaštita, Verified sellers, Rating sistem
  - Statistike: X aktivnih aukcija, X registriranih korisnika
  - SEO meta tagovi, OG tagovi
  - Mobile responsive
- **Output:** Homepage + related partials

### T-403: Auction Listing Page 🟢 Codex
- **Depends on:** T-400, T-401
- **Agent:** 🟢 Codex
- **Scope:** Pretraga i listing aukcija
- **Acceptance criteria:**
  - `app/Livewire/AuctionSearch.php` + `resources/views/livewire/auction-search.blade.php`
  - Sidebar filteri:
    - Kategorija (tree dropdown)
    - Cijena (range slider)
    - Stanje (novo, korišteno, itd.)
    - Lokacija (grad/država)
    - Tip aukcije (standard, buy now)
    - Samo sa slikom
  - Sort: Ending soon, Newest, Price low→high, Price high→low, Most bids
  - Grid/List toggle prikaz
  - `AuctionCard` Livewire komponenta:
    - Slika, naslov, kategorija
    - Trenutna cijena (real-time via Echo)
    - Countdown timer (Alpine.js)
    - Broj bidova, watchera, grad
    - [Licitiraj] + [♡ Watchlist] buttons
  - Pagination (infinite scroll ili numbered)
  - Mobile: stacked cards, bottom sheet za filtere
- **Output:** Livewire components, Blade views

### T-404: Auction Detail Page 🟢 Codex
- **Depends on:** T-401, T-403
- **Agent:** 🟢 Codex
- **Scope:** Pojedinačna aukcija — core user experience
- **Acceptance criteria:**
  - `resources/views/pages/auctions/show.blade.php`
  - Image gallery sa lightbox (swipe na mobilnom)
  - Naslov, opis, stanje, kategorija breadcrumb
  - Bidding sekcija (desno ili sticky bottom na mobilnom):
    - Trenutna cijena (real-time)
    - Countdown timer (real-time, ažurira se na anti-sniping extension)
    - Input za bid amount + [LICITIRAJ] button
    - Checkbox: "Proxy bid" + max amount input
    - Minimalni bid prikazan
    - Bid historija (collapsible lista)
  - Seller info card: avatar, ime, rating, badge-ovi
  - Shipping info: dostupne opcije, cijena
  - [♡ Watchlist] + [✉ Kontaktiraj prodavca]
  - Related auctions grid (bottom)
  - SEO: dynamic meta tags, structured data (Product schema)
  - Mobile: sticky bottom bar sa cijenom + [Licitiraj] button
- **Output:** Auction detail page + components

### T-405: BiddingConsole (Vue.js) 🟢 Codex
- **Depends on:** T-304, T-404
- **Agent:** 🟢 Codex
- **Scope:** Vue.js komponenta za bidding — kompleksna real-time interakcija
- **Acceptance criteria:**
  - `resources/vue/BiddingConsole.vue`:
    - Prikazuje current price, minimum bid, countdown
    - Input za bid amount sa validacijom (client-side)
    - Proxy bid toggle + max amount
    - Submit → POST /auctions/{id}/bid → show result
    - Echo listener za BidPlaced → auto-update cijena
    - Echo listener za AuctionExtended → update timer
    - Echo listener za AuctionEnded → disable bidding, show result
    - Outbid alert (ako je korisnik bio lider pa ga neko nadlicitira)
    - Loading states, error handling
    - Konfeti animacija na winning bid
  - `resources/vue/AuctionTimer.vue`:
    - NTP sync sa serverom
    - Color change: zeleno > 1h, narandžasto < 1h, crveno < 5min
    - Pulsing animation u zadnje 2 minute
- **Output:** Vue.js components

### T-406: Buyer Dashboard 🟢 Codex
- **Depends on:** T-400
- **Agent:** 🟢 Codex
- **Scope:** Dashboard za kupce
- **Acceptance criteria:**
  - `resources/views/pages/dashboard.blade.php`
  - Stat cards: Active bids, Won auctions, Watchlist count, Wallet balance
  - Aktivni bidovi (aukcije na kojima sam lider ili sam licitirao)
  - Watchlist (ending soon first)
  - Nedavno dobijene aukcije (status: need to pay)
  - Quick links: Browse auctions, My orders, Messages
  - Responsive grid
- **Output:** Dashboard page

### T-407: Watchlist 🟢 Codex
- **Depends on:** T-400, T-403
- **Agent:** 🟢 Codex
- **Scope:** Korisnikova lista praćenih aukcija
- **Acceptance criteria:**
  - `app/Livewire/Watchlist.php`
  - Grid sa AuctionCard komponentama
  - Filter: Active, Ending Today, Ended
  - Sort: Ending soon, Recently added
  - Remove from watchlist (inline)
  - Real-time update cijena (Echo)
  - Empty state: "Nemaš praćenih aukcija. Započni pretragu."
- **Output:** Watchlist Livewire component

---

## PHASE 5: Frontend — Seller Experience — 🟢 Codex Primary

### T-500: Seller Dashboard 🟢 Codex
- **Depends on:** T-400
- **Agent:** 🟢 Codex
- **Scope:** Dashboard za prodavce
- **Acceptance criteria:**
  - `resources/views/pages/seller/dashboard.blade.php`
  - Stat cards: Active auctions, Total sales, Wallet balance, Average rating
  - Aktivne aukcije sa live bid count-om
  - Narudžbe koje treba poslati
  - Nedavno završene aukcije (rezultati)
  - Revenue chart (last 30 dana)
  - Quick actions: Kreiraj aukciju, Pogledaj narudžbe
  - Tier status + upgrade prompt (ako je Free)
- **Output:** Seller dashboard

### T-501: Create/Edit Auction Form 🟢 Codex
- **Depends on:** T-500
- **Agent:** 🟢 Codex
- **Scope:** Multi-step forma za kreiranje aukcije
- **Acceptance criteria:**
  - `app/Livewire/CreateAuction.php` + view
  - Step 1: Naslov, kategorija (tree select), opis (rich text), stanje
  - Step 2: Slike (drag & drop upload, max 10, reorder)
  - Step 3: Cijena (start, reserve, buy now), valuta, tip aukcije
  - Step 4: Shipping (opcije, cijena, lokacija)
  - Step 5: Trajanje (3, 5, 7, 10 dana), anti-sniping toggle
  - Preview mode prije publish
  - Draft save (auto-save)
  - Edit mode (samo draft aukcije ili ograničeno za aktivne)
  - Validation na svakom koraku
  - Tier limit check (5 za Free, 50 za Premium, unlimited za Storefront)
  - Mobile friendly multi-step
- **Output:** Create/edit auction Livewire component

### T-502: Seller Orders Management 🟢 Codex
- **Depends on:** T-500
- **Agent:** 🟢 Codex
- **Scope:** Upravljanje narudžbama za sellera
- **Acceptance criteria:**
  - `resources/views/pages/seller/orders/index.blade.php` — lista narudžbi
  - `resources/views/pages/seller/orders/show.blade.php` — detalj narudžbe
  - Status tabs: Awaiting payment, Ready to ship, Shipped, Completed, Disputed
  - Narudžba detalj:
    - Buyer info, adresa dostave
    - Artikal, cijena, komisija
    - [Označi kao poslano] → tracking number input
    - Automatski tovarni list (ako je courier integriran)
  - Timeline: Bid won → Payment → Shipped → Delivered → Completed
  - Export to CSV
- **Output:** Seller orders pages

### T-503: Wallet Management (Frontend) 🟢 Codex
- **Depends on:** T-500
- **Agent:** 🟢 Codex
- **Scope:** Wallet UI za kupce i prodavce
- **Acceptance criteria:**
  - `resources/views/pages/wallet/index.blade.php`
  - Balance display (available, in escrow, total)
  - Transaction historija (sortable, filterable)
  - Deposit form (amount + payment method)
  - Withdrawal form (bank account details + amount)
  - Transaction types sa ikonama: deposit ↑, withdrawal ↓, escrow hold 🔒, release 🔓, commission 💰
  - Responsive tabela
- **Output:** Wallet pages

---

## PHASE 6: Trust & Safety — 🟣 Claude Primary

### T-600: EscrowService 🟣 Claude
- **Depends on:** T-301, T-302
- **Agent:** 🟣 Claude
- **Scope:** Escrow sistem za zaštitu transakcija
- **Input:** `docs/arhitektura/11-trust-and-safety.md`
- **Acceptance criteria:**
  - `app/Services/EscrowService.php`:
    - `holdFunds(Order)` — zamrzne sredstva iz buyer walleta
    - `releaseFunds(Order)` — prebaci prodavcu (minus komisija)
    - `refundBuyer(Order, float $amount)` — full ili partial refund
    - `autoRelease()` — auto-release 14 dana nakon dostave bez dispute
  - `app/Services/WalletService.php`:
    - `deposit(User, float, string $gateway)` — uplata
    - `withdraw(User, float)` — isplata
    - `getBalance(User)` — available + escrow hold
    - Atomic operations (DB transaction za svaku wallet operaciju)
  - `EscrowAutoRelease` artisan command — hourly scheduler
  - Custom exceptions: InsufficientFundsException, WalletFrozenException
- **Output:** EscrowService, WalletService, commands

### T-601: KYC Verification 🟣 Claude
- **Depends on:** T-200, T-300
- **Agent:** 🟣 Claude
- **Scope:** Know Your Customer verifikacija
- **Acceptance criteria:**
  - `app/Services/KycService.php`:
    - `sendSmsOtp(User, string $phone)` — via Infobip/Twilio
    - `verifySmsOtp(User, string $code)` — validate OTP
    - `submitDocument(User, UploadedFile)` — upload ID dokumenta
    - `reviewDocument(User, string $status, string $notes)` — admin review
    - `getVerificationLevel(User)` — 0, 1, 2, 3
  - Controller za KYC flow
  - Email notifikacije za status promjene
  - Rate limiting za SMS OTP (3 pokušaja / sat)
- **Output:** KycService, controller, mail templates

### T-602: Rating Service 🟣 Claude
- **Depends on:** T-300
- **Agent:** 🟣 Claude
- **Scope:** Dvosmjerno ocjenjivanje + trust score
- **Acceptance criteria:**
  - `app/Services/RatingService.php`:
    - `rateUser(Order, User $rater, int $score, string $comment)` — kreiraj rating
    - `calculateTrustScore(User)` — formula iz doc 11
    - `canRate(Order, User)` — validacija (samo after completed, jednom)
  - Trust score formula:
    - `(avg_rating × 0.6) + (transaction_bonus × 0.3) + (verification_bonus × 0.1)`
  - Trust badges automatski dodijeljeni:
    - Verified Seller: KYC level 3
    - Top Rated: score > 4.5 + 50 transakcija
    - Power Seller: 100+ prodaja u 6 mjeseci
  - Cached trust score (Redis, update na novi rating)
- **Output:** RatingService, trust score calculation

### T-603: Dispute Management 🟣 Claude
- **Depends on:** T-600
- **Agent:** 🟣 Claude
- **Scope:** Dispute flow za admin resolution
- **Acceptance criteria:**
  - `app/Services/DisputeService.php`:
    - `openDispute(Order, User, string $reason, string $description)` — otvori
    - `addEvidence(Dispute, User, files)` — dodaj dokaze
    - `resolve(Dispute, string $resolution, User $resolver)` — admin rješava
    - Auto-freeze escrow na dispute open
  - Dispute razlozi: item_not_received, not_as_described, damaged, counterfeit, seller_cancelled
  - Rokovi: seller 48h da odgovori, admin 5 dana da riješi
  - Email notifikacije na svaki status change
  - Escalation: auto-escalate ako seller ne odgovori u roku
- **Output:** DisputeService, notifications

---

## PHASE 7: Payment & Shipping Integrations — 🔵 Qwen Primary

### T-700: Payment Gateway Integration 🔵 Qwen
- **Depends on:** T-600
- **Agent:** 🔵 Qwen
- **Scope:** Integracija sa platnim procesorima
- **Acceptance criteria:**
  - `app/Services/PaymentService.php`:
    - `processPayment(Order, string $gateway, array $data)` — unified interface
    - `refund(Payment)` — refund via gateway
    - `getAvailableGateways()` — return available gateways per country
  - Gateway adapteri (Strategy pattern):
    - `app/Services/Gateways/StripeGateway.php` — Stripe Checkout
    - `app/Services/Gateways/MonriGateway.php` — Monri (BiH)
    - `app/Services/Gateways/CorvusPayGateway.php` — CorvusPay (HR)
    - `app/Services/Gateways/WalletGateway.php` — interni wallet
  - Webhook handlers za payment confirmations
  - Payment logging (svaka transakcija u payments tabeli)
  - Config: `config/payment.php` sa gateway credentials
- **Output:** PaymentService, gateway adapters, webhook routes

### T-701: Shipping / Logistics Integration 🔵 Qwen
- **Depends on:** T-300
- **Agent:** 🔵 Qwen
- **Scope:** Integracija sa regionalnim kuririma
- **Acceptance criteria:**
  - `app/Services/ShippingService.php`:
    - `createWaybill(Order)` — generira tovarni list
    - `getTrackingInfo(Shipment)` — status pošiljke
    - `estimateShipping(string $from, string $to, float $weight)` — cijena
  - Courier adapteri:
    - `app/Services/Couriers/EuroExpressCourier.php`
    - `app/Services/Couriers/PostExpressCourier.php`
    - `app/Services/Couriers/BhPostaCourier.php`
  - Tracking webhook za auto-update statusa
  - PDF waybill generiranje (za print)
  - Config: `config/shipping.php`
- **Output:** ShippingService, courier adapters

### T-702: Meilisearch Setup & Indexing 🔵 Qwen
- **Depends on:** T-100, T-300
- **Agent:** 🔵 Qwen
- **Scope:** Full-text search sa Meilisearch
- **Acceptance criteria:**
  - Laravel Scout konfiguracija za Meilisearch
  - `Auction` model searchable:
    - Searchable fields: title, description, category name, city
    - Filterable: status, category_id, type, condition, price range, country
    - Sortable: ends_at, current_price, created_at, bids_count
  - Typo tolerance configured (regionalni dijalekti)
  - Synonyms: "mobitel" = "telefon" = "mobilni", "auto" = "automobil" = "vozilo"
  - Faceted search za filter counts
  - Reindex command + scheduler (daily at 03:00)
  - Index creation i settings u migration/seeder
- **Output:** Scout config, searchable models, Meilisearch settings

### T-703: Email & Notification Service 🔵 Qwen
- **Depends on:** T-303
- **Agent:** 🔵 Qwen
- **Scope:** Transactional emails + push notifications
- **Acceptance criteria:**
  - Laravel Notifications:
    - `OutbidNotification` — email + database + push
    - `AuctionWonNotification` — email + database
    - `AuctionEndedNotification` — email (seller)
    - `PaymentReceivedNotification` — email (seller)
    - `ItemShippedNotification` — email + push (buyer)
    - `DisputeNotification` — email (both parties)
    - `KycStatusNotification` — email (seller)
    - `PaymentReminderNotification` — email (buyer, 3 dana)
    - `ShippingReminderNotification` — email (seller, 5 dana)
  - Email templates (Blade markdown) — BHS jezik
  - Firebase push notification setup
  - User notification preferences (per channel, per type)
  - `config/mail.php` — Mailgun/Resend config
- **Output:** `app/Notifications/*.php`, email templates

### T-704: Scheduled Jobs & Cron 🔵 Qwen
- **Depends on:** T-302, T-600, T-703
- **Agent:** 🔵 Qwen
- **Scope:** Svi scheduled taskovi
- **Acceptance criteria:**
  - `routes/console.php` scheduler definitions:
    - `auctions:end-expired` — every minute
    - `escrow:auto-release` — hourly
    - `orders:payment-reminders` — every 6h
    - `orders:shipping-reminders` — every 6h
    - `search:reindex` — daily 03:00
    - `backup:database` — daily 00:00
    - `analytics:daily-stats` — daily 01:00
    - `cleanup:expired-drafts` — daily 02:00 (briše draft aukcije starije od 30 dana)
  - Artisan commands za svaki job
  - Horizon monitoring za sve queued jobs
  - Logging i error handling
- **Output:** Artisan commands, scheduler config

---

## PHASE 8: Admin Panel — 🟢 Codex + 🟣 Claude

### T-800: Admin Dashboard 🟢 Codex
- **Depends on:** T-400
- **Agent:** 🟢 Codex
- **Scope:** Admin overview sa metrikama
- **Acceptance criteria:**
  - `resources/views/pages/admin/dashboard.blade.php`
  - Stat cards: Total users, Active auctions, Today's bids, Revenue (this month)
  - Charts (Chart.js):
    - Registrations trend (last 30 days)
    - Auctions created vs completed
    - Revenue by day
    - Bids per hour (peak analysis)
  - Recent activity log (last 20 events)
  - Quick actions: Moderate auction, Review KYC, Resolve dispute
  - Alerts: pending KYC reviews, open disputes, flagged auctions
- **Output:** Admin dashboard

### T-801: User Management (Admin) 🟢 Codex
- **Depends on:** T-800
- **Agent:** 🟢 Codex
- **Scope:** Admin CRUD za korisnike
- **Acceptance criteria:**
  - `resources/views/pages/admin/users/index.blade.php` — searchable data table
  - `resources/views/pages/admin/users/show.blade.php` — user detail
  - Search by name, email
  - Filter by role, KYC status, tier, active/inactive
  - View: profil, aukcije, bidovi, transakcije, ratinzi, verifikacija
  - Actions: change role, ban/unban, force KYC review, reset password
  - Admin log za svaku akciju
- **Output:** Admin user pages

### T-802: Auction Moderation (Admin) 🟢 Codex
- **Depends on:** T-800
- **Agent:** 🟢 Codex
- **Scope:** Moderacija aukcija
- **Acceptance criteria:**
  - `resources/views/pages/admin/auctions/index.blade.php` — all auctions table
  - Filter: status, reported, featured, category
  - Actions: approve, reject, feature/unfeature, cancel (with reason)
  - Reported auctions queue (priority)
  - Bulk actions: approve all pending, cancel expired
  - Auction detail view sa svim bidovima
- **Output:** Admin auction pages

### T-803: Category Management (Admin) 🟢 Codex
- **Depends on:** T-800
- **Agent:** 🟢 Codex
- **Scope:** CRUD za kategorije
- **Acceptance criteria:**
  - Tree view za hijerarhijske kategorije
  - Drag & drop reorder
  - Create/edit: name, slug, icon, parent
  - Activate/deactivate
  - Auction count per category
- **Output:** Admin category page

### T-804: Dispute Resolution (Admin) 🟢 Codex
- **Depends on:** T-603, T-800
- **Agent:** 🟢 Codex
- **Scope:** UI za rješavanje sporova
- **Acceptance criteria:**
  - `resources/views/pages/admin/disputes/index.blade.php` — dispute queue
  - `resources/views/pages/admin/disputes/show.blade.php` — dispute detail
  - Timeline view: otvaranje → dokazi → odgovor → rješenje
  - Akcije: request more evidence, resolve (buyer/seller), partial refund
  - Order details, payment info, shipping tracking
  - Communication log (admin ↔ parties)
- **Output:** Dispute admin pages

### T-805: Feature Flags Admin 🟣 Claude
- **Depends on:** T-800
- **Agent:** 🟣 Claude
- **Scope:** Feature flag management UI + backend
- **Acceptance criteria:**
  - `app/Livewire/Admin/FeatureFlags.php` + view
  - Lista svih feature flagova sa toggle switch
  - Group by section (Bidding, Payments, Shipping, Growth)
  - Edit: active/inactive, description
  - Create new flag
  - Livewire instant toggle (no page reload)
  - Middleware: `FeatureEnabled('flag_name')` za route zaštitu
  - Blade directive: `@feature('flag_name')` za conditional rendering
- **Output:** Feature flag admin + middleware + directive

### T-806: Admin Statistics & Analytics 🟢 Codex
- **Depends on:** T-800
- **Agent:** 🟢 Codex
- **Scope:** Detaljne statistike za admin
- **Acceptance criteria:**
  - `resources/views/pages/admin/statistics.blade.php`
  - Tab: Users — registrations, active, churn, by role
  - Tab: Auctions — created, completed, cancelled, avg price, avg bids
  - Tab: Revenue — total, by tier, commission breakdown, growth
  - Tab: Trust — disputes, resolution rate, avg rating
  - Date range selector
  - Export to CSV
  - Charts (Chart.js): line, bar, pie
- **Output:** Statistics page

---

## PHASE 9: Testing — 🔵 Qwen Primary

### T-900: Unit Tests — BiddingService 🔵 Qwen
- **Depends on:** T-301
- **Agent:** 🔵 Qwen
- **Scope:** Testovi za najkritičniji servis
- **Acceptance criteria:**
  - `tests/Unit/Services/BiddingServiceTest.php` (Pest):
    - Bid placement (valid)
    - Bid too low (below minimum increment)
    - Bid on own auction (rejected)
    - Bid on inactive auction (rejected)
    - Concurrent bids (only one wins)
    - Proxy bid setup
    - Proxy bid auto-outbid
    - Proxy bid exhausted
    - Anti-sniping extension triggered
    - Anti-sniping not triggered (bid before window)
    - Buy now purchase
  - Minimum 20 test cases
  - 100% coverage na BiddingService
- **Output:** `tests/Unit/Services/BiddingServiceTest.php`

### T-901: Unit Tests — EscrowService 🔵 Qwen
- **Depends on:** T-600
- **Agent:** 🔵 Qwen
- **Scope:** Testovi za escrow sistem
- **Acceptance criteria:**
  - `tests/Unit/Services/EscrowServiceTest.php`:
    - Hold funds (sufficient balance)
    - Hold funds (insufficient balance — fail)
    - Release funds (correct amount minus commission)
    - Refund buyer (full)
    - Refund buyer (partial)
    - Auto-release after 14 days
    - Dispute blocks release
    - Wallet frozen blocks operations
  - Minimum 15 test cases
- **Output:** `tests/Unit/Services/EscrowServiceTest.php`

### T-902: Unit Tests — Other Services 🔵 Qwen
- **Depends on:** T-601, T-602, T-603
- **Agent:** 🔵 Qwen
- **Scope:** Tests za KYC, Rating, Dispute services
- **Acceptance criteria:**
  - `tests/Unit/Services/KycServiceTest.php` — OTP, document review, levels
  - `tests/Unit/Services/RatingServiceTest.php` — rating creation, trust score calculation
  - `tests/Unit/Services/DisputeServiceTest.php` — open, resolve, escalation
  - `tests/Unit/Services/AuctionServiceTest.php` — state transitions, end auction
  - Minimum 10 tests per service
- **Output:** `tests/Unit/Services/*.php`

### T-903: Feature Tests — API Endpoints 🔵 Qwen
- **Depends on:** All Phase 3-7
- **Agent:** 🔵 Qwen
- **Scope:** HTTP tests za sve API endpointe
- **Acceptance criteria:**
  - `tests/Feature/Api/AuctionTest.php` — CRUD, search, filter
  - `tests/Feature/Api/BidTest.php` — place bid, validation errors, auth
  - `tests/Feature/Api/OrderTest.php` — payment, shipping, confirm
  - `tests/Feature/Api/WalletTest.php` — deposit, withdraw, balance
  - `tests/Feature/Api/UserTest.php` — profile, watchlist, ratings
  - Auth tests: login, register, middleware protection
  - Minimum 50 feature tests total
- **Output:** `tests/Feature/Api/*.php`

### T-904: E2E Tests — Playwright (OBAVEZNO) 🔵 Qwen
- **Depends on:** All Phase 4-5
- **Agent:** 🔵 Qwen
- **Scope:** End-to-end browser tests sa Playwright
- **Acceptance criteria:**
  - Playwright setup: `npm init playwright@latest`
  - `playwright.config.ts` — base URL, browsers (Chromium, Firefox, WebKit)
  - `tests/e2e/`:
    - `auth.spec.ts` — Register buyer → verify email → login → logout
    - `auth-seller.spec.ts` — Register seller → KYC → verify
    - `auction-browse.spec.ts` — Search → filter → sort → paginate
    - `auction-detail.spec.ts` — View auction → check images → see bids
    - `bidding.spec.ts` — Place bid → see price update → outbid notification
    - `proxy-bidding.spec.ts` — Set proxy → auto-outbid → win
    - `anti-sniping.spec.ts` — Bid in last 2 min → timer extends
    - `buyer-journey.spec.ts` — Search → bid → win → pay → confirm delivery → rate
    - `seller-journey.spec.ts` — Create auction → get bids → end → ship → complete → rate
    - `admin-moderation.spec.ts` — Moderate auction → review KYC → resolve dispute
    - `watchlist.spec.ts` — Add to watchlist → get notification → remove
    - `wallet.spec.ts` — Deposit → view balance → pay from wallet
    - `messaging.spec.ts` — Send message → receive → reply
    - `mobile.spec.ts` — All critical flows on 375px viewport (iPhone SE)
    - `accessibility.spec.ts` — Axe accessibility checks on key pages
  - **Minimum 15 E2E scenarija**
  - Page Object Model pattern za reusability
  - Screenshots on failure
  - Video recording za debugging
  - CI integration: runs on every PR
- **Output:** `tests/e2e/*.spec.ts`, `playwright.config.ts`
- **Test:** `npx playwright test` — all pass

### T-905: Playwright Setup & Fixtures 🔵 Qwen
- **Depends on:** T-100
- **Agent:** 🔵 Qwen
- **Scope:** Playwright infrastructure, fixtures, helpers
- **Acceptance criteria:**
  - `playwright.config.ts`:
    - baseURL: `http://localhost:8000`
    - Projects: Chromium, Firefox, WebKit, Mobile Chrome, Mobile Safari
    - Retries: 2 on CI, 0 locally
    - Workers: parallel execution
    - Reporter: HTML + JUnit (for CI)
  - `tests/e2e/fixtures/`:
    - `auth.fixture.ts` — pre-authenticated buyer/seller/admin states
    - `auction.fixture.ts` — auction with bids, ending soon, etc.
    - `database.fixture.ts` — seed/cleanup test data
  - `tests/e2e/pages/`:
    - `LoginPage.ts` — Page Object for login
    - `RegisterPage.ts`
    - `AuctionListPage.ts`
    - `AuctionDetailPage.ts`
    - `BiddingConsolePage.ts`
    - `DashboardPage.ts`
    - `AdminPage.ts`
  - `tests/e2e/helpers/`:
    - `api.helper.ts` — direct API calls for test setup
    - `websocket.helper.ts` — WebSocket event assertions
  - npm scripts:
    - `npm run test:e2e` — run all Playwright tests
    - `npm run test:e2e:ui` — Playwright UI mode
    - `npm run test:e2e:headed` — visible browser
- **Output:** Playwright config, fixtures, page objects, helpers

### T-906: Vue Component Tests (Vitest) 🔵 Qwen
- **Depends on:** T-405
- **Agent:** 🔵 Qwen
- **Scope:** Unit tests za Vue.js komponente
- **Acceptance criteria:**
  - Vitest + Vue Test Utils setup
  - `tests/vue/`:
    - `BiddingConsole.spec.ts`:
      - Renders current price
      - Validates minimum bid amount
      - Disables bid button when amount too low
      - Shows proxy bid options when toggled
      - Updates price on BidPlaced event
      - Updates timer on AuctionExtended event
      - Disables form on AuctionEnded event
      - Shows outbid alert
      - Shows confetti on win
    - `AuctionTimer.spec.ts`:
      - Displays correct days/hours/min/sec
      - Updates every second
      - Changes color at thresholds
      - Pulses in last 2 minutes
      - Shows "ZAVRŠENO" when expired
      - Handles anti-sniping extension
    - `ImageUploader.spec.ts` (if Vue component):
      - Drag & drop
      - Max 10 images
      - Preview thumbnails
      - Reorder
  - Minimum 20 Vue component tests
  - `npx vitest run` — all pass
- **Output:** `tests/vue/*.spec.ts`, `vitest.config.ts`

### T-907: Load Tests (k6) 🔵 Qwen
- **Depends on:** T-301, T-702
- **Agent:** 🔵 Qwen
- **Scope:** Performance i load testovi
- **Input:** `docs/arhitektura/17-testing-strategy.md`
- **Acceptance criteria:**
  - k6 installed
  - `tests/load/`:
    - `bid-stress.js` — 500 VU bidding na istu aukciju (auction ending simulation)
      - Threshold: p99 < 500ms, < 1% failures
    - `search-stress.js` — 200 VU search sa filterima
      - Threshold: p99 < 300ms
    - `mixed-workload.js` — 300 VU (browse + bid + search)
      - Threshold: p99 < 500ms overall
    - `websocket-connections.js` — 1000 concurrent WS connections
      - Threshold: < 5% disconnections
  - Results exported to JSON for analysis
  - `npm run test:load` script
- **Output:** `tests/load/*.js`

---

## PHASE 10: Launch Preparation — All Agents

### T-1000: Performance Optimization 🔵 Qwen
- **Depends on:** All previous phases
- **Agent:** 🔵 Qwen
- **Scope:** Optimizacija za production
- **Acceptance criteria:**
  - N+1 query fixes (Eloquent eager loading audit)
  - Redis caching za:
    - Auction current prices
    - Category tree
    - User trust scores
    - Feature flags
  - Database indexes review + EXPLAIN ANALYZE na kritičnim query-ima
  - Image optimization (WebP, lazy loading, CDN)
  - Vite build optimization (code splitting, tree shaking)
  - Laravel optimizations: config cache, route cache, view cache
  - Target: < 200ms average API response time
- **Output:** Performance fixes, cache setup

### T-1001: Security Hardening 🟣 Claude
- **Depends on:** All previous phases
- **Agent:** 🟣 Claude
- **Scope:** Full security implementation per doc 13
- **Input:** `docs/arhitektura/13-security-architecture.md`
- **Acceptance criteria:**
  - OWASP Top 10 audit:
    - SQL injection: verify all queries use Eloquent/prepared statements
    - XSS: all output escaped, HTMLPurifier for rich text, CSP headers
    - CSRF: all forms have @csrf
    - Authentication bypass test
    - Authorization: all routes have middleware
  - Security headers middleware:
    - X-Content-Type-Options, X-Frame-Options, CSP, HSTS, Referrer-Policy
  - Rate limiting on all sensitive endpoints (from doc 04)
  - File upload security pipeline:
    - MIME type validation (magic bytes)
    - EXIF stripping (privacy)
    - Max size enforcement
    - Random filenames (no user-controlled paths)
  - Secrets management: no hardcoded secrets, key rotation plan
  - Audit logging: `AuditLogger` for all admin/security actions
  - Anti-fraud engine: shill bidding detection, velocity checks
  - GDPR compliance: data export, deletion cascade, retention policy
  - Dependency vulnerability scan: `composer audit` + `npm audit`
  - Redis AUTH + TLS configured
  - PostgreSQL SSL connection enforced
  - `php.ini` hardening (disable_functions, expose_php=Off)
  - Fix ALL pronađene vulnerabilities
- **Output:** Security middleware, AuditLogger, anti-fraud service, GDPR endpoints, hardening config

### T-1004: Monitoring & Observability Setup 🔵 Qwen
- **Depends on:** T-100, T-1003
- **Agent:** 🔵 Qwen
- **Scope:** Full monitoring stack implementation
- **Input:** `docs/arhitektura/15-monitoring-observability.md`
- **Acceptance criteria:**
  - Prometheus exporter za Laravel (custom metrics):
    - HTTP request metrics (method, route, status, duration)
    - Bidding engine metrics (bids_total, processing_duration, conflicts)
    - WebSocket metrics (active connections, messages sent, errors)
    - Queue metrics (processed, failed, wait time, queue size)
    - Business metrics (GMV, registrations, active auctions)
  - Grafana dashboards (Docker service):
    - System Overview (CPU, Memory, Disk, Network)
    - Bidding Engine (Bids/min, Latency, Conflicts)
    - WebSocket Health
    - Queue Monitor
    - Database performance
    - Business KPIs
  - Sentry integration:
    - Error tracking (production)
    - Performance monitoring (20% sampling)
    - Custom spans for bidding transactions
  - Health check endpoints:
    - `GET /health` — public (for UptimeRobot)
    - `GET /health/detailed` — admin only
  - Alerting rules (Slack/SMS):
    - P0: Platform down, DB down, payment failure spike
    - P1: High error rate, slow bids, queue backlog
    - P2: Slow queries, high memory, email bounce
  - Structured JSON logging
  - Log redaction (passwords, cards, tokens)
  - Runbooks for common incidents
- **Output:** Prometheus config, Grafana dashboards, Sentry setup, health endpoints, alert rules

### T-1005: Disaster Recovery & Backup Verification 🔵 Qwen
- **Depends on:** T-1003
- **Agent:** 🔵 Qwen
- **Scope:** DR procedures and backup testing
- **Input:** `docs/arhitektura/15-monitoring-observability.md`
- **Acceptance criteria:**
  - Backup scripts:
    - `scripts/backup-db.sh` — pg_dump → encrypt → S3
    - `scripts/backup-redis.sh` — AOF copy → S3
    - `scripts/backup-config.sh` — encrypted env + config → S3
  - Restore scripts:
    - `scripts/restore-db.sh` — download → decrypt → pg_restore
    - `scripts/restore-full.sh` — full infrastructure restore
  - Backup verification:
    - Weekly automated restore test to staging
    - Row count comparison (production vs backup)
    - Checksums verification
  - DR procedures documented:
    - App server failure → recovery steps
    - Database corruption → PITR recovery
    - Redis failure → fallback to DB
    - Full infrastructure loss → complete rebuild
  - RTO verification: < 4 hours for full recovery
  - RPO verification: < 24 hours data loss max
- **Output:** Backup/restore scripts, DR runbook, verification tests

### T-1002: SEO Setup 🟢 Codex
- **Depends on:** T-402, T-404
- **Agent:** 🟢 Codex
- **Scope:** SEO optimizacija za sve stranice
- **Acceptance criteria:**
  - Dynamic meta tags za svaku aukciju (title, description, OG image)
  - Structured data (JSON-LD Product schema) na auction detail
  - `sitemap.xml` automatski generisan (aktivne aukcije + kategorije)
  - `robots.txt` configured
  - Canonical URLs
  - OpenGraph + Twitter Card tags
  - Breadcrumb structured data
  - Image alt tags
  - Semantic HTML (h1, h2, article, nav, etc.)
- **Output:** SEO middleware, sitemap generator, meta components

### T-1003: Production Deploy 🔵 Qwen
- **Depends on:** T-1000, T-1001
- **Agent:** 🔵 Qwen
- **Scope:** Production deployment
- **Acceptance criteria:**
  - Server provisioning script (Hetzner/AWS/DO)
  - SSL certificates (Let's Encrypt + auto-renewal)
  - Docker Compose production deploy
  - DNS configuration documented
  - Cloudflare setup (CDN + WAF + DDoS protection)
  - Monitoring: UptimeRobot, Sentry, Laravel Horizon dashboard
  - Backup verification (restore test)
  - Smoke tests pass on production
  - Deploy script: `scripts/deploy.sh`
- **Output:** Deploy scripts, server config, monitoring setup

---

## Task Summary by Agent

### 🟣 Claude (Architecture, Business Logic, Security)

| Phase | Tasks | Count |
|-------|-------|-------|
| 0 | Architecture docs (all 21 + README) | 22 |
| 1 | Migrations, Seeds | 2 |
| 2 | Auth setup, Middleware | 2 |
| 3 | Models, BiddingService, State Machine, Events | 4 |
| 6 | Escrow, KYC, Rating, Dispute | 4 |
| 8 | Feature Flags Admin | 1 |
| 10 | Security Hardening | 1 |
| **Total** | | **36** |

### 🟢 Codex (Frontend, UI, Pages)

| Phase | Tasks | Count |
|-------|-------|-------|
| 2 | Auth pages | 1 |
| 4 | Layouts, Components, Landing, Listing, Detail, BiddingConsole, Dashboard, Watchlist | 8 |
| 5 | Seller Dashboard, Create Auction, Orders, Wallet | 4 |
| 8 | Admin Dashboard, Users, Auctions, Categories, Disputes, Statistics | 6 |
| 10 | SEO | 1 |
| **Total** | | **20** |

### 🔵 Qwen (DevOps, API, Integrations, Tests)

| Phase | Tasks | Count |
|-------|-------|-------|
| 1 | Laravel init, Docker, CI/CD, Config | 4 |
| 3 | WebSocket Channels | 1 |
| 7 | Payments, Shipping, Search, Notifications, Cron | 5 |
| 9 | Unit Tests, Feature Tests, Playwright E2E, Vue Tests, Load Tests, Playwright Setup | 8 |
| 10 | Performance, Production Deploy, Monitoring, DR/Backup | 4 |
| **Total** | | **22** |

### Grand Total: **78 taskova**

---

## Testing Requirements (OBAVEZNO za CI/CD)

```
CI Pipeline (runs on every PR):
  ✓ PHP Lint (Pint)
  ✓ PHPStan Level 6
  ✓ ESLint
  ✓ Unit Tests (Pest) — min 80% coverage
  ✓ Feature Tests (Pest) — min 50 tests
  ✓ Vue Component Tests (Vitest) — all pass
  ✓ Playwright E2E Tests — 15 scenarios, all pass
  ✓ Build check (npm run build)

PR Merge Rules:
  ✗ BLOCKED if any test fails
  ✗ BLOCKED if coverage drops below 80%
  ✗ BLOCKED if PHPStan has errors
  ✗ BLOCKED if new feature has no test

Load Tests (manual, before release):
  ✓ k6 bid stress: p99 < 500ms
  ✓ k6 search stress: p99 < 300ms
  ✓ WebSocket: 1000 connections, < 5% drops
```

---

## Dependency Graph (Parallel Work)

```
Week 1-2:  ALL agents start simultaneously
           🔵 Qwen:  T-100 (Laravel), T-101 (Docker), T-104 (CI/CD), T-105 (Config)
           🟣 Claude: T-102 (Migrations) — waits for T-100 then starts
           🟢 Codex:  T-400 (Layouts), T-401 (Components) — waits for T-100 then starts

Week 2-3:  Auth + Models
           🟣 Claude: T-103 (Seeds), T-200 (Auth), T-202 (Middleware), T-300 (Models)
           🟢 Codex:  T-201 (Auth Pages), T-402 (Landing)
           🔵 Qwen:  T-702 (Meilisearch), T-304 (WebSocket), T-905 (Playwright setup)

Week 3-5:  Core Engine + Frontend
           🟣 Claude: T-301 (BiddingService), T-302 (State Machine), T-303 (Events)
           🟢 Codex:  T-403 (Listing), T-404 (Detail), T-405 (BiddingConsole)
           🔵 Qwen:  T-703 (Notifications), T-704 (Cron), T-900 (Bidding tests)

Week 5-7:  Buyer/Seller + Trust + Tests
           🟣 Claude: T-600 (Escrow), T-601 (KYC), T-602 (Rating), T-603 (Dispute)
           🟢 Codex:  T-406 (Dashboard), T-407 (Watchlist), T-500-503 (Seller)
           🔵 Qwen:  T-700 (Payments), T-701 (Shipping), T-901 (Escrow tests)

Week 7-9:  Admin Panel + Full Testing
           🟢 Codex:  T-800-806 (Admin pages)
           🟣 Claude: T-805 (Feature Flags)
           🔵 Qwen:  T-902-904 (Service/Feature/E2E tests), T-906 (Vue tests)

Week 9-10: Security + Performance + Load
           🟣 Claude: T-1001 (Security Hardening)
           🔵 Qwen:  T-1000 (Performance), T-907 (Load tests), T-1004 (Monitoring)
           🟢 Codex:  T-1002 (SEO)

Week 10-11: Launch
           🔵 Qwen:  T-1003 (Deploy), T-1005 (DR/Backup)
           All:      Final smoke tests, production verification
```

**Kritični put:** T-100 → T-102 → T-300 → T-301 → T-303 → T-600 → T-900 → T-904 → T-1001 → T-1003

Svi ostali taskovi mogu raditi paralelno oko ovog kritičnog puta.

**Ukupno trajanje: ~11 sedmica (2.75 mjeseca) sa 3 AI agenta paralelno.**
