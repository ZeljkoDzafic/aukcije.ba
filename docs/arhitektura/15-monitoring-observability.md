# 15 - Monitoring & Observability

## Monitoring Architecture

```
┌──────────────────────────────────────────────────────────┐
│                     ALERTING                              │
│  PagerDuty / Slack / SMS / Email                         │
└──────────────────────┬───────────────────────────────────┘
                       │
┌──────────────────────┴───────────────────────────────────┐
│                   DASHBOARDS                              │
│  Grafana (infra) + Horizon (queues) + Custom (business)  │
└────────┬─────────────┬──────────────────┬────────────────┘
         │             │                  │
┌────────┴────┐ ┌──────┴──────┐ ┌────────┴────────┐
│  METRICS    │ │   LOGS      │ │   TRACES        │
│  Prometheus │ │ Loki / ELK  │ │ Sentry / Jaeger │
└────────┬────┘ └──────┬──────┘ └────────┬────────┘
         │             │                  │
┌────────┴─────────────┴──────────────────┴────────────────┐
│                  APPLICATION LAYER                         │
│  Laravel (PHP-FPM) + Reverb (WS) + Horizon (Queue)       │
│  PostgreSQL + Redis + Meilisearch + Nginx                 │
└──────────────────────────────────────────────────────────┘
```

## Three Pillars

### 1. Metrics (Prometheus + Grafana)

#### Infrastructure Metrics

| Metrika | Alert Threshold | Severity |
|---------|----------------|----------|
| CPU usage | > 80% za 5 min | P2 |
| Memory usage | > 85% | P2 |
| Disk usage | > 90% | P1 |
| Disk I/O wait | > 50ms avg | P2 |
| Network errors | > 1% packet loss | P2 |

#### Application Metrics (Custom)

```php
// Laravel Prometheus Exporter
// Namespace: aukcije_

// HTTP metrics
aukcije_http_requests_total{method, route, status}
aukcije_http_request_duration_seconds{method, route}
aukcije_http_requests_in_progress{method}

// Bidding engine metrics
aukcije_bids_total{status="success|failed|rejected"}
aukcije_bid_processing_duration_seconds
aukcije_active_auctions_count
aukcije_auction_endings_total{result="sold|unsold|cancelled"}
aukcije_proxy_bids_triggered_total
aukcije_anti_sniping_extensions_total
aukcije_concurrent_bid_conflicts_total

// WebSocket metrics
aukcije_websocket_connections_active
aukcije_websocket_messages_sent_total{channel}
aukcije_websocket_errors_total

// Queue metrics (via Horizon)
aukcije_jobs_processed_total{queue, job}
aukcije_jobs_failed_total{queue, job}
aukcije_queue_wait_time_seconds{queue}
aukcije_queue_size{queue}

// Database metrics
aukcije_db_connections_active
aukcije_db_query_duration_seconds{query_type}
aukcije_db_slow_queries_total

// Redis metrics
aukcije_redis_connections_active
aukcije_redis_memory_used_bytes
aukcije_redis_lock_acquisitions_total{result="success|timeout"}
aukcije_redis_lock_wait_seconds

// Business metrics
aukcije_registrations_total{type="buyer|seller"}
aukcije_gmv_total_bam                    // Gross Merchandise Value
aukcije_commission_earned_bam
aukcije_escrow_held_bam
aukcije_disputes_total{status}
aukcije_kyc_verifications_total{status}
aukcije_payments_total{gateway, status}
```

#### Grafana Dashboards

| Dashboard | Panels | Audience |
|-----------|--------|----------|
| **System Overview** | CPU, Memory, Disk, Network, Uptime | DevOps |
| **Bidding Engine** | Bids/min, Latency, Conflicts, Active Auctions | Dev + Business |
| **WebSocket Health** | Active connections, Messages/sec, Errors | Dev |
| **Queue Monitor** | Job throughput, Wait time, Failed jobs | Dev |
| **Database** | Connections, Query time, Slow queries, Cache hit ratio | DBA |
| **Business KPIs** | GMV, Registrations, Conversion, Disputes | Business |
| **Trust & Safety** | KYC queue, Disputes, Fraud flags, Moderation queue | Moderators |

### 2. Logs (Loki / ELK Stack)

#### Structured Logging

```php
// config/logging.php — JSON structured logs
'channels' => [
    'structured' => [
        'driver' => 'single',
        'path' => storage_path('logs/app.json'),
        'formatter' => JsonFormatter::class,
    ],
],

// Log format
{
    "timestamp": "2024-03-15T14:32:01.234Z",
    "level": "info",
    "message": "Bid placed",
    "context": {
        "auction_id": "uuid",
        "user_id": "uuid",
        "amount": 125.50,
        "previous_price": 120.00,
        "is_proxy": false,
        "processing_time_ms": 42,
        "anti_sniping_triggered": false
    },
    "request": {
        "id": "req-uuid",
        "ip": "192.168.x.x",
        "method": "POST",
        "path": "/auctions/uuid/bid"
    }
}
```

#### Log Categories & Levels

| Category | Level | Retention | Primjer |
|----------|-------|-----------|---------|
| `auth.*` | INFO | 90 dana | Login, logout, failed login, MFA |
| `auction.*` | INFO | 180 dana | Created, ended, cancelled, extended |
| `bid.*` | INFO | 180 dana | Placed, proxy triggered, rejected |
| `payment.*` | INFO | 7 godina | Processed, failed, refunded |
| `escrow.*` | INFO | 7 godina | Held, released, refunded |
| `security.*` | WARNING | 1 godina | Rate limited, suspicious activity |
| `error.*` | ERROR | 90 dana | Exceptions, failures |
| `admin.*` | INFO | 1 godina | All admin actions (audit trail) |

#### Sensitive Data Redaction

```php
// Automatska redakcija u logu
class LogRedactor
{
    private array $patterns = [
        '/password["\s:=]+["\'](.*?)["\']/i' => 'password: "***REDACTED***"',
        '/\b\d{13,19}\b/' => '***CARD***',                    // card numbers
        '/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/' => '***EMAIL***',
        '/token["\s:=]+["\'](.*?)["\']/i' => 'token: "***REDACTED***"',
    ];
}
```

### 3. Traces (Sentry + OpenTelemetry)

```php
// Sentry za error tracking + performance monitoring
// config/sentry.php
'dsn' => env('SENTRY_DSN'),
'traces_sample_rate' => 0.2,        // 20% transaction sampling
'profiles_sample_rate' => 0.1,       // 10% profiling
'send_default_pii' => false,         // GDPR compliant

// Custom spans za bidding
Sentry\startTransaction(['name' => 'bid.place']);
$span = Sentry\startSpan(['op' => 'redis.lock']);
// ... Redis lock acquisition
$span->finish();
$span = Sentry\startSpan(['op' => 'db.transaction']);
// ... DB transaction
$span->finish();
```

#### Critical Transaction Traces

| Transaction | Span Breakdown | Target p99 |
|-------------|---------------|------------|
| `bid.place` | lock(5ms) → validate(2ms) → db_write(10ms) → proxy_check(5ms) → broadcast(3ms) | < 100ms |
| `auction.end` | find_winner(5ms) → create_order(10ms) → escrow_hold(15ms) → notify(async) | < 200ms |
| `auction.search` | meilisearch(20ms) → hydrate(10ms) → render(30ms) | < 150ms |
| `page.auction_detail` | db_read(10ms) → cache_check(2ms) → render(50ms) | < 200ms |

---

## Alerting Rules

### P0 — Critical (SMS + Slack + PagerDuty, immediate)

| Alert | Condition | Action |
|-------|-----------|--------|
| Platform Down | Health check fails 3x consecutive | Wake on-call dev |
| Database Down | PostgreSQL connection refused | Failover + investigate |
| Payment Failure Spike | > 50% payment failures in 5 min | Disable payments, investigate |
| Data Breach Indicator | Unusual data export patterns | Isolate, investigate, notify DPO |
| Bidding Engine Frozen | 0 successful bids in 5 min (during active hours) | Restart services |

### P1 — High (Slack + Email, < 1 hour response)

| Alert | Condition |
|-------|-----------|
| High Error Rate | > 5% HTTP 5xx responses in 5 min |
| Slow Bids | p99 bid latency > 500ms for 10 min |
| Queue Backlog | > 1000 pending jobs for 15 min |
| Redis Memory | > 80% maxmemory |
| WebSocket Disconnections | > 100 disconnections in 1 min |
| Disk Space | > 90% usage |
| Certificate Expiry | SSL cert expires in < 14 days |
| Failed Jobs Spike | > 50 failed jobs in 1 hour |

### P2 — Medium (Slack, < 4 hours)

| Alert | Condition |
|-------|-----------|
| Slow Queries | > 10 queries > 1s in 5 min |
| Meilisearch Lag | Index out of sync > 5 min |
| High Memory | > 80% for 30 min |
| Elevated 4xx | > 20% 4xx responses |
| Email Delivery | Bounce rate > 5% |
| Low Cache Hit | Redis hit ratio < 80% |

---

## Health Check Endpoints

```php
// GET /health — public, za UptimeRobot
Route::get('/health', function () {
    $checks = [
        'app' => true,
        'database' => DB::connection()->getPdo() !== null,
        'redis' => Cache::store('redis')->put('health', true, 5),
        'meilisearch' => Http::get(config('scout.meilisearch.host') . '/health')->ok(),
        'reverb' => /* WebSocket connection test */,
        'queue' => Cache::get('horizon:status') === 'running',
    ];

    $healthy = !in_array(false, $checks);
    return response()->json([
        'status' => $healthy ? 'healthy' : 'degraded',
        'checks' => $checks,
        'timestamp' => now()->toIso8601String(),
    ], $healthy ? 200 : 503);
});

// GET /health/detailed — auth required (admin only)
// Returns: DB connection pool, Redis memory, queue sizes, uptime, version
```

---

## SLA Definitions

| Metric | Target (MVP) | Target (Mature) |
|--------|-------------|-----------------|
| Uptime | 99.5% (43h downtime/yr) | 99.9% (8.7h/yr) |
| Bid Processing | p99 < 500ms | p99 < 200ms |
| Page Load (auction detail) | p50 < 2s, p99 < 5s | p50 < 1s, p99 < 3s |
| Search Results | p50 < 200ms | p50 < 100ms |
| WebSocket Delivery | < 1s from bid to all clients | < 500ms |
| Planned Maintenance | 4h/month (off-peak, 02:00-06:00) | 2h/month |
| RTO (Recovery Time) | < 4 hours | < 1 hour |
| RPO (Recovery Point) | < 24 hours | < 1 hour |

---

## Disaster Recovery

### Backup Strategy (Enhanced)

| Šta | Kako | Frekvencija | Retencija | Verifikacija |
|-----|------|-------------|-----------|-------------|
| PostgreSQL (full) | pg_dump → S3 (encrypted) | Daily 00:00 | 30 dana | Weekly restore test |
| PostgreSQL (WAL) | Continuous archiving → S3 | Continuous | 7 dana | Monthly PITR test |
| Redis (AOF) | Copy → S3 | Every 6h | 7 dana | On restore |
| Application code | Git (GitHub) | On every push | Permanent | N/A |
| Uploaded images | Already on S3 | N/A | N/A | N/A |
| Config / secrets | Encrypted backup → S3 | On change | 90 dana | On restore |
| Meilisearch index | Dump → S3 | Daily 03:00 | 7 dana | Reindex from DB |

### Recovery Procedures

```
Scenario 1: Application Server Failure
  1. Cloudflare → maintenance page (auto, health check fail)
  2. Spin up new server from Docker image (< 10 min)
  3. Connect to existing DB + Redis
  4. Verify health check
  5. Cloudflare → resume traffic

Scenario 2: Database Corruption
  1. Failover to read replica (if exists) — read-only mode
  2. Restore from latest pg_dump or PITR
  3. Verify data integrity (row counts, checksums)
  4. Resume write operations
  5. Re-sync any lost transactions from payment gateway webhooks

Scenario 3: Redis Failure
  1. Application falls back to database queries (degraded, slower)
  2. Restart Redis, restore from AOF if needed
  3. Warm cache (auction prices, feature flags, sessions)
  4. Verify bid lock mechanism functional

Scenario 4: Full Infrastructure Loss
  1. DNS → maintenance page (Cloudflare)
  2. Provision new infrastructure (Terraform / manual)
  3. Restore DB from S3 backup
  4. Deploy application from Git
  5. Restore Redis from AOF
  6. Reindex Meilisearch
  7. Verify all services + smoke tests
  8. Resume traffic
  Target: < 4 hours (RTO)
```

---

## Runbooks

### Runbook: High Bid Latency

```
Symptom: p99 bid latency > 500ms
Check:
  1. Redis: SLOWLOG GET 10 — check for slow commands
  2. Redis: INFO memory — check memory pressure
  3. PostgreSQL: pg_stat_activity — check for blocking queries
  4. Horizon: /horizon — check queue worker count
  5. PHP-FPM: pm.status — check active processes
Fix:
  - If Redis slow: MEMORY PURGE, restart if needed
  - If DB blocking: Kill long queries, add missing index
  - If PHP saturated: Scale workers (pm.max_children)
  - If Horizon backlogged: Scale workers
```

### Runbook: WebSocket Disconnections Spike

```
Symptom: > 100 disconnections in 1 min
Check:
  1. Reverb logs: check for OOM or crash
  2. Nginx: check proxy_read_timeout
  3. Cloudflare: check WebSocket limit hit
  4. Server: check file descriptor limit (ulimit -n)
Fix:
  - Restart Reverb: php artisan reverb:restart
  - Increase timeout: proxy_read_timeout 3600s
  - Increase fd limit: ulimit -n 65535
  - Scale: multiple Reverb instances behind load balancer
```
