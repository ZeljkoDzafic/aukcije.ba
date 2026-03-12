# 01 - Layouts Specification

## Scope

Ovaj dokument razrađuje `T-400: Base Layouts` iz [docs/TASKS.md](/Users/zeljkodzafic/Documents/aukcije.ba/docs/TASKS.md) za trenutak kada Laravel scaffold bude spreman.

## Files To Create

- `resources/views/layouts/guest.blade.php`
- `resources/views/layouts/app.blade.php`
- `resources/views/layouts/seller.blade.php`
- `resources/views/layouts/admin.blade.php`

## Shared Rules

- Tailwind-based, mobile-first, Trust Blue paleta
- Inter kao default font, monospace za cijene i timere
- Max content width za marketing stranice: `max-w-7xl`
- Sticky header na guest i app layoutu
- Minimum touch target `44x44px`
- Flash poruke i validation errors moraju imati rezervisano mjesto u layoutu
- Slotovi: `title`, `meta`, `header`, `content`, `scripts`

## Guest Layout

Koristi se za landing i auth stranice.

### Header
- Logo lijevo
- Desktop nav: `Aukcije`, `Kako radi`, `Kategorije`, `Prijava`, `Registracija`
- Mobile hamburger otvara full-height slide-over
- CTA dugme: `Počni licitirati`

### Footer
- Linkovi: `O nama`, `FAQ`, `Uslovi`, `Privatnost`, `Kontakt`
- Kratak trust blok: escrow, verified sellers, sigurno plaćanje

## App Layout

Koristi se za buyer experience.

### Top Navigation
- Logo
- Search input centralno na desktopu
- Notification bell
- Wallet shortcut
- User dropdown

### Sidebar
- `Dashboard`
- `Aukcije`
- `Watchlist`
- `Poruke`
- `Wallet`
- `Profil`

Na mobilnom sidebar postaje off-canvas panel.

## Seller Layout

Struktura prati app layout, ali navigacija je seller-specific:

- `Dashboard`
- `Moje aukcije`
- `Narudžbe`
- `Wallet`
- `Statistike`
- `Profil`

Header mora imati jasan `Kreiraj aukciju` CTA.

## Admin Layout

Sidebar prioritetno sadrži:

- `Dashboard`
- `Aukcije`
- `Korisnici`
- `Kategorije`
- `Sporovi`
- `Statistike`
- `Postavke`

Admin layout koristi gušći content rhythm i više prostora za tabele i filtre.

## Accessibility Checklist

- Skip link na vrhu dokumenta
- Vidljiv focus ring na svim linkovima i dugmadima
- `aria-expanded` i `aria-controls` za mobile menije
- Sidebar i dropdowni zatvaraju se na `Esc`

## Test Checklist

- Guest layout render bez auth state zavisnosti
- Role-based nav stavke vidljive samo relevantnim korisnicima
- Mobile menu i sidebar rade tastaturom
- Search input i user menu ne lome header na `320px`
