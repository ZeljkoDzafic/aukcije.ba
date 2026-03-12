# Aukcijska Platforma - Project Context

## Project Overview

**Aukcijska Platforma** is a high-concurrency regional auction platform designed for the Balkan market (BiH, Serbia, Croatia, Slovenia). It supports real-time bidding, automated proxy bids, anti-sniping mechanisms, and integrated regional logistics.

### Core Features

| Feature | Description |
|---------|-------------|
| **Proxy Bidding** | Automatic bidding up to user's maximum |
| **Anti-Sniping** | Auction extends 3 min when bid placed in last 2 min |
| **Dynamic Increments** | Bid steps based on current price (0.50 BAM to 50 BAM) |
| **Real-time Updates** | WebSocket broadcast via Laravel Reverb + Echo |
| **Escrow Protection** | Funds held until delivery confirmation |
| **KYC Verification** | 3-level seller verification (email, SMS, documents) |
| **Multi-gateway Payments** | Stripe (intl), Monri (BiH), CorvusPay (HR) |
| **Regional Logistics** | EuroExpress, PostExpress, Overseas integration |

### Tech Stack

| Layer | Technology |
|-------|-----------|
| **Framework** | Laravel 11.x (PHP 8.3+) |
| **Frontend** | Tailwind CSS 4 + Livewire v3 + Vue.js 3 |
| **Database** | PostgreSQL 16+ |
| **Cache/Locks** | Redis 7 |
| **Real-time** | Laravel Reverb (WebSocket) + Echo |
| **Search** | Meilisearch |
| **Queue** | Laravel Horizon (Redis) |
| **Storage** | AWS S3 + CloudFront |
| **CI/CD** | GitHub Actions |
| **Monitoring** | Prometheus + Grafana + Sentry |

## Repository Structure

```
aukcije.ba/
├── docs/
│   ├── TASKS.md                    # 67 tasks across 11 phases
│   └── arhitektura/                # 21 architecture documents
│       ├── 01-system-overview.md
│       ├── 02-tech-stack.md
│       ├── 03-database-schema.md
│       ├── 04-auth-and-roles.md
│       ├── 05-api-design.md
│       ├── 06-bidding-engine.md    # Core feature spec
│       ├── 07-frontend-structure.md
│       ├── 08-deployment.md
│       ├── 09-activity-plan.md
│       ├── 10-competitive-analysis.md
│       ├── 11-trust-and-safety.md
│       ├── 12-laravel-architecture.md
│       ├── 13-security-architecture.md
│       ├── 14-feature-flags-and-tiers.md
│       ├── 15-monitoring-observability.md
│       ├── 16-growth-engagement-strategy.md
│       ├── 17-testing-strategy.md
│       ├── 18-ui-design-guidelines.md
│       ├── 19-scaling-infrastructure.md
│       └── 20-payment-integration.md
├── README.md                       # Main documentation
├── info.txt                        # Regional market analysis
├── tech.txt                        # Architecture blueprint
└── AGENTS.md                       # Repository guidelines
```

## Development Workflow

### Quick Start (Target)

```bash
# 1. Clone
git clone <repo-url>
cd aukcije.ba

# 2. Copy environment
cp .env.example .env

# 3. Start services (Docker)
docker compose up -d

# 4. Install dependencies
composer install
npm install

# 5. Setup database
php artisan migrate
php artisan db:seed

# 6. Start development
php artisan serve         # App server (:8000)
npm run dev              # Vite (frontend assets)
php artisan reverb:start # WebSocket server (:8080)
php artisan horizon      # Queue worker
```

### Testing (Mandatory)

**Rule:** Every feature or bugfix MUST have corresponding tests. PR without tests will NOT be merged.

```bash
# Unit + Feature tests (Pest PHP)
php artisan test --parallel --coverage --min=80

# Browser tests (E2E)
php artisan dusk

# Vue component tests
npx vitest run

# Load tests
k6 run tests/load/bid-stress.js
```

### Code Quality

```bash
# PHP lint
vendor/bin/pint

# Static analysis
vendor/bin/phpstan analyse --level=6

# JS lint
npm run lint
```

### Git Workflow

| Branch | Purpose |
|--------|---------|
| `main` | Production (protected) |
| `develop` | Development branch |
| `feature/T-{id}-{desc}` | Feature branches |

**Commit format:** `type: description` (e.g., `feat: add bidding service`, `docs: refine payment notes`)

## Task Assignment

Tasks are distributed across **3 AI agents** working in parallel:

| Agent | Focus | Tasks |
|-------|-------|-------|
| **🟣 Claude** | Architecture, business logic, Bidding Engine, Trust & Safety | 30 tasks |
| **🟢 Codex** | Frontend, UI components, Livewire/Vue, Blade layouts | 20 tasks |
| **🔵 Qwen** | DevOps, tests, API, integrations, search, cron jobs | 17 tasks |

## Key Architecture Decisions

### Bidding Engine (Core)

- **Concurrency Protection:** Redis lock → DB transaction → Advisory lock fallback
- **Atomic Operations:** All bid placements within database transactions
- **Real-time:** WebSocket broadcast on every bid via `BidPlaced` event
- **State Machine:** AuctionStatus enum with validated transitions

### Security

- **OWASP Top 10:** Full mitigation via Eloquent ORM, rate limiting, CSP headers
- **PCI-DSS:** SAQ A compliance (outsourced payment processing)
- **GDPR:** Data export, erasure, portability endpoints
- **Anti-Fraud:** Shill bidding detection, velocity checks, device fingerprinting

### Database Schema

22+ tables including:
- `users`, `user_profiles`, `user_verifications` (auth + KYC)
- `auctions`, `bids`, `proxy_bids`, `auction_extensions` (core)
- `wallets`, `wallet_transactions`, `payments` (financial)
- `orders`, `shipments`, `user_ratings`, `disputes` (post-auction)
- `categories`, `feature_flags`, `admin_logs` (system)

### Seller Tiers

| Tier | Limit | Commission | Price |
|------|-------|------------|-------|
| **Free** | 5 auctions | 8% | 0 BAM/mj |
| **Premium** | 50 auctions | 5% | 29 BAM/mj |
| **Storefront** | Unlimited | 3% | 99 BAM/mj |

## Environment Variables

```env
# App
APP_NAME=Aukcije
APP_ENV=local
APP_KEY=
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=aukcije
DB_USERNAME=aukcije
DB_PASSWORD=

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# WebSocket (Reverb)
REVERB_APP_ID=
REVERB_APP_KEY=
REVERB_APP_SECRET=
REVERB_HOST=localhost
REVERB_PORT=8080

# Search
MEILISEARCH_HOST=http://localhost:7700
MEILISEARCH_KEY=

# Storage
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=eu-central-1
AWS_BUCKET=aukcije-images

# Payments
STRIPE_KEY=
STRIPE_SECRET=
MONRI_KEY=
CORVUSPAY_STORE_ID=

# Notifications
MAIL_MAILER=mailgun
INFOBIP_API_KEY=
FIREBASE_PROJECT_ID=

# Monitoring
SENTRY_DSN=
```

## Performance Targets

| Endpoint | Target (MVP) | Target (Scale) |
|----------|-------------|----------------|
| `POST /bid` | p99 < 500ms | p99 < 200ms |
| `GET /auctions` | p99 < 300ms | p99 < 100ms |
| `GET /search` | p99 < 200ms | p99 < 50ms |
| WebSocket broadcast | < 500ms | < 200ms |
| Uptime | 99% | 99.5% |

## Coverage Requirements

| Area | Minimum | Ideal |
|------|---------|-------|
| BiddingService | 100% | 100% |
| EscrowService | 100% | 100% |
| WalletService | 95% | 100% |
| **Overall** | **80%** | **85%** |

## Related Documentation

- **System Overview:** [`docs/arhitektura/01-system-overview.md`](docs/arhitektura/01-system-overview.md)
- **Bidding Engine:** [`docs/arhitektura/06-bidding-engine.md`](docs/arhitektura/06-bidding-engine.md)
- **Security:** [`docs/arhitektura/13-security-architecture.md`](docs/arhitektura/13-security-architecture.md)
- **Testing:** [`docs/arhitektura/17-testing-strategy.md`](docs/arhitektura/17-testing-strategy.md)
- **Tasks:** [`docs/TASKS.md`](docs/TASKS.md)
- **Main README:** [`README.md`](README.md)
