# Aukcijska Platforma - Arhitektura

## Documentation Index

### Core Architecture
| # | Dokument | Opis |
|---|----------|------|
| 01 | [System Overview](01-system-overview.md) | Pregled sistema, podsistemi, metrike uspjeha |
| 02 | [Tech Stack](02-tech-stack.md) | Tehnološki izbor + breakdown troškova |
| 03 | [Database Schema](03-database-schema.md) | 22+ tabela, ER dijagram, full SQL |
| 04 | [Auth & Roles](04-auth-and-roles.md) | Laravel Breeze/Jetstream, Spatie RBAC, MFA |
| 05 | [API Design](05-api-design.md) | RESTful JSON API + WebSocket kanali |
| 06 | [Bidding Engine](06-bidding-engine.md) | **CORE:** proxy bidding, anti-sniping, state machine |
| 07 | [Frontend Structure](07-frontend-structure.md) | Tailwind + Livewire/Vue, komponente, responsive |

### Business Features
| # | Dokument | Opis |
|---|----------|------|
| 10 | [Competitive Analysis](10-competitive-analysis.md) | Regionalna analiza: Limundo, OLX, Aukcije.hr, Bolha |
| 11 | [Trust & Safety](11-trust-and-safety.md) | KYC, Escrow, rating, disputes, anti-fraud |
| 12 | [Laravel Architecture](12-laravel-architecture.md) | Service Layer, Events/Listeners, Scheduled Jobs |
| 14 | [Feature Flags & Tiers](14-feature-flags-and-tiers.md) | Free/Premium/Storefront, staged rollout |
| 16 | [Growth & Engagement](16-growth-engagement-strategy.md) | SEO, referral, gamification, re-engagement |
| 18 | [UI & Design Guidelines](18-ui-design-guidelines.md) | Brand identity, boje, typography, mobile-first |
| **20** | **[Payment Integration](20-payment-integration.md)** | **Stripe, Monri, CorvusPay, Wallet, webhooks, PCI** |

### Production Readiness
| # | Dokument | Opis |
|---|----------|------|
| 08 | [Deployment](08-deployment.md) | Docker, Nginx, CI/CD, backup, SSL |
| 09 | [Activity Plan](09-activity-plan.md) | 5-fazni roadmap (14 sedmica) |
| **13** | **[Security Architecture](13-security-architecture.md)** | **OWASP Top 10, PCI-DSS, GDPR, anti-fraud, audit** |
| **15** | **[Monitoring & Observability](15-monitoring-observability.md)** | **Prometheus, Grafana, Sentry, alerts, SLA, DR** |
| **17** | **[Testing Strategy](17-testing-strategy.md)** | **Unit/Feature/E2E/Load tests, coverage targets** |
| **19** | **[Scaling & Infrastructure](19-scaling-infrastructure.md)** | **3-stage scaling, caching, partitioning, clustering** |

## Task Tracking

See [TASKS.md](../TASKS.md) for the full task breakdown (67 tasks, 11 phases, 3 AI agents).

## Current Stack

```
Framework:  Laravel 11.x (PHP 8.3+)
Real-time:  Laravel Reverb (WebSocket) + Laravel Echo
Database:   PostgreSQL 16+ (ACID za bidding transakcije)
Cache/Lock: Redis 7 (atomičke operacije za bid lock-ove)
Search:     Meilisearch (typo-tolerant, regionalni dijalekti)
Frontend:   Tailwind CSS + Livewire v3 / Vue.js 3
Queue:      Laravel Horizon (Redis-backed)
Storage:    AWS S3 + CloudFront CDN
Monitoring: Prometheus + Grafana + Sentry
CI/CD:      GitHub Actions
Security:   Cloudflare WAF + DDoS protection
```

## Architecture Decision

**Laravel 11.x izabran kao primary framework** zbog:
- Zreo ekosistem za e-commerce i real-time aplikacije
- Laravel Reverb za native WebSocket podrška
- Spatie paketi za RBAC, feature flags
- Horizon za monitoring queue job-ova
- Odlična podrška za PostgreSQL + Redis
- PCI-DSS SAQ A compliant (hosted payment pages)

## Business Model

- **3 tiera:** Free (5 aukcija, 8%) → Premium (50+ aukcija, 5%, 29 BAM/mj) → Storefront (neograničeno, 3%, 99 BAM/mj)
- **Komisija:** Diferencirana po tieru (8% / 5% / 3%)
- **Promocije:** Plaćeno izdvajanje aukcija na naslovnoj/vrhu kategorije
- **Escrow:** Interni novčanik za zaštitu kupaca i prodavaca
- **Payments:** Stripe (EUR), Monri (BAM), CorvusPay (HR), interni Wallet

## Production SLAs

| Metric | Target |
|--------|--------|
| Uptime | 99.5% (MVP) → 99.9% (mature) |
| Bid Processing | p99 < 500ms → p99 < 200ms |
| Page Load | p50 < 2s |
| WebSocket Delivery | < 1s |
| RTO | < 4h → < 1h |
| RPO | < 24h → < 1h |

## Documentation Stats

- **21 architecture documents**
- **22+ database tables** defined with full SQL
- **30+ API endpoints** documented
- **67 development tasks** across 11 phases
- **3 AI agents** working in parallel
- **14 weeks** estimated to MVP
