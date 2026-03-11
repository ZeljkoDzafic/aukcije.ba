# 04 - Authentication & Roles

## Auth System (Laravel Breeze / Jetstream)

### Login Metode
1. **Email + Password** — primarna metoda
2. **Google OAuth** — brza registracija za kupce
3. **SMS OTP** — za mobilne korisnike (Phase 2)

### Registration Flow

```
[Landing Page]
     |
     v
[Click "Registruj se"]
     |
     v
[Form: ime, email, password, tip (kupac/prodavac)]
     |
     v
[Laravel creates user + assigns role]
     |
     v
[Email verification link sent]
     |
     v
[User klikne link → redirected to /dashboard]
     |
     v
[Onboarding wizard (first login)]
  Za kupce:
    1. Preferirane kategorije
    2. Lokacija (za shipping estimaciju)
  Za prodavce:
    1. KYC verifikacija (SMS + dokument)
    2. Wallet setup
    3. Prva aukcija wizard
     |
     v
[Dashboard - spreman za korištenje]
```

### Pet Rola (RBAC via Spatie)

| Rola | Pristup | Kako se dodjeljuje |
|------|---------|-------------------|
| `buyer` | Licitiranje, watchliste, poruke | Auto pri registraciji |
| `seller` | Sve buyer + kreiranje aukcija (limit 5) | Auto pri registraciji kao prodavac |
| `verified_seller` | Sve seller + bez limita, API, Featured | Admin dodjeljuje nakon KYC |
| `moderator` | Verifikacija sadržaja, kategorije, reporti | Admin dodjeljuje |
| `super_admin` | Sve + konfiguracija, dispute resolution, financije | Inicijalni korisnik |

### Permissions (granularne)

```php
// Buyer permissions
'bid.create', 'bid.view_own',
'auction.view', 'auction.search',
'watchlist.manage', 'message.send',
'profile.edit_own', 'rating.create'

// Seller permissions (+ buyer)
'auction.create', 'auction.edit_own', 'auction.cancel_own',
'order.manage_own', 'shipment.create',
'wallet.view_own', 'wallet.withdraw'

// Verified seller (+ seller)
'auction.create_unlimited', 'auction.featured',
'api.access', 'storefront.manage'

// Moderator (+ buyer)
'auction.moderate', 'user.moderate',
'category.manage', 'report.view',
'dispute.review'

// Super admin (sve)
'*'
```

### MFA (Multi-Factor Authentication)

- **Obavezno** za sve seller role (seller, verified_seller)
- **Opcionalno** za buyer role
- Implementacija: Laravel Fortify TOTP (Google Authenticator / Authy)
- Backup codes: 8 jednokratnih kodova pri aktivaciji MFA

### Session Security

```php
// config/session.php
'lifetime' => 120,        // 2 sata
'expire_on_close' => false,
'encrypt' => true,
'secure' => true,          // samo HTTPS
'http_only' => true,
'same_site' => 'lax',
```

### Rate Limiting

| Endpoint | Limit | Period |
|----------|-------|--------|
| Login attempts | 5 | 1 min |
| Registration | 3 | 1 hour |
| Bid placement | 10 | 1 min |
| Password reset | 3 | 1 hour |
| API calls (verified seller) | 100 | 1 min |
