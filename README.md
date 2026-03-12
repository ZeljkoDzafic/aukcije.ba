# Aukcijska Platforma

Regionalna aukcijska platforma za tržište Balkana sa real-time licitiranjem, proxy bidding-om, anti-sniping mehanizmom i integriranom logistikom.

## Quick Start

```bash
# 1. Clone
git clone <repo-url>
cd aukcije

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
php artisan serve         # App server
npm run dev              # Vite (frontend assets)
php artisan reverb:start # WebSocket server
php artisan horizon      # Queue worker
```

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Framework | Laravel 11.x (PHP 8.3+) |
| Frontend | Tailwind CSS + Livewire v3 + Vue.js 3 |
| Database | PostgreSQL 16+ |
| Cache / Locks | Redis 7 |
| Real-time | Laravel Reverb (WebSocket) + Echo |
| Search | Meilisearch |
| Queue | Laravel Horizon (Redis) |
| Storage | AWS S3 + CloudFront |
| CI/CD | GitHub Actions |
| Monitoring | Prometheus + Grafana + Sentry |
| Security | Cloudflare WAF + DDoS |

## Architecture

Full architecture documentation: [`docs/arhitektura/`](docs/arhitektura/README.md)

**21 dokumenata** koji pokrivaju:
- System overview, tech stack, database schema (22+ tabela)
- Bidding engine (proxy, anti-sniping, state machine)
- Auth & roles (5 rola, MFA, rate limiting)
- API design (30+ endpointa + WebSocket kanali)
- Security (OWASP Top 10, PCI-DSS, GDPR, anti-fraud)
- Monitoring & observability (Prometheus, Grafana, SLA, DR)
- Testing strategy (unit, feature, E2E, load)
- Scaling (3-stage: single server → multi → cluster)
- Payment integration (Stripe, Monri, CorvusPay)

## Task Tracking

Full task breakdown: [`docs/TASKS.md`](docs/TASKS.md)

**67 taskova** across 11 faza, podijeljeno na 3 AI agenta:
- **Claude** — Arhitektura, biznis logika, security (30 taskova)
- **Codex** — Frontend, UI, stranice (20 taskova)
- **Qwen** — DevOps, API, integracije, testovi (17 taskova)

## Current Product Roadmap

- Production checklist: [`docs/PRODUCTION_READINESS.md`](docs/PRODUCTION_READINESS.md)
- World-class roadmap: [`docs/WORLD_CLASS_ROADMAP.md`](docs/WORLD_CLASS_ROADMAP.md)

## Project Structure

```
aukcije/
├── app/
│   ├── Models/              # Eloquent modeli (22+)
│   ├── Services/            # Biznis logika (BiddingService, EscrowService, etc.)
│   ├── Events/              # Domain events (BidPlaced, AuctionEnded, etc.)
│   ├── Listeners/           # Event handlers
│   ├── Jobs/                # Queue jobs (EndAuctionJob, etc.)
│   ├── Enums/               # AuctionStatus, AuctionType, etc.
│   ├── Http/
│   │   ├── Controllers/     # Web + API + Admin
│   │   ├── Livewire/        # Livewire components
│   │   ├── Middleware/       # Auth, KYC, Throttle
│   │   └── Requests/        # Form validation
│   ├── Notifications/       # Email + Push + SMS
│   └── Policies/            # Authorization policies
├── resources/
│   ├── views/
│   │   ├── layouts/         # Guest, App, Admin, Seller
│   │   ├── livewire/        # Livewire component views
│   │   ├── components/      # Blade components
│   │   └── admin/           # Admin panel views
│   ├── vue/                 # Vue.js components (BiddingConsole)
│   ├── js/                  # Echo, countdown, app.js
│   └── css/                 # Tailwind
├── database/
│   ├── migrations/          # 22+ migration files
│   ├── seeders/             # Demo data
│   └── factories/           # Test factories
├── tests/
│   ├── Unit/                # Service tests (200+)
│   ├── Feature/             # HTTP/API tests (50+)
│   ├── Browser/             # Laravel Dusk E2E (10+)
│   └── Load/                # k6 load tests
├── config/
│   ├── auction.php          # Bidding config
│   ├── escrow.php           # Escrow timelines
│   ├── tiers.php            # Seller tier limits
│   └── payment.php          # Gateway config
├── docs/
│   ├── arhitektura/         # 21 architecture documents
│   └── TASKS.md             # 67 tasks, 3 AI agents
├── docker-compose.yml       # Development
├── docker-compose.prod.yml  # Production
└── .github/workflows/       # CI/CD pipelines
```

## Key Features

### Bidding Engine
- **Proxy Bidding** — automatsko licitiranje do korisnikovog maksimuma
- **Anti-Sniping** — produženje aukcije na bidove u zadnje 2 minute
- **Dynamic Increments** — koraci licitacije bazirani na trenutnoj cijeni
- **Redis Locks** — atomičko procesiranje konkurentnih bidova
- **Real-time Updates** — WebSocket broadcast svake promjene cijene

### Trust & Safety
- **KYC** — 3 nivoa verifikacije (email, SMS, dokument)
- **Escrow** — sredstva zamrznuta do potvrde prijema
- **Rating** — dvosmjerno ocjenjivanje kupac ↔ prodavac
- **Anti-Fraud** — shill bidding detekcija, velocity checks
- **Disputes** — admin resolution sa escrow zaštitom

### Payments
- **Stripe** — international (EUR/USD)
- **Monri** — BiH lokalne kartice (BAM)
- **CorvusPay** — Hrvatska (EUR)
- **Wallet** — interni novčanik za brzo plaćanje

## Development Rules

### Testing (OBAVEZNO)

```bash
# Unit + Feature tests (mora proći prije PR merge-a)
php artisan test --parallel

# Browser tests (E2E)
php artisan dusk

# Vue component tests
npx vitest run

# Load tests
k6 run tests/load/bid-stress.js
```

**Coverage zahtjevi:**
- BiddingService: **100%**
- EscrowService: **100%**
- WalletService: **95%**
- Overall: **80% minimum**

**Pravilo:** Svaka nova feature ili bugfix MORA imati test. PR bez testa se NE mergea.

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

- `main` — production branch (protected)
- `develop` — development branch
- Feature branches: `feature/T-301-bidding-service`
- Naming: `feature/T-{task_id}-{short-description}`
- PR review required before merge
- CI must pass (tests + lint + static analysis)

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

# Reverb (WebSocket)
REVERB_APP_ID=
REVERB_APP_KEY=
REVERB_APP_SECRET=
REVERB_HOST=localhost
REVERB_PORT=8080

# Meilisearch
MEILISEARCH_HOST=http://localhost:7700
MEILISEARCH_KEY=

# S3 (Images)
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=eu-central-1
AWS_BUCKET=aukcije-images

# Payment Gateways
STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=
MONRI_KEY=
MONRI_AUTHENTICITY_TOKEN=
CORVUSPAY_STORE_ID=
CORVUSPAY_SECRET_KEY=

# Notifications
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=
MAILGUN_SECRET=
INFOBIP_API_KEY=
FIREBASE_PROJECT_ID=

# Monitoring
SENTRY_DSN=
```

## License

Private / Proprietary
