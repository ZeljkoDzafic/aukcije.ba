# 06 - Bidding Engine (Core Feature)

## Pregled

Bidding Engine je srce platforme. Mora garantovati:
- **Atomičnost** — nijedan bid se ne može izgubiti pri konkurentnim ponudama
- **Korektnost** — uvijek pobjeđuje najviša ponuda
- **Real-time** — svi korisnici vide ažurirane cijene u < 500ms
- **Fairness** — anti-sniping mehanizam osigurava fer završetak
- **Testabilnost** — 100% unit test coverage, load testi za concurrency

> **OBAVEZNO:** Svaka promjena u BiddingService MORA imati unit test.
> Bez testa = PR se ne mergea. Vidi `docs/arhitektura/17-testing-strategy.md`

## Tipovi Aukcija

### 1. Standardna Aukcija (English Auction)
- Cijena raste sa svakim bidom
- Pobjeđuje najviša ponuda na kraju
- Anti-sniping opcionalno uključen (default: da)

### 2. Kupi Odmah (Fixed Price)
- Fiksna cijena, nema licitiranja
- Prva osoba koja klikne "Kupi" dobija artikal
- Može kombinovati sa standardnom aukcijom (hybrid)

### 3. Holandska Aukcija (Dutch Auction)
- Cijena opada tokom vremena
- Prva osoba koja prihvati trenutnu cijenu dobija artikal
- Korisno za brzu prodaju

## Proxy Bidding System

```
Korisnik A postavi proxy bid sa max = 100 BAM
Korisnik B licitira 30 BAM

Sistem automatski:
1. Provjeri proxy bidove na aukciji
2. Nađe Korisnika A sa max 100 BAM
3. Auto-bid za Korisnika A: 30 + increment = 31 BAM
4. Broadcastuje novi current_price = 31 BAM

Korisnik B licitira 50 BAM
→ Auto-bid za A: 51 BAM

Korisnik C licitira 105 BAM
→ A-ov proxy je iscrpljen (max 100)
→ C vodi sa 105 BAM
→ A prima OutbidNotification
```

### Proxy Bidding Flow (Technical)

```php
class BiddingService
{
    public function placeBid(Auction $auction, User $user, float $amount, bool $isProxy = false): Bid
    {
        // 1. Acquire Redis lock
        $lock = Cache::lock("auction:{$auction->id}:bid", 5);

        return $lock->block(3, function () use ($auction, $user, $amount, $isProxy) {

            // 2. Refresh auction from DB
            $auction->refresh();

            // 3. Validacije
            $this->validateBid($auction, $user, $amount);

            // 4. Ako je proxy bid — spremi max amount
            if ($isProxy) {
                return $this->handleProxyBid($auction, $user, $amount);
            }

            // 5. Kreiraj bid
            $bid = $this->createBid($auction, $user, $amount);

            // 6. Provjeri aktivne proxy bidove drugih korisnika
            $this->processProxyBids($auction, $bid);

            // 7. Anti-sniping check
            $this->checkAntiSniping($auction, $bid);

            // 8. Broadcast event
            BidPlaced::dispatch($auction, $bid);

            return $bid;
        });
    }
}
```

## Anti-Sniping Mehanizam

```
Scenario:
- Aukcija završava u 18:00:00
- Anti-sniping window: 2 minuta
- Produženje: 3 minute

17:58:30 → Korisnik B licitira
         → Preostalo: 1:30 < 2:00 (unutar window-a)
         → Aukcija produžena do 18:01:30

18:00:45 → Korisnik C licitira
         → Preostalo: 0:45 < 2:00
         → Aukcija produžena do 18:03:45

18:03:00 → Niko ne licitira
18:03:45 → Aukcija završava, C pobjeđuje
```

### Implementacija

```php
private function checkAntiSniping(Auction $auction, Bid $bid): void
{
    if (!$auction->auto_extension) {
        return;
    }

    $timeRemaining = $auction->ends_at->diffInSeconds(now());
    $snipingWindow = 120; // 2 minuta

    if ($timeRemaining <= $snipingWindow) {
        $oldEndAt = $auction->ends_at;
        $newEndAt = now()->addMinutes($auction->extension_minutes);

        $auction->update(['ends_at' => $newEndAt]);

        AuctionExtension::create([
            'auction_id' => $auction->id,
            'triggered_by_bid_id' => $bid->id,
            'old_end_at' => $oldEndAt,
            'new_end_at' => $newEndAt,
        ]);

        // Broadcast timer update
        AuctionExtended::dispatch($auction, $newEndAt);
    }
}
```

## Bid Increments (Dinamički)

| Trenutna cijena | Minimalni korak |
|----------------|-----------------|
| 0 - 10 BAM | 0.50 BAM |
| 10 - 50 BAM | 1.00 BAM |
| 50 - 100 BAM | 2.00 BAM |
| 100 - 500 BAM | 5.00 BAM |
| 500 - 1,000 BAM | 10.00 BAM |
| 1,000 - 5,000 BAM | 25.00 BAM |
| 5,000+ BAM | 50.00 BAM |

```php
class BidIncrementService
{
    public function getMinimumBid(float $currentPrice): float
    {
        $increment = BidIncrement::where('price_from', '<=', $currentPrice)
            ->where(function ($q) use ($currentPrice) {
                $q->where('price_to', '>', $currentPrice)
                  ->orWhereNull('price_to');
            })
            ->first();

        return $currentPrice + ($increment?->increment ?? 1.00);
    }
}
```

## Auction Lifecycle (State Machine)

```
[DRAFT] ──create──→ [ACTIVE] ──time expires──→ [FINISHED]
                       │                            │
                       │                     (has winning bid?)
                       │                      /          \
                       │                   Yes            No
                       │                    │              │
                       │               [SOLD]      [FINISHED]
                       │
                       ├──seller cancels──→ [CANCELLED]
                       │   (no bids yet)
                       │
                       └──buy now──→ [SOLD]
```

```php
// App\Enums\AuctionStatus
enum AuctionStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Finished = 'finished';
    case Sold = 'sold';
    case Cancelled = 'cancelled';

    public function canTransitionTo(self $new): bool
    {
        return match($this) {
            self::Draft => in_array($new, [self::Active, self::Cancelled]),
            self::Active => in_array($new, [self::Finished, self::Sold, self::Cancelled]),
            self::Finished => in_array($new, [self::Sold]),
            default => false,
        };
    }
}
```

## Auction Ending (Scheduled Job)

```php
// App\Console\Commands\EndExpiredAuctions
// Runs every minute via Laravel Scheduler

class EndExpiredAuctions extends Command
{
    public function handle()
    {
        Auction::where('status', 'active')
            ->where('ends_at', '<=', now())
            ->chunk(50, function ($auctions) {
                foreach ($auctions as $auction) {
                    EndAuctionJob::dispatch($auction);
                }
            });
    }
}

// EndAuctionJob
class EndAuctionJob implements ShouldQueue
{
    public function handle(AuctionService $service)
    {
        $service->endAuction($this->auction);
        // → Determines winner
        // → Creates Order
        // → Freezes escrow amount
        // → Sends notifications (winner, seller, watchers)
        // → Broadcasts AuctionEnded event
    }
}
```

## Concurrency Protection Summary

| Layer | Mehanizam | Svrha |
|-------|-----------|-------|
| 1. Redis Lock | `Cache::lock()` | Sprječava simultane bidove na istoj aukciji |
| 2. DB Transaction | `DB::transaction()` | Atomička operacija bid + price update |
| 3. Advisory Lock | `pg_advisory_lock()` | Backup za Redis failure |
| 4. Optimistic Lock | `updated_at` check | Detektuje stale data |
| 5. Rate Limit | Throttle middleware | Sprječava bid flooding |
