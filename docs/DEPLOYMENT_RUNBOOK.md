# Deployment Runbook

## Overview

This runbook provides step-by-step instructions for deploying Aukcije.ba to production.

---

## Pre-Deployment Checklist

### 1. Code Review

- [ ] All tests passing (CI/CD)
- [ ] Code review approved
- [ ] Security scan completed
- [ ] Performance benchmarks met
- [ ] Database migrations reviewed

### 2. Environment Verification

- [ ] Production .env configured
- [ ] All secrets in place (AWS Secrets Manager)
- [ ] SSL certificates valid
- [ ] DNS records configured
- [ ] CDN configured

### 3. Database Backup

```bash
# Create backup before deployment
./scripts/backup-db.sh

# Verify backup
aws s3 ls s3://aukcije-backups/database/$(date +%Y/%m/%d)/

# Test restore on staging (monthly)
./scripts/restore-db.sh latest
```

### 4. Notify Stakeholders

- [ ] Team notified via Slack #deployments
- [ ] Status page updated (if maintenance required)
- [ ] On-call engineer confirmed

---

## Deployment Steps

### Step 1: Connect to Production Server

```bash
# SSH to production server
ssh deploy@aukcije.ba

# Or use AWS Session Manager
aws ssm start-session --target i-xxxxxxxxx
```

### Step 2: Pull Latest Changes

```bash
cd /var/www/aukcije

# Fetch latest code
git fetch origin
git checkout main
git pull origin main

# Verify correct commit
git log -1 --oneline
```

### Step 3: Install Dependencies

```bash
# PHP dependencies
docker compose exec app composer install --no-dev --optimize-autoloader --no-interaction

# Node.js dependencies
docker compose exec app npm ci --only=production
docker compose exec app npm run build
```

### Step 4: Run Migrations

```bash
# Check migration status
docker compose exec app php artisan migrate:status

# Run migrations (with force flag for production)
docker compose exec app php artisan migrate --force

# Verify migrations
docker compose exec app php artisan migrate:status
```

### Step 5: Clear and Cache

```bash
# Clear existing cache
docker compose exec app php artisan optimize:clear

# Cache configuration
docker compose exec app php artisan config:cache

# Cache routes
docker compose exec app php artisan route:cache

# Cache views
docker compose exec app php artisan view:cache

# Cache events
docker compose exec app php artisan event:cache
```

### Step 6: Update Search Index

```bash
# Reindex auctions
docker compose exec app php artisan scout:import "App\\Models\\Auction"

# Verify index
curl http://meilisearch:7700/indexes/aukcije_auctions/stats
```

### Step 7: Restart Services

```bash
# Restart application containers
docker compose restart app nginx

# Restart Horizon (queue workers)
docker compose exec app php artisan horizon:terminate

# Restart Reverb (WebSocket)
docker compose restart reverb

# Verify services
docker compose ps
```

### Step 8: Health Checks

```bash
# Application health
curl -f https://aukcije.ba/health

# API health
curl -f https://aukcije.ba/api/health

# WebSocket health
curl -f https://aukcije.ba:8080/health

# Database connection
docker compose exec app php artisan db:show

# Queue status
docker compose exec app php artisan horizon:status
```

### Step 9: Smoke Tests

```bash
# Homepage
curl -f https://aukcije.ba/

# Login page
curl -f https://aukcije.ba/login

# API endpoint
curl -f https://aukcije.ba/api/v1/auctions

# Check for errors in logs
docker compose logs --tail=100 app | grep -i error
```

---

## Post-Deployment

### 1. Monitor Dashboards

- [ ] Grafana: Check application metrics
- [ ] Sentry: Monitor for new errors
- [ ] Horizon: Check queue health
- [ ] Logs: Check for errors

### 2. Verify Key Features

- [ ] User can login
- [ ] Auctions are loading
- [ ] Bidding is working
- [ ] Payments are processing
- [ ] Emails are sending

### 3. Update Status

- [ ] Update Slack #deployments with results
- [ ] Update status page (if maintenance was required)
- [ ] Document any issues in deployment log

---

## Rollback Procedure

If deployment fails, rollback immediately:

### Step 1: Stop Deployment

```bash
# Cancel any running processes
Ctrl+C
```

### Step 2: Revert Code

```bash
cd /var/www/aukcije

# Revert to previous commit
git checkout <previous-commit-hash>
```

### Step 3: Restore Database (if migrations caused issues)

```bash
# Download backup from before deployment
aws s3 cp s3://aukcije-backups/database/[timestamp]/aukcije_db_[timestamp].sql.gz ./

# Restore database
./scripts/restore-db.sh ./aukcije_db_[timestamp].sql.gz
```

### Step 4: Clear Cache

```bash
docker compose exec app php artisan optimize:clear
```

### Step 5: Restart Services

```bash
docker compose restart
```

### Step 6: Verify Rollback

```bash
# Check application is working
curl -f https://aukcije.ba/health

# Check version
docker compose exec app php artisan --version
```

---

## Emergency Contacts

| Role | Name | Phone | Email |
|------|------|-------|-------|
| On-Call Engineer | [Name] | [Phone] | [Email] |
| DevOps Lead | [Name] | [Phone] | [Email] |
| Product Owner | [Name] | [Phone] | [Email] |
| CTO | [Name] | [Phone] | [Email] |

---

## Common Issues

### Issue: Migration Fails

**Symptoms:** Error during `php artisan migrate --force`

**Solution:**
```bash
# Check migration status
docker compose exec app php artisan migrate:status

# Rollback last migration
docker compose exec app php artisan migrate:rollback --step=1

# Fix migration file and retry
```

### Issue: Queue Workers Stuck

**Symptoms:** Jobs not processing, Horizon shows stalled

**Solution:**
```bash
# Restart Horizon
docker compose exec app php artisan horizon:terminate

# Check failed jobs
docker compose exec app php artisan horizon:failed

# Retry failed jobs
docker compose exec app php artisan horizon:retry all
```

### Issue: High Memory Usage

**Symptoms:** Container restarting, OOM errors

**Solution:**
```bash
# Check memory usage
docker stats

# Restart high-memory containers
docker compose restart app

# Check for memory leaks in logs
docker compose logs app | grep -i "memory"
```

### Issue: WebSocket Disconnections

**Symptoms:** Users reporting real-time updates not working

**Solution:**
```bash
# Check Reverb status
docker compose ps reverb

# Restart Reverb
docker compose restart reverb

# Check Reverb logs
docker compose logs reverb | tail -100
```

---

## Deployment Schedule

| Environment | Schedule | Approval Required |
|-------------|----------|-------------------|
| Staging | On-demand | None |
| Production | Tuesday/Thursday 10:00 CET | Product Owner |
| Hotfix | Anytime | CTO |

---

## Version History

| Version | Date | Deployed By | Notes |
|---------|------|-------------|-------|
| 1.0.0 | [Date] | [Name] | Initial production release |

---

**Last Updated:** March 2026  
**Next Review:** After each major deployment
