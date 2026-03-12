# Performance Optimization Guide

## Database Optimization

### Indexes

Add these indexes to improve query performance:

```sql
-- Auctions
CREATE INDEX auctions_status_ends_at_idx ON auctions(status, ends_at);
CREATE INDEX auctions_category_id_idx ON auctions(category_id);
CREATE INDEX auctions_seller_id_idx ON auctions(seller_id);
CREATE INDEX auctions_current_price_idx ON auctions(current_price);
CREATE INDEX auctions_created_at_idx ON auctions(created_at);

-- Bids
CREATE INDEX bids_auction_id_idx ON bids(auction_id);
CREATE INDEX bids_user_id_idx ON bids(user_id);
CREATE INDEX bids_is_winning_idx ON bids(is_winning);
CREATE INDEX bids_created_at_idx ON bids(created_at);

-- Users
CREATE INDEX users_email_idx ON users(email);
CREATE INDEX users_role_idx ON model_has_roles(role_id);

-- Orders
CREATE INDEX orders_buyer_id_idx ON orders(buyer_id);
CREATE INDEX orders_seller_id_idx ON orders(seller_id);
CREATE INDEX orders_status_idx ON orders(status);

-- Wallet transactions
CREATE INDEX wallet_transactions_user_id_idx ON wallet_transactions(user_id);
CREATE INDEX wallet_transactions_type_idx ON wallet_transactions(type);
```

### Query Optimization

```php
// Use eager loading to prevent N+1 queries
Auction::with(['seller', 'category', 'primaryImage', 'winningBid'])->active()->get();

// Use select to only fetch needed columns
Auction::select(['id', 'title', 'current_price', 'ends_at'])->active()->get();

// Use chunk for large datasets
Auction::active()->chunk(100, function ($auctions) {
    // Process auctions
});

// Use database indexes
Auction::where('status', 'active')
    ->where('ends_at', '>', now())
    ->orderBy('ends_at')
    ->limit(20)
    ->get();
```

## Caching Strategy

### Redis Cache Configuration

```php
// Cache auction data (5 minutes)
$auction = Cache::remember(
    "auction:{$id}",
    now()->addMinutes(5),
    fn() => Auction::with(['seller', 'images'])->find($id)
);

// Cache category tree (1 hour)
$categories = Cache::remember(
    'categories:tree',
    now()->addHour(),
    fn() => Category::with('children')->whereNull('parent_id')->get()
);

// Cache user trust score (30 minutes)
$trustScore = Cache::remember(
    "user:{$userId}:trust_score",
    now()->addMinutes(30),
    fn() => $user->trust_score
);

// Cache feature flags (1 hour)
$flags = Cache::remember(
    'feature_flags',
    now()->addHour(),
    fn() => FeatureFlag::all()->pluck('enabled', 'name')
);
```

### Cache Tags

```php
// Tag related cache entries
Cache::tags(['auctions', "auction:{$id}"])->put('auction:data', $data);

// Clear all auction cache
Cache::tags(['auctions'])->flush();

// Clear specific auction cache
Cache::tags(["auction:{$id}"])->flush();
```

## Frontend Optimization

### Vite Build Optimization

```javascript
// vite.config.js
export default defineConfig({
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['vue', 'alpinejs'],
                    charts: ['chart.js'],
                },
            },
        },
        chunkSizeWarningLimit: 1000,
    },
});
```

### Image Optimization

```php
// Use WebP format
use Intervention\Image\Facades\Image;

$image = Image::make($uploadedFile);
$image->encode('webp', 80); // 80% quality
$image->resize(800, null, function ($constraint) {
    $constraint->aspectRatio();
});
$image->save();

// Generate thumbnails
$image->fit(400, 400)->save('thumbnail.webp');
```

### Lazy Loading

```html
<!-- Images -->
<img src="{{ $auction->image_url }}" loading="lazy" alt="{{ $auction->title }}">

<!-- Vue components -->
<component :is="HeavyComponent" v-if="isVisible" />
```

## Queue Optimization

### Queue Configuration

```php
// config/queue.php
'horizon' => [
    'environments' => [
        'production' => [
            'supervisor-1' => [
                'connection' => 'redis',
                'queue' => ['default', 'emails', 'notifications', 'bidding'],
                'balance' => 'auto',
                'minProcesses' => 3,
                'maxProcesses' => 20,
                'tries' => 3,
                'timeout' => 60,
            ],
        ],
    ],
],
```

### Job Batching

```php
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;

$batch = Bus::batch([
    new ProcessAuctionJob($auction1),
    new ProcessAuctionJob($auction2),
    // ...
])
->then(fn (Batch $batch) => Log::info('Batch completed'))
->catch(fn (Batch $batch, Throwable $e) => Log::error('Batch failed'))
->finally(fn (Batch $batch) => Log::info('Batch finished'))
->dispatch();
```

## WebSocket Optimization

### Connection Limits

```php
// Limit concurrent connections per user
$maxConnections = config('reverb.max_connections_per_user', 5);
$currentConnections = Cache::get("user:{$userId}:ws_connections", 0);

if ($currentConnections >= $maxConnections) {
    // Reject connection
}
```

### Event Throttling

```php
// Throttle bid events
RateLimiter::for('bidding', function ($user) {
    return Limit::perMinute(10)->by($user->id);
});

// In bid handler
if (RateLimiter::tooManyAttempts('bidding:' . $user->id, 10)) {
    return response('Too many requests', 429);
}
```

## API Optimization

### Response Compression

```php
// Enable gzip compression
// config/app.php
'middleware' => [
    \Illuminate\Http\Middleware\SetCacheHeaders::class,
    \Illuminate\Http\Middleware\HandleCors::class,
    // Add compression middleware
    \App\Http\Middleware\CompressResponses::class,
],
```

### API Pagination

```php
// Always paginate API responses
return AuctionResource::collection(
    Auction::active()->paginate(20)
);

// Allow client to specify limit (max 100)
$limit = min($request->get('limit', 20), 100);
```

## Monitoring

### Query Logging

```php
// Enable query logging in development
DB::enableQueryLog();

// Log slow queries (over 100ms)
DB::listen(function ($query) {
    if ($query->time > 100) {
        Log::warning('Slow query', [
            'sql' => $query->sql,
            'bindings' => $query->bindings,
            'time' => $query->time,
        ]);
    }
});
```

### Performance Metrics

```php
// Track response times
$start = microtime(true);
// ... process request
$duration = (microtime(true) - $start) * 1000;

if ($duration > 500) {
    Log::warning('Slow response', [
        'url' => request()->url(),
        'duration_ms' => $duration,
    ]);
}
```

## Production Checklist

- [ ] Enable OPcache
- [ ] Enable Redis for sessions and cache
- [ ] Configure Horizon for queue management
- [ ] Enable database query cache
- [ ] Configure CDN for static assets
- [ ] Enable HTTP/2
- [ ] Configure browser caching headers
- [ ] Enable gzip/brotli compression
- [ ] Optimize images (WebP format)
- [ ] Minify CSS and JS
- [ ] Use database connection pooling
- [ ] Configure auto-scaling rules
