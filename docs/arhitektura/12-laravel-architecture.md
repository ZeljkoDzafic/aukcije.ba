# 12 - Laravel Internal Architecture

## Pattern: Service Layer + Repository (Optional)

```
app/
├── Models/                      -- Eloquent modeli
│   ├── User.php
│   ├── Auction.php
│   ├── Bid.php
│   ├── ProxyBid.php
│   ├── Order.php
│   ├── Wallet.php
│   └── ...
│
├── Services/                    -- Biznis logika (core)
│   ├── BiddingService.php       -- Bid processing, proxy, locks
│   ├── AuctionService.php       -- State changes, extensions, ending
│   ├── WalletService.php        -- Deposit, withdraw, escrow
│   ├── EscrowService.php        -- Hold, release, refund
│   ├── ShippingService.php      -- Courier API integration
│   ├── SearchService.php        -- Meilisearch wrapper
│   ├── KycService.php           -- Verification logic
│   └── RatingService.php        -- Trust score calculation
│
├── Events/                      -- Domain events
│   ├── BidPlaced.php
│   ├── AuctionExtended.php
│   ├── AuctionEnded.php
│   ├── AuctionWon.php
│   ├── OrderCreated.php
│   ├── PaymentReceived.php
│   ├── ItemShipped.php
│   ├── ItemDelivered.php
│   └── DisputeOpened.php
│
├── Listeners/                   -- Event handlers
│   ├── BroadcastBidUpdate.php
│   ├── SendOutbidNotification.php
│   ├── SendAuctionWonEmail.php
│   ├── CreateOrderOnAuctionEnd.php
│   ├── FreezeEscrowOnPayment.php
│   ├── ReleaseEscrowOnConfirm.php
│   └── UpdateTrustScore.php
│
├── Jobs/                        -- Queue jobs
│   ├── EndAuctionJob.php
│   ├── ProcessProxyBidsJob.php
│   ├── SendNotificationJob.php
│   ├── GenerateWaybillJob.php
│   └── IndexAuctionJob.php
│
├── Enums/
│   ├── AuctionStatus.php
│   ├── AuctionType.php
│   ├── OrderStatus.php
│   ├── PaymentGateway.php
│   ├── TransactionType.php
│   └── UserRole.php
│
├── Http/
│   ├── Controllers/
│   │   ├── Api/                 -- JSON API controllers
│   │   │   ├── AuctionController.php
│   │   │   ├── BidController.php
│   │   │   └── UserController.php
│   │   ├── Web/                 -- Web controllers (minimal, Livewire handles most)
│   │   │   ├── HomeController.php
│   │   │   └── AuctionController.php
│   │   └── Admin/               -- Admin controllers
│   │       ├── DashboardController.php
│   │       ├── UserController.php
│   │       └── DisputeController.php
│   │
│   ├── Livewire/                -- Livewire components
│   │   ├── AuctionSearch.php
│   │   ├── AuctionCard.php
│   │   ├── BiddingConsole.php
│   │   ├── Watchlist.php
│   │   ├── WalletBalance.php
│   │   ├── MessageThread.php
│   │   └── NotificationBell.php
│   │
│   ├── Middleware/
│   │   ├── EnsureKycVerified.php
│   │   ├── EnsureSellerRole.php
│   │   └── ThrottleBids.php
│   │
│   └── Requests/                -- Form requests (validation)
│       ├── StoreBidRequest.php
│       ├── StoreAuctionRequest.php
│       └── UpdateProfileRequest.php
│
├── Notifications/
│   ├── OutbidNotification.php
│   ├── AuctionWonNotification.php
│   ├── AuctionEndedNotification.php
│   ├── PaymentReceivedNotification.php
│   └── ItemShippedNotification.php
│
└── Policies/                    -- Authorization
    ├── AuctionPolicy.php
    ├── BidPolicy.php
    ├── OrderPolicy.php
    └── MessagePolicy.php
```

## Service Layer Pattern

```php
// App\Services\BiddingService

class BiddingService
{
    public function __construct(
        private BidIncrementService $incrementService,
        private AuctionService $auctionService,
    ) {}

    /**
     * Atomički postavi bid na aukciju.
     * Koristi Redis lock za concurrency protection.
     */
    public function placeBid(Auction $auction, User $user, float $amount, bool $isProxy = false): Bid
    {
        // Redis lock - samo jedan bid procesiran istovremeno po aukciji
        $lock = Cache::lock("auction:{$auction->id}:bid", 5);

        return $lock->block(3, function () use ($auction, $user, $amount, $isProxy) {
            return DB::transaction(function () use ($auction, $user, $amount, $isProxy) {

                $auction->lockForUpdate()->refresh();

                // Validacije
                throw_unless($auction->isActive(), AuctionNotActiveException::class);
                throw_if($auction->seller_id === $user->id, CannotBidOwnAuctionException::class);
                throw_if($amount < $this->incrementService->getMinimumBid($auction->current_price),
                    BidTooLowException::class);

                // Kreiraj bid
                $bid = Bid::create([
                    'auction_id' => $auction->id,
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'is_proxy' => $isProxy,
                    'ip_address' => request()->ip(),
                ]);

                // Update auction
                $auction->update([
                    'current_price' => $amount,
                    'bids_count' => DB::raw('bids_count + 1'),
                ]);

                // Anti-sniping
                $this->auctionService->checkAntiSniping($auction, $bid);

                // Process proxy bids od drugih korisnika
                $this->processProxyBids($auction, $bid);

                // Event
                BidPlaced::dispatch($auction->fresh(), $bid);

                return $bid;
            });
        });
    }
}
```

## Event → Listener Mapping

```php
// EventServiceProvider

protected $listen = [
    BidPlaced::class => [
        BroadcastBidUpdate::class,        // WebSocket → ažuriraj cijenu svima
        SendOutbidNotification::class,    // Obavijesti prethodnog vođu
    ],

    AuctionExtended::class => [
        BroadcastTimerUpdate::class,      // WebSocket → novi timer svima
    ],

    AuctionEnded::class => [
        CreateOrderOnAuctionEnd::class,   // Kreiraj Order record
        SendAuctionWonEmail::class,       // Email pobjedniku
        SendAuctionEndedEmail::class,     // Email prodavcu
        NotifyWatchers::class,            // Obavijesti sve watchere
    ],

    PaymentReceived::class => [
        FreezeEscrowOnPayment::class,     // Zamrzni sredstva
        NotifySellerToShip::class,        // Obavijesti prodavca
    ],

    ItemDelivered::class => [
        PromptBuyerToConfirm::class,      // Podsjeti kupca da potvrdi
    ],

    OrderCompleted::class => [
        ReleaseEscrowToSeller::class,     // Oslobodi sredstva
        PromptRating::class,              // Pozovi na ocjenjivanje
        UpdateTrustScore::class,          // Ažuriraj trust score
    ],
];
```

## Scheduled Commands

```php
// Console\Kernel

protected function schedule(Schedule $schedule): void
{
    // Završi istekle aukcije — svaki minut
    $schedule->command('auctions:end-expired')->everyMinute();

    // Auto-release escrow (14 dana bez odgovora kupca) — svaki sat
    $schedule->command('escrow:auto-release')->hourly();

    // Podsjeti kupca na plaćanje (3 dana) — svaka 6h
    $schedule->command('orders:payment-reminders')->everySixHours();

    // Podsjeti prodavca na slanje (5 dana) — svaka 6h
    $schedule->command('orders:shipping-reminders')->everySixHours();

    // Meilisearch reindex — svaki dan u 03:00
    $schedule->command('scout:import "App\\Models\\Auction"')->dailyAt('03:00');

    // Database backup → S3 — svaki dan u 00:00
    $schedule->command('backup:run')->dailyAt('00:00');
}
```
