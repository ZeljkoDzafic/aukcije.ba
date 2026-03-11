# 17 - Testing Strategy

## Testing Pyramid

```
                    ┌───────────┐
                    │  E2E (10) │  Dusk browser tests
                   ┌┴───────────┴┐
                   │ Feature (50) │  HTTP/API tests
                  ┌┴──────────────┴┐
                  │  Unit (200+)    │  Service, Model, Helper tests
                 ┌┴─────────────────┴┐
                 │  Static Analysis   │  PHPStan Level 6, Pint
                └─────────────────────┘
```

## Testing Stack

| Tool | Svrha |
|------|-------|
| **Pest PHP** | Test framework (BDD-style, built on PHPUnit) |
| **Laravel Dusk** | Browser / E2E testing |
| **PHPStan** | Static analysis (Level 6) |
| **Laravel Pint** | Code style (PSR-12) |
| **Faker** | Test data generation |
| **Mockery** | Mocking (Redis, external APIs) |
| **k6** | Load / performance testing |
| **Vitest** | Vue.js component tests |

---

## Unit Tests

### BiddingService Tests (Critical — 100% coverage required)

```php
// tests/Unit/Services/BiddingServiceTest.php

describe('BiddingService::placeBid', function () {

    // Happy path
    it('places a valid bid', function () {
        $auction = Auction::factory()->active()->create(['current_price' => 100]);
        $user = User::factory()->buyer()->create();

        $bid = $this->biddingService->placeBid($auction, $user, 105);

        expect($bid->amount)->toBe(105.0);
        expect($auction->fresh()->current_price)->toBe(105.0);
        expect($auction->fresh()->bids_count)->toBe(1);
    });

    // Validation
    it('rejects bid below minimum increment', function () {
        $auction = Auction::factory()->active()->create(['current_price' => 100]);
        $user = User::factory()->buyer()->create();

        expect(fn() => $this->biddingService->placeBid($auction, $user, 101))
            ->toThrow(BidTooLowException::class);
        // Minimum bid at 100 BAM = 105 (increment = 5)
    });

    it('rejects bid on own auction', function () {
        $seller = User::factory()->seller()->create();
        $auction = Auction::factory()->active()->create(['seller_id' => $seller->id]);

        expect(fn() => $this->biddingService->placeBid($auction, $seller, 200))
            ->toThrow(CannotBidOwnAuctionException::class);
    });

    it('rejects bid on inactive auction', function () {
        $auction = Auction::factory()->create(['status' => 'finished']);
        $user = User::factory()->buyer()->create();

        expect(fn() => $this->biddingService->placeBid($auction, $user, 50))
            ->toThrow(AuctionNotActiveException::class);
    });

    it('rejects bid on cancelled auction');
    it('rejects bid from banned user');
    it('rejects bid from unverified email');

    // Proxy bidding
    it('triggers proxy bid from another user', function () {
        $auction = Auction::factory()->active()->create(['current_price' => 100]);
        $proxyUser = User::factory()->buyer()->create();
        $bidder = User::factory()->buyer()->create();

        // Set proxy bid
        ProxyBid::factory()->create([
            'auction_id' => $auction->id,
            'user_id' => $proxyUser->id,
            'max_amount' => 200,
        ]);

        // Place regular bid
        $this->biddingService->placeBid($auction, $bidder, 105);

        // Proxy should auto-bid to 110
        expect($auction->fresh()->current_price)->toBe(110.0);
        expect(Bid::where('user_id', $proxyUser->id)->exists())->toBeTrue();
    });

    it('exhausts proxy bid when outbid above max');
    it('handles two proxy bids competing');
    it('proxy bid does not bid against itself');

    // Anti-sniping
    it('extends auction when bid in last 2 minutes', function () {
        $auction = Auction::factory()->active()->create([
            'ends_at' => now()->addMinutes(1),
            'auto_extension' => true,
            'extension_minutes' => 3,
        ]);
        $user = User::factory()->buyer()->create();

        $bid = $this->biddingService->placeBid($auction, $user, 105);

        expect($auction->fresh()->ends_at)->toBeGreaterThan(now()->addMinutes(2));
        expect(AuctionExtension::where('auction_id', $auction->id)->exists())->toBeTrue();
    });

    it('does not extend auction when bid before sniping window');
    it('extends multiple times on consecutive last-minute bids');
    it('does not extend when auto_extension is disabled');

    // Concurrency
    it('handles concurrent bids safely', function () {
        $auction = Auction::factory()->active()->create(['current_price' => 100]);
        $user1 = User::factory()->buyer()->create();
        $user2 = User::factory()->buyer()->create();

        // Simulate concurrent access
        $results = collect([$user1, $user2])->map(function ($user) use ($auction) {
            try {
                return $this->biddingService->placeBid($auction, $user, 105);
            } catch (\Throwable $e) {
                return $e;
            }
        });

        // Exactly one should succeed, one should fail
        $successes = $results->filter(fn($r) => $r instanceof Bid);
        $failures = $results->filter(fn($r) => $r instanceof \Throwable);

        expect($successes)->toHaveCount(1);
        expect($failures)->toHaveCount(1);
    });

    // Buy Now
    it('processes buy now purchase');
    it('rejects buy now when no buy_now_price set');
});
```

### EscrowService Tests (Critical — 100% coverage)

```php
describe('EscrowService', function () {
    it('holds funds from buyer wallet');
    it('fails hold when insufficient balance');
    it('fails hold when wallet frozen');
    it('releases funds to seller minus commission');
    it('calculates correct commission by tier', function () {
        $seller = User::factory()->create();
        // Free tier = 8%
        $order = Order::factory()->create(['seller_id' => $seller->id, 'amount' => 100]);

        $this->escrowService->releaseFunds($order);

        $sellerWallet = $seller->wallet->fresh();
        expect($sellerWallet->balance)->toBe(92.0); // 100 - 8%
    });
    it('refunds buyer fully');
    it('refunds buyer partially');
    it('auto-releases after 14 days');
    it('blocks release when dispute open');
    it('handles double-release attempt gracefully');
});
```

### Other Service Tests

| Service | Min Tests | Critical Scenarios |
|---------|-----------|-------------------|
| `AuctionService` | 15 | State transitions, end auction, cancel |
| `KycService` | 10 | OTP send/verify, document review, levels |
| `RatingService` | 10 | Create rating, trust score calc, badges |
| `DisputeService` | 12 | Open, evidence, resolve, escalation, auto-escalate |
| `WalletService` | 10 | Deposit, withdraw, balance, frozen, atomic ops |
| `PaymentService` | 10 | Process, refund, webhook validation |
| `ShippingService` | 8 | Create waybill, tracking, estimate |
| `BidIncrementService` | 7 | All price ranges, edge cases |

---

## Feature Tests (HTTP / API)

```php
// tests/Feature/Api/BidTest.php

describe('POST /auctions/{id}/bid', function () {
    it('returns 200 and bid data on success');
    it('returns 400 when bid below minimum');
    it('returns 401 when not authenticated');
    it('returns 403 when bidding on own auction');
    it('returns 409 when outbid during processing');
    it('returns 410 when auction ended');
    it('returns 422 on validation error');
    it('returns 429 when rate limited');
    it('broadcasts BidPlaced event on success');
    it('sends OutbidNotification to previous leader');
});

// tests/Feature/Api/AuctionTest.php
describe('Auction API', function () {
    it('lists active auctions with pagination');
    it('filters by category');
    it('filters by price range');
    it('searches by keyword');
    it('sorts by ending soon');
    it('creates auction as seller');
    it('rejects auction creation over tier limit');
    it('cancels auction with no bids');
    it('rejects cancel when bids exist');
});

// tests/Feature/Auth/AuthTest.php
describe('Authentication', function () {
    it('registers new buyer');
    it('registers new seller');
    it('rejects duplicate email');
    it('logs in with correct credentials');
    it('rejects wrong password');
    it('locks account after 10 failed attempts');
    it('sends verification email on register');
    it('redirects buyer to dashboard after login');
    it('redirects seller to seller dashboard after login');
    it('MFA required for seller operations');
});
```

---

## E2E Tests (Laravel Dusk)

```php
// tests/Browser/BuyerJourneyTest.php

it('complete buyer journey: register → search → bid → win → pay → receive', function () {
    $this->browse(function (Browser $browser) {
        $browser
            // Register
            ->visit('/register')
            ->select('type', 'buyer')
            ->type('name', 'Test Buyer')
            ->type('email', 'buyer@test.com')
            ->type('password', 'Password123!')
            ->press('Registruj se')
            ->assertPathIs('/verify-email')

            // After verification
            ->visit('/dashboard')
            ->assertSee('Dashboard')

            // Search
            ->visit('/auctions')
            ->type('search', 'Samsung')
            ->waitForText('Samsung Galaxy')
            ->click('@auction-card-1')

            // Bid
            ->assertSee('Trenutna cijena')
            ->type('bid_amount', '150')
            ->press('LICITIRAJ')
            ->waitForText('Vaš bid je prihvaćen')

            // Win (simulate auction end)
            // ... artisan command to end auction
            ->visit('/dashboard')
            ->assertSee('Čestitamo! Dobili ste')

            // Pay
            ->click('@pay-button')
            ->assertSee('Plaćanje')
            // ... payment flow

            // Confirm delivery
            ->visit('/orders')
            ->click('@confirm-delivery')
            ->assertSee('Transakcija završena');
    });
});
```

### E2E Scenarios (10 total)

| # | Scenario | Priority |
|---|----------|----------|
| 1 | Buyer: register → bid → win → pay → rate | Critical |
| 2 | Seller: register → KYC → create auction → receive payment | Critical |
| 3 | Proxy bidding: set proxy → auto-outbid → win | High |
| 4 | Anti-sniping: bid in last 2 min → timer extends | High |
| 5 | Escrow: payment → dispute → resolution | High |
| 6 | Admin: moderate auction → approve/reject | Medium |
| 7 | Admin: review KYC → approve → seller upgraded | Medium |
| 8 | Watchlist: add → notification when ending soon | Medium |
| 9 | Search: filter → sort → paginate | Medium |
| 10 | Mobile: all critical flows on 375px viewport | High |

---

## Load Testing (k6)

```javascript
// tests/load/bid-stress.js

import http from 'k6/http';
import { check, sleep } from 'k6';
import ws from 'k6/ws';

export const options = {
    scenarios: {
        // Simulate auction ending — 500 users bidding in last 2 minutes
        auction_ending: {
            executor: 'ramping-vus',
            startVUs: 10,
            stages: [
                { duration: '30s', target: 100 },
                { duration: '60s', target: 500 },   // Peak: 500 concurrent bidders
                { duration: '30s', target: 0 },
            ],
        },
    },
    thresholds: {
        http_req_duration: ['p(99) < 500'],     // 99th percentile < 500ms
        http_req_failed: ['rate < 0.01'],        // < 1% failure rate
        'checks': ['rate > 0.95'],               // 95% of checks pass
    },
};

export default function () {
    const auctionId = 'test-auction-uuid';
    const token = getAuthToken();

    const res = http.post(
        `${BASE_URL}/auctions/${auctionId}/bid`,
        JSON.stringify({ amount: Math.random() * 100 + 100 }),
        {
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`,
            },
        }
    );

    check(res, {
        'status is 200 or 409': (r) => [200, 409].includes(r.status),
        'response time < 500ms': (r) => r.timings.duration < 500,
    });

    sleep(Math.random() * 2);
}
```

### Load Test Scenarios

| Scenario | Virtual Users | Duration | Target |
|----------|-------------|----------|--------|
| Normal traffic | 50 VU | 10 min | p99 < 200ms |
| Peak bidding (auction ending) | 500 VU | 2 min | p99 < 500ms, < 1% errors |
| Search stress | 200 VU | 5 min | p99 < 300ms |
| WebSocket connections | 1000 connections | 10 min | < 5% disconnections |
| Mixed workload | 300 VU (browse + bid + search) | 30 min | p99 < 500ms overall |

### Performance Baselines

| Endpoint | Current | Target (MVP) | Target (Scale) |
|----------|---------|-------------|----------------|
| `POST /bid` | TBD | < 200ms p50, < 500ms p99 | < 50ms p50, < 200ms p99 |
| `GET /auctions` | TBD | < 300ms p50 | < 100ms p50 |
| `GET /auctions/{id}` | TBD | < 200ms p50 | < 100ms p50 |
| `GET /search` | TBD | < 200ms p50 | < 50ms p50 |
| WebSocket broadcast | TBD | < 500ms | < 200ms |

---

## CI/CD Test Pipeline

```yaml
# .github/workflows/ci.yml
name: CI

on: [pull_request]

jobs:
  static:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: PHP Lint (Pint)
        run: vendor/bin/pint --test
      - name: PHPStan (Level 6)
        run: vendor/bin/phpstan analyse --level=6
      - name: JS Lint (ESLint)
        run: npm run lint

  unit:
    runs-on: ubuntu-latest
    needs: static
    services:
      postgres:
        image: postgres:16
        env: { POSTGRES_DB: test, POSTGRES_USER: test, POSTGRES_PASSWORD: test }
      redis:
        image: redis:7
    steps:
      - uses: actions/checkout@v4
      - name: Unit + Feature Tests
        run: php artisan test --parallel --coverage --min=80
      - name: Upload Coverage
        uses: codecov/codecov-action@v4

  dusk:
    runs-on: ubuntu-latest
    needs: unit
    steps:
      - name: Browser Tests
        run: php artisan dusk

  vue:
    runs-on: ubuntu-latest
    steps:
      - name: Vue Component Tests
        run: npx vitest run --coverage
```

### Coverage Targets

| Area | Min Coverage | Ideal |
|------|-------------|-------|
| BiddingService | 100% | 100% |
| EscrowService | 100% | 100% |
| WalletService | 95% | 100% |
| Other Services | 80% | 90% |
| Models | 70% | 80% |
| Controllers | 60% | 75% |
| Livewire Components | 50% | 70% |
| **Overall** | **80%** | **85%** |

---

## Test Data Strategy

### Factories

```php
// Svaki model ima factory sa realističnim podacima

Auction::factory()
    ->active()                    // status=active, ends_at=future
    ->withBids(15)                // 15 random bidova
    ->withImages(3)               // 3 slike
    ->featured()                  // is_featured=true
    ->create();

User::factory()
    ->seller()                    // role=seller
    ->verified()                  // email_verified_at set
    ->kycLevel(3)                 // verified_seller
    ->withWallet(1000)            // wallet sa 1000 BAM
    ->withRating(4.5, 50)         // 4.5 avg, 50 ratings
    ->create();
```

### Database Transactions in Tests

```php
// Svaki test unutar DB transakcije — auto rollback
uses(RefreshDatabase::class);

// Za Dusk tests — DatabaseMigrations (slower but needed)
uses(DatabaseMigrations::class);
```
