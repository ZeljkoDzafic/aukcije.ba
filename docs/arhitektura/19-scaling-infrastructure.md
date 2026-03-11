# 19 - Scaling & Infrastructure Strategy

## Growth Stages

```
Stage 1: MVP Launch          Stage 2: Regional Growth     Stage 3: Scale
0-2K users                   2K-20K users                 20K-200K users
100 active auctions          1K active auctions           10K+ active auctions
$50/mo infra                 $200-500/mo infra            $1K-5K/mo infra
Single server                Multi-server                 Clustered
```

---

## Stage 1: Single Server Architecture (MVP)

```
┌────────────────────────────────────────┐
│          Hetzner CPX31 ($16/mo)        │
│          4 vCPU, 8GB RAM, 160GB       │
│                                        │
│  ┌──────────────────────────────────┐  │
│  │  Docker Compose                   │  │
│  │                                   │  │
│  │  Nginx (:443) ─→ PHP-FPM (:9000) │  │
│  │                                   │  │
│  │  Reverb (:8080)                   │  │
│  │  Horizon (queue worker × 3)       │  │
│  │  Scheduler (cron)                 │  │
│  │                                   │  │
│  │  PostgreSQL (:5432) — 50GB data   │  │
│  │  Redis (:6379) — 512MB            │  │
│  │  Meilisearch (:7700) — 1GB       │  │
│  └──────────────────────────────────┘  │
│                                        │
│  Cloudflare (CDN + WAF + DDoS)        │
│  S3 (images)                           │
└────────────────────────────────────────┘

Backup: Daily pg_dump → S3
SSL: Let's Encrypt via Certbot
```

### Bottleneck Indicators (when to scale)

| Signal | Threshold | Action |
|--------|-----------|--------|
| CPU sustained > 70% | 5+ min | Move to Stage 2 |
| Memory > 80% | Sustained | Increase RAM or split |
| DB connections > 80 | Peak | Add PgBouncer |
| Redis memory > 400MB | Sustained | Dedicated Redis |
| Bid latency p99 > 500ms | 5+ min | Split DB + app |
| WebSocket connections > 500 | Peak | Dedicated Reverb |

---

## Stage 2: Multi-Server Architecture

```
                 Cloudflare CDN + WAF
                        │
                 ┌──────┴──────┐
                 │   Nginx LB   │  (Hetzner LB - $6/mo)
                 └──┬───────┬──┘
                    │       │
          ┌─────────┘       └─────────┐
          │                           │
┌─────────┴─────────┐     ┌──────────┴────────┐
│   App Server 1     │     │   App Server 2    │
│   CPX21 ($8/mo)    │     │   CPX21 ($8/mo)   │
│   PHP-FPM          │     │   PHP-FPM         │
│   Horizon × 3      │     │   Horizon × 3     │
│   Scheduler         │     │                   │
└─────────┬──────────┘     └──────────┬────────┘
          │                           │
          └──────────┬────────────────┘
                     │
     ┌───────────────┼───────────────┐
     │               │               │
┌────┴────┐   ┌──────┴──────┐  ┌────┴────────┐
│  DB     │   │   Redis     │  │  Reverb     │
│  CPX21  │   │   Dedicated │  │  Dedicated  │
│  ($8)   │   │   ($5)      │  │  ($5)       │
│         │   │             │  │             │
│ PgBouncer│  │  Sentinel   │  │  Sticky WS  │
│ Read    │   │  (failover) │  │  sessions   │
│ replica │   └─────────────┘  └─────────────┘
└─────────┘

┌──────────────┐
│  Meilisearch │  ($5/mo dedicated or same as app)
└──────────────┘

Total: ~$45-55/mo
```

### Key Changes from Stage 1

| Component | Before | After |
|-----------|--------|-------|
| App servers | 1 | 2 (load balanced) |
| Database | Co-located | Dedicated server + PgBouncer |
| Redis | Co-located | Dedicated + Sentinel |
| Reverb | Co-located | Dedicated server |
| Sessions | File/cookie | Redis (shared across app servers) |
| Cache | Local | Redis (shared) |
| Queue | Single worker | 6 workers (3 per server) |

### PgBouncer Configuration

```ini
[databases]
aukcije = host=db-server dbname=aukcije

[pgbouncer]
listen_port = 6432
pool_mode = transaction       # Important: transaction mode for Laravel
max_client_conn = 200
default_pool_size = 25
min_pool_size = 5
reserve_pool_size = 5
```

### Read Replica Setup

```php
// config/database.php
'pgsql' => [
    'read' => [
        'host' => [env('DB_READ_HOST', env('DB_HOST'))],
    ],
    'write' => [
        'host' => [env('DB_HOST')],
    ],
    // ... connection details
],

// Usage: Read-heavy queries automatski idu na repliku
// Bids/writes uvijek idu na primary
```

---

## Stage 3: Clustered Architecture

```
                    Cloudflare (Enterprise)
                    │
                    Global Load Balancer
                    │
        ┌───────────┼───────────┐
        │           │           │
   App Cluster   WebSocket    API Cluster
   (3× App)      Cluster      (2× API)
   (Kubernetes    (2× Reverb)  (mobile/seller API)
    or Docker
    Swarm)
        │           │           │
        └───────────┼───────────┘
                    │
    ┌───────────────┼───────────────┐
    │               │               │
  PostgreSQL     Redis Cluster    Meilisearch
  Primary +      (3 nodes)       Cluster
  2 Read         Sentinel        (if needed)
  Replicas
    │
  PgBouncer
  (connection pooling)

Queue Workers: 12+ (dedicated nodes)
Scheduler: 1 (leader election)
```

### Database Partitioning

```sql
-- Partition bids table by month (high-write table)
CREATE TABLE bids (
    id UUID NOT NULL,
    auction_id UUID NOT NULL,
    user_id UUID NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT now()
) PARTITION BY RANGE (created_at);

CREATE TABLE bids_2024_01 PARTITION OF bids
    FOR VALUES FROM ('2024-01-01') TO ('2024-02-01');
CREATE TABLE bids_2024_02 PARTITION OF bids
    FOR VALUES FROM ('2024-02-01') TO ('2024-03-01');
-- ... auto-create partitions via cron job

-- Benefits:
-- Faster queries on recent bids (smaller index scan)
-- Easy archival: DROP old partition instead of DELETE
-- Better vacuum performance
```

### Redis Cluster Configuration

```
Topology: 3 master + 3 replica (6 nodes)

Key distribution:
  auction:{id}:price    → Shard by auction_id
  auction:{id}:lock     → Same shard as price (co-located)
  user:{id}:session     → Shard by user_id
  feature_flags         → Single key, any shard
  queue:*               → Dedicated shard
```

### WebSocket Scaling

```
Problem: Reverb is single-process
Solution: Multiple Reverb instances + Redis pub/sub

            ┌─── Reverb 1 ◄──► Redis Pub/Sub
User A ─────┤
            └─── Reverb 2 ◄──► Redis Pub/Sub
User B ─────┐
            └─── Reverb 1 ◄──► Redis Pub/Sub

When BidPlaced event fires:
1. Laravel publishes to Redis channel
2. ALL Reverb instances receive
3. Each sends to its connected clients
4. All users see update regardless of which Reverb they're on
```

---

## Caching Strategy (Multi-Layer)

```
Layer 1: Browser Cache (static assets)
  - CSS/JS/fonts: Cache-Control: public, max-age=31536000, immutable
  - Images: Cache-Control: public, max-age=86400
  - HTML pages: no-cache (always validate)

Layer 2: Cloudflare CDN
  - Static assets cached at edge
  - API responses: no-cache (pass through)
  - Page rules for /auctions/[id] → 60s edge cache

Layer 3: Application Cache (Redis)
  - Category tree: 1 hour TTL
  - Feature flags: 5 min TTL
  - User trust scores: 1 hour TTL, invalidate on new rating
  - Auction current price: NO cache (always real-time from Redis key)
  - Search results: 30 sec TTL per query
  - Bid increments: 24 hour TTL (rarely changes)

Layer 4: Database Query Cache
  - Prepared statements cached by PostgreSQL
  - pg_stat_statements for query analysis
```

### Cache Invalidation Strategy

```php
// Tag-based cache invalidation
Cache::tags(['auction', "auction:{$id}"])->put("auction:{$id}:detail", $data, 300);

// On bid placed:
Cache::tags(["auction:{$id}"])->flush();

// On category change:
Cache::tags(['categories'])->flush();

// Never cache:
// - Bid placement results
// - Wallet balance
// - Payment status
// - Real-time auction price (lives in Redis key, not cache)
```

---

## Queue Scaling Strategy

### Horizon Configuration

```php
// config/horizon.php
'environments' => [
    'production' => [
        'supervisor-bids' => [
            'connection' => 'redis',
            'queue' => ['bids'],
            'balance' => 'auto',
            'minProcesses' => 3,
            'maxProcesses' => 10,
            'tries' => 1,              // Bids: no retry (idempotent check)
            'timeout' => 10,
        ],
        'supervisor-notifications' => [
            'connection' => 'redis',
            'queue' => ['notifications', 'emails'],
            'balance' => 'auto',
            'minProcesses' => 2,
            'maxProcesses' => 5,
            'tries' => 3,
            'timeout' => 60,
        ],
        'supervisor-default' => [
            'connection' => 'redis',
            'queue' => ['default', 'search-index', 'images'],
            'balance' => 'auto',
            'minProcesses' => 2,
            'maxProcesses' => 5,
            'tries' => 3,
            'timeout' => 120,
        ],
    ],
],
```

### Queue Priority

| Queue | Priority | Max Timeout | Retry | Failure Action |
|-------|----------|-------------|-------|---------------|
| `bids` | Critical | 10s | 1 (no retry) | Log + alert |
| `payments` | High | 30s | 3 | Log + alert + manual review |
| `notifications` | Medium | 60s | 3 | Log only |
| `emails` | Medium | 60s | 3 | Retry, then dead letter |
| `search-index` | Low | 120s | 3 | Retry, eventually consistent |
| `images` | Low | 120s | 3 | Retry |
| `default` | Normal | 120s | 3 | Standard |

---

## Image Processing Pipeline

```
Upload (5MB max JPEG/PNG)
    │
    ▼
Validate (magic bytes, dimensions, no EXIF malware)
    │
    ▼
Process (queued job):
    ├── Strip EXIF metadata (privacy)
    ├── Auto-orient (based on EXIF rotation)
    ├── Resize:
    │   ├── Original: max 2048px longest edge
    │   ├── Large: 1024px (auction detail)
    │   ├── Medium: 600px (auction card)
    │   ├── Thumb: 200px (search results, admin)
    │   └── Blur: 20px (placeholder for lazy loading)
    ├── Convert to WebP (50-70% smaller than JPEG)
    ├── Fallback JPEG (for old browsers)
    └── Upload to S3 with CDN-friendly paths
    │
    ▼
Serve:
    S3 → CloudFront CDN → Browser
    URL: /images/auctions/{auction_id}/{size}/{hash}.webp

    <picture>
      <source srcset="/images/auctions/.../medium/abc.webp" type="image/webp">
      <img src="/images/auctions/.../medium/abc.jpg" loading="lazy">
    </picture>
```

---

## Cost Projections

| Stage | Users | Auctions | Infra Cost | Revenue Target |
|-------|-------|----------|-----------|----------------|
| MVP | 200 | 100 | $50/mo | Break-even |
| Stage 1 | 2K | 500 | $80/mo | $500/mo (commissions) |
| Stage 2 | 10K | 2K | $200/mo | $3K/mo |
| Stage 3 | 50K | 10K | $1K/mo | $15K/mo |
| Stage 4 | 200K | 50K | $5K/mo | $50K+/mo |

---

## Migration Playbook (Stage 1 → Stage 2)

```
1. Pre-migration (1 week before):
   □ Provision new servers (DB, Redis, App 2)
   □ Set up PgBouncer on DB server
   □ Set up Redis Sentinel
   □ Test load balancer configuration
   □ Update DNS TTL to 60s

2. Migration night (off-peak, 02:00-06:00):
   □ Enable maintenance mode
   □ Final pg_dump backup
   □ Migrate DB to dedicated server (pg_restore)
   □ Update .env on both app servers
   □ Start App Server 2
   □ Configure load balancer
   □ Disable maintenance mode
   □ Monitor for 1 hour

3. Post-migration (next day):
   □ Set up read replica (streaming replication)
   □ Verify replica sync
   □ Update read connection in Laravel config
   □ Restore DNS TTL
   □ Decommission old single server (after 1 week)
```
