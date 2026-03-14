# 🚀 Aukcije.ba - Quick Start Guide

**Brzi start za development i produkciju**

---

## ⚡ Brzi Start (5 minuta)

### 1. Clone & Setup

```bash
# Clone repository
git clone <repository-url> aukcije.ba
cd aukcije.ba

# Copy environment file
cp .env.docker .env
```

### 2. Start Docker

```bash
# Start all services
docker compose up -d

# Check status
docker compose ps
```

### 3. Install Dependencies

```bash
# PHP dependencies
docker compose exec app composer install

# Node.js dependencies
docker compose exec app npm install
```

### 4. Setup Database

```bash
# Run migrations
docker compose exec app php artisan migrate

# Seed demo data
docker compose exec app php artisan db:seed
```

### 5. Start Development

```bash
# Start Laravel server
docker compose exec app php artisan serve --host=0.0.0.0 --port=8000

# Start Vite (in new terminal)
docker compose exec app npm run dev
```

### 6. Access Application

- **App:** http://localhost:8000
- **Mailpit:** http://localhost:8025
- **Meilisearch:** http://localhost:7700
- **Horizon:** http://localhost:8000/horizon

---

## 📦 Dostupni Servisi

| Servis | URL | Credentials |
|--------|-----|-------------|
| **Laravel App** | http://localhost:8000 | - |
| **Mailpit (Email)** | http://localhost:8025 | - |
| **Meilisearch** | http://localhost:7700 | Key: `meilisearch_master_key_2024` |
| **MinIO (S3)** | http://localhost:9000 | `minioadmin` / `minioadmin` |
| **PostgreSQL** | localhost:5432 | `aukcije` / `aukcije_secret_2024` |
| **Redis** | localhost:6379 | No password |

---

## 🎯 Demo Korisnici

Nakon seedanja, koristi ove korisnike za testiranje:

```
Admin:
Email: admin@aukcije.ba
Password: AdminPassword123!

Seller:
Email: seller@test.com
Password: Password123!

Buyer:
Email: buyer@test.com
Password: Password123!

Additional test access:
Email: test.superadmin@aukcije.ba
Password: Test12345!
```

---

## 🔧 Korisne Komande

### Development

```bash
# Clear cache
docker compose exec app php artisan optimize:clear

# Run tests
docker compose exec app php artisan test

# Tail logs
docker compose logs -f app

# Restart services
docker compose restart app nginx redis
```

### Database

```bash
# Fresh migration
docker compose exec app php artisan migrate:fresh --seed

# Backup database
./scripts/backup-db.sh

# Restore database
./scripts/restore-db.sh
```

### Queue & Horizon

```bash
# Check queue status
docker compose exec app php artisan horizon:status

# Restart Horizon
docker compose exec app php artisan horizon:terminate

# Check failed jobs
docker compose exec app php artisan horizon:failed
```

---

## 🚀 Production Deploy

### Requirements

- Server with Docker & Docker Compose
- Domain pointed to server
- SSL certificate (Let's Encrypt)

### Deploy Steps

```bash
# 1. SSH to server
ssh deploy@your-server.com

# 2. Clone/pull code
cd /var/www/aukcije
git pull origin main

# 3. Copy production env
cp .env.production .env

# 4. Start production stack
docker compose -f docker-compose.prod.yml up -d

# 5. Run migrations
docker compose -f docker-compose.prod.yml exec app php artisan migrate --force

# 6. Optimize
docker compose -f docker-compose.prod.yml exec app php artisan optimize

# 7. Check health
curl https://your-domain.com/health
```

---

## 🧪 Testing

### Unit & Feature Tests

```bash
# Run all tests
docker compose exec app php artisan test

# Run with coverage
docker compose exec app php artisan test --coverage --min=80

# Run specific test
docker compose exec app php artisan test tests/Unit/Services/BiddingServiceTest.php
```

### E2E Tests (Playwright)

```bash
# Install Playwright browsers
npx playwright install

# Run all E2E tests
npx playwright test

# Run specific test
npx playwright test tests/e2e/auth.spec.ts

# Run with UI
npx playwright test --ui
```

### Load Tests (k6)

```bash
# Install k6
brew install k6  # macOS
# or download from https://k6.io/docs

# Run load test
k6 run tests/load/bid-stress.js

# With custom config
k6 run --vus 500 --duration 2m tests/load/bid-stress.js
```

---

## 🐛 Troubleshooting

### App Not Starting

```bash
# Check logs
docker compose logs app

# Restart services
docker compose restart

# Rebuild containers
docker compose up -d --build
```

### Database Connection Error

```bash
# Check DB is running
docker compose ps postgres

# Check connection
docker compose exec postgres pg_isready -U aukcije

# Restart DB
docker compose restart postgres
```

### Permission Issues

```bash
# Fix storage permissions
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
docker compose exec app chmod -R 775 storage bootstrap/cache
```

### Queue Not Processing

```bash
# Check Horizon status
docker compose exec app php artisan horizon:status

# Restart Horizon
docker compose restart horizon

# Check failed jobs
docker compose exec app php artisan horizon:failed
```

---

## 📚 Dokumentacija

| Dokument | Opis |
|----------|------|
| [README.md](README.md) | Main project overview |
| [docs/FINAL_SUMMARY.md](docs/FINAL_SUMMARY.md) | Complete project summary |
| [docs/USER_MANUAL.md](docs/USER_MANUAL.md) | User guide |
| [docs/ADMIN_GUIDE.md](docs/ADMIN_GUIDE.md) | Admin guide |
| [docs/TROUBLESHOOTING.md](docs/TROUBLESHOOTING.md) | Common issues |
| [docs/DEPLOYMENT_RUNBOOK.md](docs/DEPLOYMENT_RUNBOOK.md) | Deployment procedures |

---

## 🆘 Support

**Email:** support@aukcije.ba  
**Docs:** https://docs.aukcije.ba  
**Status:** https://status.aukcije.ba

---

**Version:** 1.0.0  
**Last Updated:** March 2026
