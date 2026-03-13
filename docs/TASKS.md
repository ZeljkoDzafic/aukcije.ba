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
- Post-production excellence roadmap: `docs/WORLD_CLASS_ROADMAP.md`
- **121 taskova** ukupno: Phase 0-10 (67 originalni) + Phase 11-16 (54 world-class)

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

### T-102: Database Migrations 🟣 Claude ✅ DONE
- **Depends on:** T-100
- **Agent:** 🟣 Claude
- **Status:** ✅ COMPLETED
- **Scope:** Convert schema doc to executable Laravel migrations
- **Input:** `docs/arhitektura/03-database-schema.md` (22+ tabela)
- **Acceptance criteria:** ✅ ALL MET
  - `database/migrations/` sa 22+ migration fajlova ✅
  - Svi indexi iz schema dokumenta ✅
  - Foreign key constraints ✅
  - Enum validacije via CHECK constraints ✅
- **Output:** `database/migrations/*.php` ✅
- **Test:** `php artisan migrate` — no errors ✅

### T-103: Seed Data 🟣 Claude ✅ DONE
- **Depends on:** T-102
- **Agent:** 🟣 Claude
- **Status:** ✅ COMPLETED
- **Scope:** Create seed data za development/demo ✅
- **Acceptance criteria:** ✅ ALL MET
  - `database/seeders/`: ✅
    - `RoleSeeder.php` — 5 rola + permissions (Spatie) ✅
    - `CategorySeeder.php` — 15+ kategorija ✅
    - `BidIncrementSeeder.php` — 7 razina bid incrementa ✅
    - `UserSeeder.php` — 1 admin, 2 moderatora, 5 sellera, 10 buyera ✅
    - `AuctionSeeder.php` — 30+ aukcija ✅
    - `BidSeeder.php` — 100+ bidova ✅
    - `FeatureFlagSeeder.php` — 11 predefinisanih flagova ✅
  - Sve na BHS jeziku ✅
  - Realistični podaci ✅
- **Output:** `database/seeders/*.php` ✅
- **Test:** `php artisan db:seed` — no errors ✅

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

### T-200: Auth Setup (Breeze + Spatie) 🟣 Claude ✅ DONE
- **Depends on:** T-100, T-102
- **Agent:** 🟣 Claude
- **Status:** ✅ COMPLETED
- **Scope:** Complete auth system with roles ✅
- **Acceptance criteria:** ✅ ALL MET
  - Laravel Breeze installed (Blade + Livewire stack) ✅
  - Spatie roles & permissions configured ✅
  - 5 roles: `buyer`, `seller`, `verified_seller`, `moderator`, `super_admin` ✅
  - Granularne permissions (30+ permissions) ✅
  - Registration: izbor buyer/seller tipa ✅
  - Email verification obavezan ✅
  - MFA setup za seller role ✅
  - Rate limiting na auth endpointima ✅
- **Output:** Auth controllers, middleware, Spatie config ✅

### T-201: Auth Pages (Frontend) 🟢 Codex
- **Depends on:** T-200
- **Agent:** 🟢 Codex
- **Status update:** ✅ DONE — auth Blade pages implemented and rendering
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

### T-202: Middleware 🟣 Claude ✅ DONE
- **Depends on:** T-200
- **Agent:** 🟣 Claude
- **Status:** ✅ COMPLETED
- **Scope:** Route protection middleware ✅
- **Acceptance criteria:** ✅ ALL MET
  - `EnsureKycVerified` — blokira seller akcije bez KYC ✅
  - `EnsureSellerRole` — blokira ne-sellere od kreiranja aukcija ✅
  - `ThrottleBids` — rate limit: 10 bid/min po korisniku ✅
  - `EnsureAuctionActive` — provjera statusa aukcije ✅
  - Route groups sa middleware ✅
- **Output:** `app/Http/Middleware/*.php`, route definitions ✅

---

## PHASE 3: Auction Engine (Core) — 🟣 Claude Primary ✅ DONE

### T-300: Eloquent Models 🟣 Claude ✅ DONE
- **Depends on:** T-102
- **Agent:** 🟣 Claude
- **Status:** ✅ COMPLETED
- **Scope:** All Eloquent models with relationships, scopes, casts ✅
- **Acceptance criteria:** ✅ ALL MET
  - Models za svih 22+ tabela ✅
  - `Auction` model sa relacijama, scopes, casts ✅
  - `Bid` model sa relacijama, scopes ✅
  - `User` model sa relacijama, accessors ✅
  - `Wallet`, `Order`, `Payment`, `Shipment`, `ProxyBid` ✅
  - `Category` — self-referencing ✅
- **Output:** `app/Models/*.php` ✅ (24 modela)

### T-301: BiddingService 🟣 Claude ✅ DONE
- **Depends on:** T-300
- **Agent:** 🟣 Claude
- **Status:** ✅ COMPLETED
- **Scope:** Core bidding logic — najkritičniji servis na platformi ✅
- **Input:** `docs/arhitektura/06-bidding-engine.md` ✅
- **Acceptance criteria:** ✅ ALL MET
  - `app/Services/BiddingService.php` ✅
    - `placeBid()` — atomic bid placement sa Redis lock + DB transaction ✅
    - `processProxyBids()` — auto-bid do max iznosa ✅
    - `validateBid()` — sve validacije ✅
  - `app/Services/BidIncrementService.php` ✅
  - `app/Services/AuctionService.php` ✅
  - Concurrency zaštita: Redis lock → DB transaction ✅
  - Custom exceptions ✅
- **Output:** `app/Services/BiddingService.php`, `BidIncrementService.php`, `AuctionService.php` ✅
- **Test:** Unit tests za sve edge cases ✅

### T-302: Auction State Machine 🟣 Claude ✅ DONE
- **Depends on:** T-300
- **Agent:** 🟣 Claude
- **Status:** ✅ COMPLETED
- **Scope:** Auction lifecycle management ✅
- **Acceptance criteria:** ✅ ALL MET
  - `app/Enums/AuctionStatus.php` ✅
  - `app/Enums/AuctionType.php` ✅
  - State transitions sa validacijom ✅
  - `EndExpiredAuctions` artisan command ✅
  - `EndAuctionJob` — queued job ✅
  - Scheduler registration ✅
- **Output:** Enums, commands, jobs ✅

### T-303: Events & Listeners 🟣 Claude ✅ DONE
- **Depends on:** T-301
- **Agent:** 🟣 Claude
- **Status:** ✅ COMPLETED
- **Scope:** Domain events i event handlers ✅
- **Acceptance criteria:** ✅ ALL MET
  - Events: `BidPlaced`, `AuctionExtended`, `AuctionEnded`, `AuctionWon`, `OrderCreated` ✅
  - Listeners: `BroadcastBidUpdate`, `SendOutbidNotification`, `NotifyWatchers` ✅
  - Event → Listener mapping u `EventServiceProvider` ✅
- **Output:** `app/Events/*.php`, `app/Listeners/*.php` ✅

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
- **Status update:** ✅ DONE — guest, app, seller, and admin layouts implemented
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
- **Status update:** ✅ DONE — shared component library implemented and in use
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
- **Status update:** ✅ DONE — landing page implemented with sectioned marketing layout
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
- **Status update:** ✅ DONE — Livewire listing filters, sorting, category options, and model-backed search implemented
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
- **Status update:** ✅ DONE — detail page now binds dynamic auction/category/bid data with related auctions and SEO schema
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
- **Status update:** ✅ DONE — Vue bidding console now covers submit flow, realtime listeners, timer states, outbid alerts, and winning confetti UX
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
- **Status update:** ✅ DONE — buyer dashboard page implemented
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
- **Status update:** ✅ DONE — Livewire watchlist now renders model-backed data and removes items through pivot persistence
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
- **Status update:** ✅ DONE — seller dashboard page implemented
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
- **Status update:** ✅ DONE — create/edit wizard now covers image upload/reorder, draft/publish persistence, edit mode, and tier-limit enforcement
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
- **Status update:** ✅ DONE — seller orders listing/detail, shipped-status fulfillment, and CSV export flow implemented
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
- **Status update:** ✅ DONE — wallet Livewire flow now covers balances, history, gateway-selected deposits, and withdrawal actions
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

## PHASE 6: Trust & Safety — 🟣 Claude Primary ✅ DONE

### T-600: EscrowService 🟣 Claude ✅ DONE
- **Depends on:** T-301, T-302
- **Agent:** 🟣 Claude
- **Status:** ✅ COMPLETED
- **Scope:** Escrow sistem za zaštitu transakcija ✅
- **Acceptance criteria:** ✅ ALL MET
  - `app/Services/EscrowService.php` ✅
    - `holdFunds(Order)` ✅
    - `releaseFunds(Order)` ✅
    - `refundBuyer(Order, float $amount)` ✅
    - `autoRelease()` ✅
  - `app/Services/WalletService.php` ✅
  - `EscrowAutoRelease` artisan command ✅
  - Custom exceptions ✅
- **Output:** EscrowService, WalletService, commands ✅

### T-601: KYC Verification 🟣 Claude ✅ DONE
- **Depends on:** T-200, T-300
- **Agent:** 🟣 Claude
- **Status:** ✅ COMPLETED
- **Scope:** Know Your Customer verifikacija ✅
- **Acceptance criteria:** ✅ ALL MET
  - `app/Services/KycService.php` ✅
  - Controller za KYC flow ✅
  - Email notifikacije ✅
  - Rate limiting za SMS OTP ✅
- **Output:** KycService, controller, mail templates ✅

### T-602: Rating Service 🟣 Claude ✅ DONE
- **Depends on:** T-300
- **Agent:** 🟣 Claude
- **Status:** ✅ COMPLETED
- **Scope:** Dvosmjerno ocjenjivanje + trust score ✅
- **Acceptance criteria:** ✅ ALL MET
  - `app/Services/RatingService.php` ✅
  - Trust score formula ✅
  - Trust badges automatski dodijeljeni ✅
  - Cached trust score (Redis) ✅
- **Output:** RatingService, trust score calculation ✅

### T-603: Dispute Management 🟣 Claude ✅ DONE
- **Depends on:** T-600
- **Agent:** 🟣 Claude
- **Status:** ✅ COMPLETED
- **Scope:** Dispute flow za admin resolution ✅
- **Acceptance criteria:** ✅ ALL MET
  - `app/Services/DisputeService.php` ✅
  - Dispute razlozi ✅
  - Rokovi ✅
  - Email notifikacije ✅
  - Escalation ✅
- **Output:** DisputeService, notifications ✅

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
- **Status update:** ✅ DONE — admin dashboard implemented
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
- **Status update:** ✅ DONE — admin user filters, detail view, role/ban actions, KYC review trigger, reset-password action, and admin logging are implemented
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
- **Status update:** ✅ DONE — moderation listing/detail, approve/cancel/feature actions, and bulk moderation controls are implemented
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
- **Status update:** ✅ DONE — category manager now supports save, activate/deactivate, and reorder interactions
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
- **Status update:** ✅ DONE — dispute queue/detail, resolution actions, and admin communication log workflow are implemented
- **Scope:** UI za rješavanje sporova
- **Acceptance criteria:**
  - `resources/views/pages/admin/disputes/index.blade.php` — dispute queue
  - `resources/views/pages/admin/disputes/show.blade.php` — dispute detail
  - Timeline view: otvaranje → dokazi → odgovor → rješenje
  - Akcije: request more evidence, resolve (buyer/seller), partial refund
  - Order details, payment info, shipping tracking
  - Communication log (admin ↔ parties)
- **Output:** Dispute admin pages

### T-805: Feature Flags Admin 🟣 Claude ✅ DONE
- **Depends on:** T-800
- **Agent:** 🟣 Claude
- **Status:** ✅ COMPLETED
- **Scope:** Feature flag management UI + backend ✅
- **Acceptance criteria:** ✅ ALL MET
  - `app/Livewire/Admin/FeatureFlags.php` + view ✅
  - Lista svih feature flagova sa toggle switch ✅
  - Edit: active/inactive, description ✅
  - Create new flag ✅
  - Livewire instant toggle ✅
  - Middleware + Blade directive ✅
- **Output:** Feature flag admin + middleware + directive ✅

### T-806: Admin Statistics & Analytics 🟢 Codex
- **Depends on:** T-800
- **Agent:** 🟢 Codex
- **Status update:** ✅ DONE — analytics UI now includes Livewire summaries, chart-style dataset visualisation, date range control, and CSV export
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

### T-1001: Security Hardening 🟣 Claude ✅ DONE
- **Depends on:** All previous phases
- **Agent:** 🟣 Claude
- **Status:** ✅ COMPLETED
- **Scope:** Full security implementation per doc 13 ✅
- **Input:** `docs/arhitektura/13-security-architecture.md` ✅
- **Acceptance criteria:** ✅ ALL MET
  - OWASP Top 10 audit ✅
  - Security headers middleware ✅
  - Rate limiting on all sensitive endpoints ✅
  - File upload security pipeline ✅
  - Secrets management ✅
  - Audit logging ✅
  - Anti-fraud engine ✅
  - GDPR compliance ✅
  - Dependency vulnerability scan ✅
- **Output:** Security middleware, AuditLogger, anti-fraud service, GDPR endpoints ✅

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
- **Status update:** ✅ DONE — dynamic meta tags, canonical tags, JSON-LD, robots, and sitemap endpoints implemented
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

## Task Summary by Agent - UPDATED

### 🟣 Claude (Architecture, Business Logic, Security) - 100% DONE ✅

| Phase | Tasks | Count | Status |
|-------|-------|-------|--------|
| 0 | Architecture docs (all 22 + README) | 22 | ✅ DONE |
| 1 | Migrations, Seeds | 2 | ✅ DONE |
| 2 | Auth setup, Middleware | 2 | ✅ DONE |
| 3 | Models, BiddingService, State Machine, Events | 4 | ✅ DONE |
| 6 | Escrow, KYC, Rating, Dispute | 4 | ✅ DONE |
| 8 | Feature Flags Admin | 1 | ✅ DONE |
| 10 | Security Hardening | 1 | ✅ DONE |
| **Total** | | **36** | **✅ 100%** |

### 🟢 Codex (Frontend, UI, Pages)

| Phase | Tasks | Count | Status |
|-------|-------|-------|--------|
| 2 | Auth pages | 1 | ✅ DONE |
| 4 | Layouts, Components, Landing, Listing, Detail, BiddingConsole, Dashboard, Watchlist | 8 | ✅ DONE |
| 5 | Seller Dashboard, Create Auction, Orders, Wallet | 4 | ✅ DONE |
| 8 | Admin Dashboard, Users, Auctions, Categories, Disputes, Statistics | 6 | ✅ DONE |
| 10 | SEO | 1 | ✅ DONE |
| **Total** | | **20** | **✅ 100%** |

### 🔵 Qwen (DevOps, API, Integrations, Tests) - 100% DONE ✅

| Phase | Tasks | Count | Status |
|-------|-------|-------|--------|
| 1 | Laravel init, Docker, CI/CD, Config | 4 | ✅ DONE |
| 3 | WebSocket Channels | 1 | ✅ DONE |
| 7 | Payments, Shipping, Search, Notifications, Cron | 5 | ✅ DONE |
| 9 | Unit Tests, Feature Tests, Playwright E2E, Vue Tests, Load Tests, Playwright Setup | 8 | ✅ DONE |
| 10 | Performance, Production Deploy, Monitoring, DR/Backup | 4 | ✅ DONE |
| **Total** | | **22** | **✅ 100%** |

### Grand Total: **78 taskova**

| Agent | Completed | Pending | Total | % Done |
|-------|-----------|---------|-------|--------|
| 🟣 Claude | 36 | 0 | 36 | **100%** ✅ |
| 🟢 Codex | 20 | 0 | 20 | **100%** ✅ |
| 🔵 Qwen | 22 | 0 | 22 | **100%** ✅ |
| **TOTAL** | **78** | **0** | **78** | **100%** ✅ |

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

---

## PHASE 11: Trust & Safety (World-Class — M1) 🔴

> Detalji: [WORLD_CLASS_ROADMAP.md → Faza 1](WORLD_CLASS_ROADMAP.md)

### Backend (🟣 Claude)

| ID | Task | Agent | Status | Opis |
|----|------|-------|--------|------|
| T-1100 | FraudScoringService | 🟣 Claude | ✅ DONE | Risk score 0-100 po korisniku: account age, bid velocity, win/bid ratio, device fingerprint. Ažurira se na svaki bid. |
| T-1101 | ShillBiddingDetector | 🟣 Claude | ✅ DONE | Heuristika: isti IP/device/seller. Auto-flag za admin review queue u `admin_logs`. |
| T-1102 | RiskReviewQueueAPI | 🟣 Claude | ✅ DONE | Proširiti `admin_logs` sa `risk_level`, `risk_signals`, `review_status`. Admin API za review. |
| T-1103 | AuditTrailMiddleware | 🟣 Claude | ✅ DONE | Middleware koji automatski loguje sve POST/PUT/DELETE admin/seller akcije. |
| T-1104 | SellerReputationScore | 🟣 Claude | ✅ DONE | Periodični job (svaka 24h): fulfilment_rate×0.4 + punctuality×0.3 + dispute_rate×0.2 + response_time×0.1. |
| T-1105 | KycEnforcementService | 🟣 Claude | ✅ DONE | Striktna ograničenja po KYC nivou: email=praćenje, sms=licitiranje, dokument=prodaja+withdrawal. |
| T-1106 | 2FA za prodavace | 🟣 Claude | ✅ DONE | TOTP obavezan za verified_seller i admin. `laravel-google-2fa`. |

### Frontend (🟢 Codex)

| ID | Task | Agent | Status | Opis |
|----|------|-------|--------|------|
| T-1150 | SellerReputationBadge | 🟢 Codex | ✅ DONE | Badge komponenta je uvezana na auction detail i javni profil prodavca, sa trust tooltip signalima. |
| T-1151 | 2FA Enrollment UI | 🟢 Codex | 🟡 PARTIAL | UI view postoji, ali puni enrollment/settings flow još traži završno povezivanje. |
| T-1152 | KYC Status Dashboard | 🟢 Codex | 🟡 PARTIAL | Status dashboard view postoji, ali puni upload/verifikacioni tok još nije zatvoren. |

---

## PHASE 12: Discovery & Search (World-Class — M1) 🟡

> Detalji: [WORLD_CLASS_ROADMAP.md → Faza 2](WORLD_CLASS_ROADMAP.md)

### Backend (🟣 Claude)

| ID | Task | Agent | Status | Opis |
|----|------|-------|--------|------|
| T-1200 | Meilisearch Schema | 🟣 Claude | ✅ DONE | Definisati filterableAttributes, sortableAttributes, rankingRules. Reindex job. |
| T-1201 | SavedSearchService | 🟣 Claude | ✅ DONE | `saved_searches` tabela. Job svaka 4h: nova aukcija → notifikacija svim matching saved searches. |
| T-1202 | HomepageDataService | 🟣 Claude | ✅ DONE | Cache za 4 sekcije: featured, ending_soon, new_arrivals, most_watched. TTL 5min. |
| T-1203 | SellerDirectoryController | 🟣 Claude | ✅ DONE | API: seller profili sa reputacijom, aktivnim aukcijama, kategorijama. Paginate + sort. |
| T-1204 | ReservePrice API | 🟣 Claude | ✅ DONE | BiddingService: bid >= reserve_price otvara buy-now. Seller postavlja pri kreiranju. |

### Frontend (🟢 Codex)

| ID | Task | Agent | Status | Opis |
|----|------|-------|--------|------|
| T-1250 | HomepageSections | 🟢 Codex | 🟡 PARTIAL | Homepage sada ima featured, ending soon i most watched blokove na stvarnim podacima, ali nije još prebačeno na lazy Livewire sekcije i new arrivals blok. |
| T-1251 | SavedSearchUI | 🟢 Codex | ✅ DONE | Spremanje pretrage, pregled, brisanje i toggle alerta su implementirani u korisničkom toku. |
| T-1252 | SellerDirectory | 🟢 Codex | ✅ DONE | Javna stranica `/prodavci` sada ima profile, pretragu, filter po kategoriji i sortiranje. |
| T-1253 | CategoryLandingPages | 🟢 Codex | ✅ DONE | Kategorijske stranice imaju hero sadržaj, podkategorije, listing i SEO copy. |
| T-1254 | ReservePriceBadge | 🟢 Codex | ✅ DONE | Reserve badge komponenta je uvezana na auction detail sa statusnim prikazom. |

---

## PHASE 13: Seller Command Center (World-Class — M2) 🟡

> Detalji: [WORLD_CLASS_ROADMAP.md → Faza 3](WORLD_CLASS_ROADMAP.md)

### Backend (🟣 Claude)

| ID | Task | Agent | Status | Opis |
|----|------|-------|--------|------|
| T-1300 | SellerStatsController | 🟣 Claude | ✅ DONE | GMV, sell-through rate, avg days to sell, top kategorije, dispute rate. Cache 1h. |
| T-1301 | AuctionTemplateService | 🟣 Claude | ✅ DONE | `auction_templates` tabela. `AuctionService::createFromTemplate()`. |
| T-1302 | BulkAuctionService | 🟣 Claude | ✅ DONE | Bulk publish/end/clone N aukcija kao queued jobs. |
| T-1303 | ScheduledStartTime | 🟣 Claude | ✅ DONE | `scheduled` status. Cron job svaka minuta prelazi scheduled → active. |
| T-1304 | SecondChanceOffer | 🟣 Claude | ✅ DONE | `second_chance_offers` tabela. Seller nudi 2. licitantu. Notifikacija kupcu. |
| T-1305 | SellerTierEnforcement | 🟣 Claude | ✅ DONE | `AuctionService::store()` — provjera tier limita (5/50/∞) i komisije (8/5/3%). |
| T-1306 | PaymentDeadlineAutoCancel | 🟣 Claude | ✅ DONE | Job svaki sat: expired payment deadline → cancel order + vrati escrow. |

### Frontend (🟢 Codex)

| ID | Task | Agent | Status | Opis |
|----|------|-------|--------|------|
| T-1350 | SellerAnalyticsDashboard | 🟢 Codex | 🟡 PARTIAL | Analytics UI view postoji, ali puni route/page integration i finalni chart flow još nisu zatvoreni. |
| T-1351 | AuctionTemplateUI | 🟢 Codex | ⏳ TODO | Sačuvaj aukciju kao template. Kreiraj novu iz template-a. Livewire wizard. |
| T-1352 | BulkOperationsUI | 🟢 Codex | ⏳ TODO | Seller checklist sa bulk publish/end/clone. Confirm modal. |
| T-1353 | ScheduledStartPicker | 🟢 Codex | ⏳ TODO | DateTimePicker u CreateAuctionWizard koraku 3. |

---

## PHASE 14: Buyer Experience (World-Class — M2) 🟡

> Detalji: [WORLD_CLASS_ROADMAP.md → Faza 4](WORLD_CLASS_ROADMAP.md)

### Backend (🟣 Claude)

| ID | Task | Agent | Status | Opis |
|----|------|-------|--------|------|
| T-1400 | GDPRDataExportJob | 🟣 Claude | ✅ DONE | Kompajlira sve PII u JSON/ZIP. Download link emailom u < 24h. |
| T-1401 | GDPRErasureService | 🟣 Claude | ✅ DONE | `User::anonymize()` — zamjenjuje PII anonimiziranim podacima, čuva transakcione zapise. |
| T-1402 | PushNotificationService | 🟣 Claude | ✅ DONE | Firebase FCM za web push: outbid, auction_won, payment_due, shipped. |
| T-1403 | WishlistReminderJob | 🟣 Claude | ✅ DONE | Svaka 24h: watchlist aukcije koje uskoro ističu (< 2h) → push/email reminder. |

### Frontend (🟢 Codex)

| ID | Task | Agent | Status | Opis |
|----|------|-------|--------|------|
| T-1450 | LiveBidFeedComponent | 🟢 Codex | ⏳ TODO | Vue komponenta: posljednjih 10 bidova real-time (Echo listener). Animirani feed. |
| T-1451 | PWAManifest | 🟢 Codex | 🟡 PARTIAL | Manifest je dodat u layout i public root, ali service worker/install prompt još nisu zatvoreni. |
| T-1452 | MobileOneTapBid | 🟢 Codex | ⏳ TODO | Quick bid dugme na mobilnom (minimum_bid). Haptic feedback. |
| T-1453 | BlurhashPlaceholders | 🟢 Codex | 🟡 PARTIAL | Komponenta i image optimization osnovа postoje, ali listing još nije sistemski prebačen na blurhash render. |
| T-1454 | SimilarAuctionsSection | 🟢 Codex | ✅ DONE | Similar auctions sekcija je uvezana na auction detail. |
| T-1455 | CookieConsentBanner | 🟢 Codex | 🟡 PARTIAL | Cookie consent banner postoji i uključen je u layout, ali puni consent/runtime governance sloj traži finalizaciju. |
| T-1456 | GDPRSettingsUI | 🟢 Codex | 🟡 PARTIAL | GDPR settings stranica postoji i povezana je s profilom, ali izvozi/brisanje još nisu puni završeni korisnički tokovi. |

---

## PHASE 15: Admin Operations (World-Class — M2) 🟡

> Detalji: [WORLD_CLASS_ROADMAP.md → Faza 5](WORLD_CLASS_ROADMAP.md)

### Backend (🟣 Claude)

| ID | Task | Agent | Status | Opis |
|----|------|-------|--------|------|
| T-1500 | BulkModerationService | 🟣 Claude | ✅ DONE | Bulk approve/reject aukcija sa razlogom. Batch AdminLog. Cache invalidacija. |
| T-1501 | KYCBackofficeController | 🟣 Claude | ✅ DONE | Admin API: pending KYC listanje, pregled dokumenata, approve/reject sa napomenom. |
| T-1502 | CategoryMerchandizingService | 🟣 Claude | ✅ DONE | Sort order za kategorije. `featured` flag za homepage. |
| T-1503 | AdminAnalyticsAPI | 🟣 Claude | ✅ DONE | GMV daily/weekly/monthly, active auctions, new users, conversion rate, top sellers. |
| T-1504 | DBIndicesMigration | 🟣 Claude | ✅ DONE | KRITIČNO: bids(auction_id,created_at), auctions(status,ends_at), auctions(seller_id,status), wallet_transactions(wallet_id,created_at), watchlist(user_id), orders(buyer_id,status). |

### Frontend (🟢 Codex)

| ID | Task | Agent | Status | Opis |
|----|------|-------|--------|------|
| T-1550 | AdminBulkModerationUI | 🟢 Codex | ✅ DONE | Moderation queue ima checkbox selekciju, bulk akcije, note i decision history. |
| T-1551 | KYCBackofficeUI | 🟢 Codex | 🟡 PARTIAL | KYC backoffice UI view postoji, ali nije još punom mjerom povezan kao finalni admin workflow ekran. |
| T-1552 | AdminAnalyticsUI | 🟢 Codex | 🟡 PARTIAL | Analytics UI view i chart skeleton postoje, ali nisu završno spojeni na glavni admin page flow. |

---

## PHASE 16: Performance & Observability (World-Class — M3) 🟢

> Detalji: [WORLD_CLASS_ROADMAP.md → Faza 6](WORLD_CLASS_ROADMAP.md)

### Backend (🟣 Claude / 🔵 Qwen)

| ID | Task | Agent | Status | Opis |
|----|------|-------|--------|------|
| T-1600 | HorizonQueueConfig | 🔵 Qwen | ✅ DONE | Queue config, Horizon runtime i deploy/restart tokovi su implementirani u config/docker/deploy sloju. |
| T-1601 | SLOMonitoringJob | 🔵 Qwen | ✅ DONE | SLO monitoring job i Prometheus/Grafana wiring postoje u aplikacionom i docker observability sloju. |
| T-1602 | QueryOptimizationAudit | 🟣 Claude | ✅ DONE | Telescope N+1 audit. Dodati eager loads gdje nedostaju. |
| T-1603 | ImageOptimizationPipeline | 🔵 Qwen | 🟡 PARTIAL | Image optimization servis i Imgix-ready URL logika postoje, ali puni media pipeline još nije svuda uvezan. |

---

## World-Class Task Summary

| Faza | Backend (🟣 Claude) | Frontend (🟢 Codex) | DevOps (🔵 Qwen) | Milestone |
|------|---------------------|---------------------|------------------|-----------|
| 11: Trust & Safety | T-1100..1106 (7) | T-1150..1152 (3) | — | M1 |
| 12: Discovery | T-1200..1204 (5) | T-1250..1254 (5) | — | M1 |
| 13: Seller Tools | T-1300..1306 (7) | T-1350..1353 (4) | — | M2 |
| 14: Buyer Experience | T-1400..1403 (4) | T-1450..1456 (7) | — | M2 |
| 15: Admin Ops | T-1500..1504 (5) | T-1550..1552 (3) | — | M2 |
| 16: Performance | T-1602 (1) | — | T-1600, T-1601, T-1603 (3) | M3 |
| **Ukupno** | **29 taskova** | **22 taskova** | **3 taskova** | |

**Ukupno world-class taskova: 54**
**Ukupno svih taskova (Phase 0-16): ~121**
