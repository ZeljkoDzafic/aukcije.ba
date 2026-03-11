# 14 - Feature Flags & Tiers

## Feature Flags

### Package: `spatie/laravel-feature-flags`

Feature flags omogućavaju staged rollout novih funkcionalnosti bez re-deploya.

### Predefinisane Feature Flags

| Flag | Default | Opis |
|------|---------|------|
| `dutch_auctions` | OFF | Holandske aukcije (opadajuća cijena) |
| `proxy_bidding` | ON | Automatsko licitiranje |
| `anti_sniping` | ON | Produženje aukcije |
| `escrow_payments` | ON | Escrow zaštita |
| `kyc_required_sellers` | ON | KYC obavezan za prodavce |
| `messaging` | ON | In-app poruke |
| `push_notifications` | OFF | Firebase push (čeka mobile app) |
| `seller_api` | OFF | API za verified sellers |
| `storefront` | OFF | Custom subdomene za sellere |
| `multi_currency` | OFF | EUR/RSD podrška |
| `auto_shipping` | OFF | Automatski tovarni listovi |

### Korištenje u Kodu

```php
// U Blade template-u
@feature('dutch_auctions')
    <option value="dutch">Holandska aukcija (opadajuća cijena)</option>
@endfeature

// U PHP kodu
if (Feature::active('escrow_payments')) {
    $this->escrowService->holdFunds($order);
}

// U middleware-u
Route::middleware('feature:seller_api')->group(function () {
    Route::apiResource('auctions', Api\AuctionController::class);
});
```

---

## Tier Sistem

### 3 Tiera za Prodavce

```
┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐
│    FREE          │  │    PREMIUM      │  │   STOREFRONT    │
│                  │  │                 │  │                 │
│  5 aktivnih      │  │  50+ aktivnih   │  │  Bez limita     │
│  aukcija         │  │  aukcija        │  │                 │
│                  │  │                 │  │  Custom         │
│  Standard        │  │  "Featured"     │  │  subdomena      │
│  vidljivost      │  │  badge          │  │                 │
│                  │  │                 │  │  Dedicated      │
│  8% komisija     │  │  5% komisija    │  │  account mgr    │
│                  │  │                 │  │                 │
│  Basic support   │  │  Priority       │  │  3% komisija    │
│                  │  │  support        │  │                 │
│  FREE            │  │  29 BAM/mj     │  │  99 BAM/mj     │
└─────────────────┘  └─────────────────┘  └─────────────────┘
```

### Detaljno Poređenje

| Feature | Free | Premium | Storefront |
|---------|------|---------|------------|
| Aktivne aukcije | 5 | 50 | Neograničeno |
| Komisija na prodaju | 8% | 5% | 3% |
| Featured badge | Ne | Da | Da |
| Promoted listings | Ne | 3/mj uključeno | 10/mj uključeno |
| Seller API | Ne | Ne | Da |
| Custom subdomena | Ne | Ne | Da |
| Analytics dashboard | Osnovni | Napredni | Kompletni |
| Support | Email (48h) | Email (24h) | Dedicated (4h) |
| Bulk upload | Ne | Da (CSV) | Da (CSV + API) |
| Custom branding | Ne | Ne | Logo + boje |
| Cijena | 0 BAM/mj | 29 BAM/mj | 99 BAM/mj |

### Tier Upgrade Flow

```
[Free korisnik]
     │
     ▼
[Dostiže limit od 5 aukcija]
     │
     ▼
[Prikazuje se upgrade banner]
     │
     ▼
[Klikne "Upgrade to Premium"]
     │
     ▼
[Payment flow (Monri/Stripe)]
     │
     ▼
[Subscription kreiran → Role update]
     │
     ▼
[Limiti automatski prošireni]
```

### Implementacija

```php
// App\Models\User

public function tier(): string
{
    return $this->subscription?->plan ?? 'free';
}

public function canCreateAuction(): bool
{
    $limits = [
        'free' => 5,
        'premium' => 50,
        'storefront' => PHP_INT_MAX,
    ];

    $activeCount = $this->auctions()->active()->count();
    return $activeCount < ($limits[$this->tier()] ?? 5);
}

public function commissionRate(): float
{
    return match($this->tier()) {
        'storefront' => 0.03,
        'premium' => 0.05,
        default => 0.08,
    };
}
```

## Registration Flow + Tier Assignment

```
[Landing Page]
     │
     ├── "Kupi" → Registracija kao Buyer (no tier)
     │
     └── "Prodaj" → Registracija kao Seller
           │
           ▼
     [Automatski Free tier]
           │
           ▼
     [KYC verifikacija (opciona, ali preporučena)]
           │
           ├── KYC verificiran → "Verified Seller" badge
           │
           └── KYC nije verificiran → Upozorenje na profilu
           │
           ▼
     [Koristi Free tier dok ne upgradeuje]
```
