# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**aukcije.ba** is a regional auction platform for the Balkan market. The repository currently contains architecture documentation and task planning — no runnable app scaffold exists yet. The app will be built on Laravel 11 + PostgreSQL + Redis + Livewire v3 + Vue.js 3.

## Commands (once app scaffold exists)

```bash
# Services
docker compose up -d

# Dependencies
composer install && npm install

# Dev servers
php artisan serve
npm run dev
php artisan reverb:start   # WebSocket
php artisan horizon         # Queue worker

# Database
php artisan migrate
php artisan db:seed

# Tests
php artisan test --parallel  # Unit + Feature (Pest)
php artisan dusk             # Browser E2E
npx vitest run               # Vue component tests
k6 run tests/load/bid-stress.js

# Code quality
vendor/bin/pint
vendor/bin/phpstan analyse --level=6
npm run lint
```

## Architecture

### Tech Stack
- **Backend:** Laravel 11 (PHP 8.3+), PostgreSQL 16+, Redis 7
- **Frontend:** Livewire v3 + Vue.js 3, Tailwind CSS 4, Vite
- **Real-time:** Laravel Reverb (WebSocket) + Laravel Echo
- **Queue:** Laravel Horizon
- **Search:** Meilisearch
- **Storage:** AWS S3 + CloudFront
- **Payments:** Stripe (EUR), Monri (BAM), CorvusPay (EUR/HR), internal wallet

### Core Subsystems
- **BiddingService** — proxy bidding, anti-sniping (auto-extend if bid in final 2 min), dynamic increments, Redis atomic locks for concurrency
- **EscrowService** — funds frozen at bid win, released after shipment confirmation
- **WalletService** — internal wallet for deposits/withdrawals
- **KYC** — 3-tier (email → SMS → document)
- **DisputeService** — buyer/seller dispute resolution with admin review
- **RatingService** — bidirectional buyer↔seller ratings

### Service Layer Pattern
Business logic lives in `app/Services/`. Services emit domain events (`app/Events/`) consumed by listeners (`app/Listeners/`). Jobs (`app/Jobs/`) handle async work via Horizon.

### Key config files (planned)
- `config/auction.php` — bid increment rules, anti-sniping duration
- `config/escrow.php` — confirmation deadlines, refund windows
- `config/tiers.php` — seller tier limits and commissions (Free 8%, Premium 5%, Storefront 3%)
- `config/payment.php` — payment gateway settings

### Database
22+ PostgreSQL tables. Core: `users`, `auctions`, `bids`, `proxy_bids`, `orders`, `payments`, `wallets`, `wallet_transactions`, `escrow_transactions`, `shipments`, `disputes`, `user_ratings`, `feature_flags`. See `docs/arhitektura/03-database-schema.md`.

## Agent Role (Claude = 🟣)

Claude owns: architecture, business logic, bidding engine, trust & safety, security hardening.

**Claude's pending implementation tasks (in dependency order):**
1. **T-102** — Database migrations (depends on T-100: Laravel init by Qwen)
2. **T-103** — Seed data (depends on T-102)
3. **T-200** — Auth setup (Breeze + Spatie permissions)
4. **T-202** — Middleware (KYC checks, rate limiting)
5. **T-300** — Eloquent models (all 22+ models with relationships)
6. **T-301** — BiddingService (proxy bidding, anti-sniping, atomic Redis locks)
7. **T-302** — Auction state machine (draft → active → ended → settled)
8. **T-303** — Events & listeners (BidPlaced, AuctionEnded, OutbidNotification)
9. **T-600** — EscrowService (funds freeze/release, deadlines)
10. **T-601** — KYC verification flow
11. **T-602** — RatingService
12. **T-603** — DisputeManagement
13. **T-805** — Feature flags admin panel
14. **T-1001** — Security hardening (OWASP, PCI-DSS, rate limiting, shill detection)

## Testing Requirements (Mandatory)

| Service | Required Coverage |
|---------|-------------------|
| BiddingService | **100%** |
| EscrowService | **100%** |
| WalletService | **95%** |
| Overall | **80%+** |

Every task Claude implements **must include tests in the same PR**. CI rejects PRs without tests.

## Commit Style

Use imperative scoped messages: `feat: add BiddingService with proxy bidding`, `fix: resolve race condition in bid placement`, `docs: update escrow flow diagram`. Reference task IDs: `(T-301)`.

## Documentation

All architecture docs in `docs/arhitektura/` (01–20, numbered reading order). Task tracking in `docs/TASKS.md`. New design notes go in `docs/`, following kebab-case numbered naming.
