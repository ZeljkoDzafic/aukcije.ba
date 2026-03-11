# 02 - Technology Stack

## The Stack

```
Framework:  Laravel 11.x (PHP 8.3+)
Real-time:  Laravel Reverb (WebSocket) + Laravel Echo
Database:   PostgreSQL 16+ (Strict ACID za bidding transakcije)
Cache/Lock: Redis (Atomičke operacije za bid lock-ove)
Search:     Meilisearch (High speed, typo-tolerant za regionalne dijalekte)
Frontend:   Tailwind CSS + Livewire v3 (reaktivni UI) / Vue.js 3 (kompleksni dashboardi)
Queue:      Laravel Horizon (Redis-backed)
Storage:    AWS S3 (slike proizvoda)
```

## Zašto ovaj Stack?

### Framework: Laravel 11.x

| Izbor | Zašto |
|-------|-------|
| **Laravel** | Zreo ekosistem, Eloquent ORM, ugrađen queue sistem |
| **PHP 8.3+** | Fibers, typed properties, enum support, performanse |
| **Horizon** | Vizualni monitoring svih queue jobova |
| **Reverb** | Native WebSocket server — nema potrebe za Pusher/Soketi |

**Zašto NE Node.js/NestJS?**
- Laravel ima zreliji ekosistem za e-commerce (Spatie paketi, Cashier, itd.)
- Reverb eliminira potrebu za zasebnim WebSocket serverom
- Eloquent ORM + migrations = brži razvoj baze
- PHP hosting je jeftiniji i dostupniji u regionu

### Real-time: Laravel Reverb + Echo

```
1. Korisnik postavi bid → POST /auctions/{id}/bid
2. BiddingService validira i procesira bid (Redis lock)
3. BidPlaced event se emituje
4. Laravel Reverb broadcastuje na WebSocket kanal
5. Laravel Echo (frontend) prima update
6. AuctionCard komponenta ažurira cijenu u realnom vremenu
```

### Database: PostgreSQL 16+

| Što dobiješ | Kako |
|-------------|------|
| ACID transakcije | Integritet podataka pri konkurentnim bidovima |
| Advisory Locks | Dodatni layer zaštite za bidding race conditions |
| JSONB kolone | Fleksibilni metadata za aukcije |
| Full-text search | Backup za Meilisearch (fallback) |
| Partitioning | Skaliranje tabele bids po datumu |

### Cache/Lock: Redis

| Funkcija | Implementacija |
|----------|---------------|
| Bid Lock | `Redis::lock("auction:{$id}", 5)` — sprječava race condition |
| Current Price Cache | Najnovija cijena uvijek u Redis-u za brz pristup |
| Rate Limiting | Ograničavanje bid frekvencije po korisniku |
| Session Storage | Brze sesije za autentificirane korisnike |
| Queue Backend | Laravel Horizon koristi Redis za job queue |

### Search: Meilisearch

| Feature | Benefit |
|---------|---------|
| Typo tolerance | "Samsug" → "Samsung" |
| Regionalni dijalekti | "Mobilni telefon" = "Mobitel" = "Telefon" |
| Faceted search | Filtriranje po kategoriji, cijeni, lokaciji |
| < 50ms latency | Instant rezultati pretrage |

### Frontend: Tailwind CSS + Livewire v3 / Vue.js 3

| Komponenta | Tehnologija | Razlog |
|-----------|-------------|--------|
| Aukcijske stranice | Livewire v3 | Reaktivnost bez SPA kompleksnosti |
| Bidding konzola | Vue.js 3 | Kompleksna real-time interakcija |
| Admin panel | Livewire v3 | CRUD operacije, tabele, forme |
| Landing/SEO stranice | Blade + Tailwind | Statički, SEO-friendly |

## Cost Breakdown

| Servis | Mjesečni trošak | Napomene |
|--------|-----------------|----------|
| AWS/DO server (app) | $24-48 | Laravel + Reverb + Horizon |
| AWS/DO server (DB) | $15-30 | PostgreSQL + Redis |
| Meilisearch | $0 | Self-hosted na istom serveru |
| AWS S3 (slike) | $5-10 | Ovisi o broju aukcija |
| Cloudflare | $0-20 | Free tier + Pro za WAF |
| Mailgun/Resend | $0 | Free tier za početak |
| Infobip SMS | ~$10 | KYC verifikacija |
| **Ukupno** | **$54-138/mj** | |

## Development Tools

| Alat | Svrha |
|------|-------|
| VS Code + Claude Code | Development |
| PHP 8.3 + Composer | Backend dependency management |
| Node.js + npm | Frontend build (Vite) |
| Laravel Sail | Local Docker development |
| TablePlus / pgAdmin | Database management |
| Postman / Insomnia | API testing |
| Git + GitHub | Version control |
| GitHub Actions | CI/CD pipeline |

## Composer Dependencies (core)

```json
{
  "require": {
    "laravel/framework": "^11.0",
    "laravel/reverb": "^1.0",
    "laravel/horizon": "^5.0",
    "laravel/scout": "^10.0",
    "livewire/livewire": "^3.0",
    "spatie/laravel-permission": "^6.0",
    "spatie/laravel-feature-flags": "^1.0",
    "meilisearch/meilisearch-php": "^1.0"
  }
}
```
