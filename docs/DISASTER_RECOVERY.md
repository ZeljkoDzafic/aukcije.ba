# Disaster Recovery Runbook

## Overview

This document outlines disaster recovery procedures for the Aukcije.ba platform.

## Recovery Time Objective (RTO)

| Scenario | Target RTO |
|----------|-----------|
| App server failure | < 30 minutes |
| Database failure | < 1 hour |
| Full infrastructure loss | < 4 hours |
| Data corruption | < 2 hours |

## Recovery Point Objective (RPO)

| Data Type | Target RPO |
|-----------|-----------|
| Database | < 24 hours (daily backups) |
| User uploads | < 1 hour (S3 versioning) |
| Sessions | 0 (Redis persistence) |

---

## Emergency Contacts

| Role | Contact | Phone |
|------|---------|-------|
| Primary Developer | [Name] | [Phone] |
| Backup Developer | [Name] | [Phone] |
| DevOps | [Name] | [Phone] |
| AWS Support | [Account ID] | [Support Phone] |

---

## Scenario 1: App Server Failure

### Symptoms
- HTTP 502/503 errors
- Application unresponsive
- Health check failures

### Recovery Steps

```bash
# 1. Check server status
ssh deploy@aukcije.ba
docker compose ps

# 2. Check logs
docker compose logs app
docker compose logs nginx

# 3. Restart application
docker compose restart app

# 4. If restart fails, redeploy
cd /var/www/aukcije
git pull origin main
docker compose -f docker-compose.prod.yml up -d --build

# 5. Verify health
curl https://aukcije.ba/health
```

### Rollback Procedure

```bash
# Deploy previous version
cd /var/www/aukcije
git checkout HEAD~1
docker compose -f docker-compose.prod.yml up -d --build
```

---

## Scenario 2: Database Failure

### Symptoms
- Database connection errors
- Query timeouts
- Data corruption

### Recovery Steps

```bash
# 1. Check database status
docker compose ps postgres
docker compose logs postgres

# 2. Check disk space
docker compose exec postgres df -h

# 3. Attempt restart
docker compose restart postgres

# 4. If data corruption, restore from backup
./scripts/restore-db.sh

# 5. Verify data integrity
docker compose exec postgres psql -U aukcije -d aukcije -c "
    SELECT COUNT(*) FROM auctions;
    SELECT COUNT(*) FROM users;
    SELECT COUNT(*) FROM bids;
"
```

### Point-in-Time Recovery

```bash
# List available backups
aws s3 ls s3://aukcije-backups/database/

# Download specific backup
aws s3 cp s3://aukcije-backups/database/2024/01/15/aukcije_db_20240115_120000.sql.gz ./backups/

# Restore
./scripts/restore-db.sh ./backups/aukcije_db_20240115_120000.sql.gz
```

---

## Scenario 3: Redis Failure

### Symptoms
- Cache misses
- Session loss
- Queue processing stopped

### Recovery Steps

```bash
# 1. Check Redis status
docker compose ps redis
docker compose logs redis

# 2. Restart Redis
docker compose restart redis

# 3. Clear cache if corrupted
docker compose exec redis redis-cli FLUSHALL

# 4. Restart Horizon (queue worker)
docker compose restart horizon

# 5. Verify queue processing
docker compose exec app php artisan horizon:status
```

---

## Scenario 4: Full Infrastructure Loss

### Recovery Steps

```bash
# 1. Provision new server (AWS/DigitalOcean)

# 2. Install Docker
curl -fsSL https://get.docker.com | sh

# 3. Clone repository
git clone <repository-url> /var/www/aukcije
cd /var/www/aukcije

# 4. Configure environment
cp .env.production .env
# Edit .env with correct values

# 5. Restore database
./scripts/restore-db.sh

# 6. Start services
docker compose -f docker-compose.prod.yml up -d

# 7. Verify all services
docker compose ps

# 8. Update DNS if needed
```

---

## Scenario 5: Security Incident

### Symptoms
- Unauthorized access detected
- Suspicious activity in logs
- Data breach indicators

### Immediate Actions

```bash
# 1. Enable maintenance mode
docker compose exec app php artisan down

# 2. Rotate all secrets
# - APP_KEY
# - Database passwords
# - API keys
# - JWT secrets

# 3. Review access logs
docker compose logs app | grep -E "ERROR|WARN|unauthorized"

# 4. Check for unauthorized users
docker compose exec app php artisan tinker
>>> User::where('role', 'admin')->get();

# 5. Notify stakeholders
# - Legal team
# - Affected users (if data breach)
# - Authorities (if required by law)
```

---

## Backup Verification

### Weekly Automated Test

```bash
# 1. Download latest backup
aws s3 cp s3://aukcije-backups/database/latest.sql.gz ./test-restore/

# 2. Restore to staging
docker compose -f docker-compose.staging.yml up -d
./scripts/restore-db.sh ./test-restore/latest.sql.gz

# 3. Verify data integrity
# - Row counts match production
# - Critical queries return expected results

# 4. Cleanup
docker compose -f docker-compose.staging.yml down
rm ./test-restore/latest.sql.gz
```

---

## Communication Plan

### During Incident

1. **Detection** (0-5 min)
   - Monitoring alerts
   - User reports

2. **Assessment** (5-15 min)
   - Identify scope
   - Assign severity (P0-P3)

3. **Communication** (15-30 min)
   - Internal: Slack #incidents
   - External: Status page update

4. **Resolution** (varies)
   - Regular updates every 30 min
   - Status page updates

5. **Post-Mortem** (within 48h)
   - Document root cause
   - Action items to prevent recurrence

---

## Testing Schedule

| Test Type | Frequency | Last Test | Next Test |
|-----------|-----------|-----------|-----------|
| Backup restore | Weekly | [Date] | [Date] |
| Full DR drill | Quarterly | [Date] | [Date] |
| Security incident | Quarterly | [Date] | [Date] |

---

## Document History

| Version | Date | Author | Changes |
|---------|------|--------|---------|
| 1.0 | [Date] | [Name] | Initial version |
