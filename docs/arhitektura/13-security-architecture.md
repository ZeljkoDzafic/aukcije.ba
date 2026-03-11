# 13 - Security Architecture

## Security Principles

1. **Defense in Depth** — višestruki slojevi zaštite
2. **Least Privilege** — minimalne dozvole za svaku rolu
3. **Zero Trust** — svaki request se verificira
4. **Fail Secure** — pri grešci, sistem odbija pristup
5. **Secure by Default** — sve zaključano dok se eksplicitno ne otvori

## OWASP Top 10 Mitigation

### 1. Injection (SQL, NoSQL, OS Command)

| Vektor | Mitigacija | Implementacija |
|--------|-----------|----------------|
| SQL Injection | Parametrizirani upiti | Eloquent ORM — NIKAD raw SQL sa user inputom |
| OS Command Injection | Zabrana exec/shell_exec | `disable_functions` u php.ini |
| LDAP/XPath | N/A | Ne koristimo LDAP |

```php
// ✅ SIGURNO — Eloquent prepared statements
Auction::where('title', 'LIKE', '%' . $search . '%')->get();

// ❌ NIKAD — raw SQL sa user inputom
DB::select("SELECT * FROM auctions WHERE title LIKE '%" . $search . "%'");
```

### 2. Broken Authentication

| Mehanizam | Implementacija |
|-----------|---------------|
| Password hashing | bcrypt (Laravel default, cost 12) |
| MFA | TOTP via Fortify (obavezno za sellere) |
| Session fixation | `session()->regenerate()` na login |
| Brute force | Rate limiting: 5 login pokušaja/min |
| Account lockout | 30-min lockout nakon 10 failed pokušaja |
| Password policy | Min 8 chars, mix upper/lower/number |
| Token expiry | Access: 2h, Refresh: 7 dana, API: 365 dana (revocable) |

### 3. Sensitive Data Exposure

```
Encryption at Rest:
  - PostgreSQL: full disk encryption (LUKS na server level)
  - Redis: AUTH password + TLS
  - S3: AES-256 server-side encryption
  - Backups: GPG encrypted before upload

Encryption in Transit:
  - TLS 1.3 enforced (min TLS 1.2)
  - HSTS header: max-age=31536000; includeSubDomains; preload
  - Certificate pinning za mobile app (Phase 2)

Sensitive Fields (encrypted in DB via Laravel Crypt):
  - user_verifications.document_url (KYC dokumenti)
  - wallets.balance (financial data)
  - payments.gateway_transaction_id
  - shipments.tracking_number

Never Logged / Never in Error Messages:
  - Passwords, tokens, API keys
  - Payment card numbers
  - KYC document contents
  - Full IP addresses (anonymize to /24)
```

### 4. XML External Entities (XXE)

- Disablovano: `libxml_disable_entity_loader(true)`
- Ne koristimo XML parsing (JSON only API)

### 5. Broken Access Control

```php
// Policy-based authorization — svaka akcija provjerena
class AuctionPolicy
{
    public function bid(User $user, Auction $auction): bool
    {
        return $user->id !== $auction->seller_id    // ne može licitirati na svoje
            && $auction->isActive()                  // aukcija mora biti aktivna
            && $user->hasVerifiedEmail()             // email verificiran
            && !$user->isBanned();                   // nije banovan
    }

    public function cancel(User $user, Auction $auction): bool
    {
        return $user->id === $auction->seller_id     // samo vlasnik
            && $auction->bids_count === 0            // nema bidova
            && $auction->isActive();                 // još uvijek aktivna
    }
}

// Middleware stack za svaku route grupu
Route::middleware(['auth', 'verified', 'role:seller|verified_seller'])
    ->prefix('seller')
    ->group(function () {
        // Seller routes — samo za autentificirane, verificirane sellere
    });
```

### 6. Security Misconfiguration

```php
// config/app.php (production)
'debug' => false,                    // NIKAD true na produkciji
'env' => 'production',

// Headers (via middleware)
'X-Content-Type-Options' => 'nosniff',
'X-Frame-Options' => 'DENY',
'X-XSS-Protection' => '1; mode=block',
'Referrer-Policy' => 'strict-origin-when-cross-origin',
'Permissions-Policy' => 'camera=(), microphone=(), geolocation=()',
'Content-Security-Policy' => "default-src 'self'; script-src 'self' 'nonce-{random}'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https://s3.amazonaws.com; connect-src 'self' wss://aukcije.ba;",

// php.ini hardening
expose_php = Off
display_errors = Off
log_errors = On
allow_url_fopen = Off
allow_url_include = Off
disable_functions = exec,passthru,shell_exec,system,proc_open,popen
session.cookie_httponly = 1
session.cookie_secure = 1
session.cookie_samesite = Lax
session.use_strict_mode = 1
```

### 7. Cross-Site Scripting (XSS)

| Tip | Mitigacija |
|-----|-----------|
| Reflected XSS | Blade auto-escaping: `{{ $var }}` (NIKAD `{!! !!}` sa user inputom) |
| Stored XSS | HTMLPurifier za rich text (auction description) |
| DOM XSS | CSP nonce-based script policy |

```php
// HTMLPurifier za auction description
class SanitizeAuctionDescription
{
    public function handle(string $html): string
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 'p,br,strong,em,ul,ol,li,h3,h4');
        $config->set('HTML.MaxImgLength', null); // no images in description
        $purifier = new HTMLPurifier($config);
        return $purifier->purify($html);
    }
}
```

### 8. Insecure Deserialization

- Laravel Signed URLs za sve sensitive linkove
- Queue jobs koriste `SerializesModels` (safe)
- Cookie encryption enforced (`encrypt` => true)

### 9. Using Components with Known Vulnerabilities

```bash
# CI/CD pipeline obavezno
composer audit                    # PHP dependency audit
npm audit                        # JS dependency audit

# Automated monthly
# Dependabot / Renovate za auto-update PRs
# Snyk za continuous monitoring
```

### 10. Insufficient Logging & Monitoring

Vidi sekciju Monitoring ispod.

---

## PCI-DSS Compliance (Payment Card Security)

```
Strategija: SAQ A (Outsourced Payment Processing)

Nikad ne dodirujemo kartične podatke:
  - Stripe Checkout / Monri Hosted → redirect korisnika na payment stranicu
  - Kartični podaci nikad ne prolaze kroz naš server
  - Samo webhook sa payment confirmation

Obaveze:
  ✓ TLS 1.2+ na svim stranicama
  ✓ No storage of card data (no PAN, CVV, expiry)
  ✓ Webhook signature verification
  ✓ Access logging za sve payment operacije
  ✓ Quarterly vulnerability scan (ASV)
  ✓ Annual SAQ A self-assessment
```

---

## Secrets Management

```
Development:
  - .env file (gitignored)
  - .env.example sa placeholder vrijednostima

Production:
  - Environment variables injected via Docker
  - Sensitive values u Docker Secrets ili AWS Secrets Manager
  - NIKAD hardcodirane tajne u kodu
  - Key rotation svaka 90 dana:
    - APP_KEY
    - DB_PASSWORD
    - REDIS_PASSWORD
    - API keys (Stripe, Monri, Infobip, Mailgun)
    - REVERB_APP_KEY

Checklist:
  ✓ .env u .gitignore
  ✓ Nema tajni u docker-compose.yml (koristi env_file)
  ✓ CI/CD koristi GitHub Secrets
  ✓ Database password min 32 chars, random generated
  ✓ Redis requirepass configured
  ✓ Meilisearch master key configured
```

---

## Rate Limiting Strategy

| Endpoint | Limit | Period | Penalty |
|----------|-------|--------|---------|
| `POST /login` | 5 | 1 min | 30 min lockout after 10 |
| `POST /register` | 3 | 1 hour | IP block 24h |
| `POST /forgot-password` | 3 | 1 hour | — |
| `POST /auctions/{id}/bid` | 10 | 1 min | Soft block 5 min |
| `POST /auctions` | 5 | 1 hour | — |
| `POST /messages` | 20 | 1 hour | — |
| `GET /api/v1/*` (auth) | 100 | 1 min | 429 response |
| `GET /api/v1/*` (unauth) | 30 | 1 min | 429 response |
| `POST /kyc/sms-otp` | 3 | 1 hour | — |
| Global per IP | 300 | 1 min | Cloudflare challenge |

```php
// Implementacija: Laravel RateLimiter + Cloudflare WAF
RateLimiter::for('bids', function (Request $request) {
    return Limit::perMinute(10)->by($request->user()->id);
});

RateLimiter::for('api', function (Request $request) {
    return $request->user()
        ? Limit::perMinute(100)->by($request->user()->id)
        : Limit::perMinute(30)->by($request->ip());
});
```

---

## File Upload Security

```php
// Auction image upload validation
class StoreAuctionImageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'images.*' => [
                'required',
                'image',                          // Must be image
                'mimes:jpeg,png,webp',            // Allowed types only
                'max:5120',                       // Max 5MB
                'dimensions:min_width=200,min_height=200,max_width=4096,max_height=4096',
            ],
            'images' => 'max:10',                 // Max 10 images
        ];
    }
}

// Processing pipeline:
// 1. Validate MIME type (magic bytes, not just extension)
// 2. Strip EXIF metadata (privacy)
// 3. Resize to max 2048px
// 4. Convert to WebP (size optimization)
// 5. Generate thumbnail (400px)
// 6. Upload to S3 with random filename (no user-controlled paths)
// 7. Serve via CloudFront CDN (no direct S3 access)
```

---

## Anti-Fraud Engine

### Shill Bidding Detection

```
Signali:
  1. Isti IP za bidder i seller
  2. Bidder ima samo bidove na ovom selleru
  3. Bidder kreira nalog neposredno prije bidanja
  4. Pattern: uvijek nadlicitira za minimalni increment
  5. Bidder nikad ne pobjeđuje (uvijek se povlači na kraju)

Scoring:
  Svaki signal = +1 bod
  Score ≥ 3 → Auto-flag za moderaciju
  Score ≥ 4 → Auto-suspend aukcije + notify admin

Implementacija: ProcessBidFraudCheck job (async, after each bid)
```

### Velocity Checks

```
Alert triggers:
  - Seller kreira > 20 aukcija u 1 satu
  - Buyer licitira na > 30 aukcija u 1 satu
  - Account kreira > 5 disputes u 30 dana
  - Wallet: > 3 withdrawal attempts u 1 dan
  - Account age < 24h + bid > 1000 BAM
```

### Device Fingerprinting (Phase 2)

```
Collected signals:
  - Browser User-Agent
  - Screen resolution
  - Timezone
  - Installed fonts (Canvas fingerprint)
  - WebGL renderer
  - Audio context fingerprint

Purpose:
  - Link multiple accounts to same device
  - Detect ban evasion
  - Risk scoring for high-value bids
```

---

## Incident Response Playbook

### Severity Levels

| Level | Opis | Response Time | Example |
|-------|------|---------------|---------|
| P0 - Critical | Platform down, data breach, financial loss | < 15 min | DB compromised, payment leak |
| P1 - High | Major feature broken, security vulnerability | < 1 hour | Bidding engine down, auth bypass |
| P2 - Medium | Feature degraded, non-critical bug | < 4 hours | Search down, slow responses |
| P3 - Low | Minor issue, cosmetic | Next business day | UI glitch, typo |

### Response Steps (P0/P1)

```
1. DETECT    → Monitoring alert / user report
2. TRIAGE    → Assess severity, assign owner
3. CONTAIN   → Isolate affected system (feature flag OFF, maintenance mode)
4. NOTIFY    → Stakeholders, users (if data affected)
5. FIX       → Root cause analysis + patch
6. VERIFY    → Test fix in staging
7. DEPLOY    → Push to production
8. REVIEW    → Post-mortem within 48h
9. IMPROVE   → Update playbook, add monitoring
```

### Contact Chain

```
Detection → Developer (Slack/SMS alert)
         → If no response in 15 min → Escalate to backup
         → If data breach → Legal team + GDPR DPO within 72h
```

---

## GDPR / Data Protection Compliance

### Data Subject Rights

| Pravo | Implementacija | Endpoint |
|-------|---------------|----------|
| Right to Access | Export svih ličnih podataka (JSON/CSV) | `GET /profile/export` |
| Right to Rectification | Edit profil | `PUT /profile` |
| Right to Erasure | Account deletion (30 dana cooling) | `POST /profile/delete-request` |
| Right to Portability | Download sve: profil, aukcije, bidovi, poruke | `GET /profile/export?format=json` |
| Right to Object | Opt-out marketing emails | Notification preferences |

### Data Retention Policy

| Podatak | Retencija | Razlog |
|---------|-----------|--------|
| Active user data | Dok je nalog aktivan | Pružanje usluge |
| Deleted account data | 30 dana (cooling period) | Account recovery |
| Transaction records | 7 godina | Zakonska obaveza (finansijski zapisi) |
| Bid history | 3 godine | Dispute resolution + analytics |
| Chat messages | 1 godina nakon zadnje poruke | Dispute resolution |
| KYC documents | 5 godina od verifikacije | AML compliance |
| Server logs | 90 dana | Security + debugging |
| Analytics (anonymized) | Neograničeno | Business intelligence |

### Data Deletion Cascade

```
Account deletion request:
  Day 0:  Account deactivated, profile hidden
  Day 30: If not cancelled:
    1. Delete: profile, avatar, preferences
    2. Anonymize: bids (user_id → null, keep amount for auction integrity)
    3. Anonymize: ratings (keep score, remove name)
    4. Delete: messages, notifications, watchlist
    5. Keep: orders, payments, shipments (legal requirement, anonymized)
    6. Delete: KYC documents (scheduled for 5y from verification)
    7. Delete: wallet (after zero balance confirmed)
    8. Delete: auth user record
```

---

## Audit Logging

```php
// Svaka admin/security akcija se loguje
class AuditLogger
{
    public static function log(string $action, ?User $actor, ?Model $target, array $meta = []): void
    {
        AdminLog::create([
            'admin_id' => $actor?->id,
            'action' => $action,
            'target_type' => $target ? get_class($target) : null,
            'target_id' => $target?->id,
            'metadata' => array_merge($meta, [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now()->toIso8601String(),
            ]),
        ]);
    }
}

// Logovane akcije:
// - Login/logout (success + failed)
// - Role change
// - KYC status change
// - Auction moderation (approve/reject/cancel)
// - Dispute resolution
// - Wallet operations (deposit/withdrawal/escrow)
// - Feature flag toggle
// - User ban/unban
// - Password reset (admin-initiated)
// - Tier change
// - Bulk operations
```
