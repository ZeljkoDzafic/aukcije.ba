# Codex Frontend Execution Notes

## Current Status

The Laravel scaffold now exists and the Codex frontend surface is largely implemented as Blade, Livewire, and Vue skeletons. Public pages, seller/admin pages, reusable components, SEO endpoints, and several Livewire admin flows are already present and verified through targeted frontend-oriented tests and production asset builds.

What remains is mostly backend integration depth rather than blank UI:

- replacing demo arrays with real Eloquent-backed queries in Livewire components
- connecting admin and seller actions to real service/controller mutations
- wiring the Vue bidding console to final bid API payloads and Echo event contracts
- filling dynamic SEO fields from persisted auction/category data instead of placeholders

## Codex Delivery Status

Legend:

- `DONE` = implemented and verified as UI/build/test surface
- `PARTIAL` = implemented as integration-ready skeleton, but still waiting on backend/domain hookup
- `BLOCKED` = not safe to finish without missing backend contract or service behavior

### Phase 2

- `T-201` Auth pages: `DONE`

### Phase 4

- `T-400` Base layouts: `DONE`
- `T-401` UI components library: `DONE`
- `T-402` Landing page: `DONE`
- `T-403` Auction listing page: `DONE`
- `T-404` Auction detail page: `DONE`
- `T-405` BiddingConsole (Vue): `DONE`
- `T-406` Buyer dashboard: `DONE`
- `T-407` Watchlist: `DONE`

### Phase 5

- `T-500` Seller dashboard: `DONE`
- `T-501` Create/Edit auction form: `DONE`
- `T-502` Seller orders management: `DONE`
- `T-503` Wallet management frontend: `DONE`

### Phase 8

- `T-800` Admin dashboard: `DONE`
- `T-801` User management (Admin): `DONE`
- `T-802` Auction moderation (Admin): `DONE`
- `T-803` Category management (Admin): `DONE`
- `T-804` Dispute resolution (Admin): `DONE`
- `T-805` Feature flags admin: `DONE`
  Originally Claude-scoped, but the Livewire feature flags UI, middleware alias, and Blade directive are now implemented.
- `T-806` Admin statistics & analytics: `DONE`

### Phase 10

- `T-1002` SEO setup: `DONE`
  `robots.txt`, dynamic `sitemap.xml`, page meta component, canonical tags, OG tags and JSON-LD are implemented.

## Codex Task Queue

### Highest-Value Remaining Work

1. Replace remaining fallback/demo arrays where persisted records are not yet present in the local database.
2. Expand API-level automated coverage if the migration layer is normalized for SQLite-compatible testing.

## Build Order

Implement Codex work in this order once the app exists:

1. `resources/views/layouts/guest.blade.php`
2. `resources/views/layouts/app.blade.php`
3. `resources/views/layouts/seller.blade.php`
4. `resources/views/layouts/admin.blade.php`
5. Shared Blade components in `resources/views/components/`
6. Marketing and dashboard pages
7. Auction listing and detail flows
8. Seller and admin CRUD surfaces
9. SEO metadata, structured data, sitemap

## UI Decisions Locked From Docs

- Use the Trust Blue palette from [docs/arhitektura/18-ui-design-guidelines.md](/Users/zeljkodzafic/Documents/aukcije.ba/docs/arhitektura/18-ui-design-guidelines.md).
- Default typography is `Inter`; prices and countdowns use a monospace face.
- Layout is mobile-first with minimum `44x44px` touch targets.
- Auction cards, badges, buttons, and countdown behavior should mirror [docs/arhitektura/07-frontend-structure.md](/Users/zeljkodzafic/Documents/aukcije.ba/docs/arhitektura/07-frontend-structure.md).

## Required Data Contracts Before UI Implementation

Codex should not start detail-level UI files until these are present:

- Auth redirect contract for buyer, seller, moderator, and super admin
- Auction listing payload with image, title, category, current price, bid count, watcher count, location, and end time
- Auction detail payload with seller summary, shipping options, bid history, and related auctions
- Echo event names and payloads for `BidPlaced`, `AuctionExtended`, and `AuctionEnded`
- Wallet transaction shape and order status enum labels in BHS
- Admin dashboard metrics and chart datasets

## Definition of Done For Codex PRs

- Every new page uses an existing layout and shared components instead of duplicating markup.
- Copy is in BHS and matches platform terminology from the docs.
- Mobile layout is first-class, not a desktop layout squeezed down.
- Accessibility states are present: focus rings, labels, error text, empty states, loading states.
- The matching feature test, Livewire test, or component test ships in the same change.

## Next Practical Step

After `T-100`, create the foundational frontend slice in a single PR:

- `T-400` Base layouts
- `T-401` Core components: `button`, `input`, `card`, `badge`, `alert`, `avatar`
- `T-402` Landing page

That unlocks the rest of the Codex queue with the least rework.

## Prepared Specs In This Repository

- [docs/frontend/01-layouts-spec.md](/Users/zeljkodzafic/Documents/aukcije.ba/docs/frontend/01-layouts-spec.md)
- [docs/frontend/02-components-spec.md](/Users/zeljkodzafic/Documents/aukcije.ba/docs/frontend/02-components-spec.md)
- [docs/frontend/03-landing-page-spec.md](/Users/zeljkodzafic/Documents/aukcije.ba/docs/frontend/03-landing-page-spec.md)
- [docs/frontend/04-auth-pages-spec.md](/Users/zeljkodzafic/Documents/aukcije.ba/docs/frontend/04-auth-pages-spec.md)
- [docs/frontend/05-auction-listing-spec.md](/Users/zeljkodzafic/Documents/aukcije.ba/docs/frontend/05-auction-listing-spec.md)
- [docs/frontend/06-auction-detail-spec.md](/Users/zeljkodzafic/Documents/aukcije.ba/docs/frontend/06-auction-detail-spec.md)
- [docs/frontend/07-bidding-console-spec.md](/Users/zeljkodzafic/Documents/aukcije.ba/docs/frontend/07-bidding-console-spec.md)
- [docs/frontend/08-buyer-dashboard-spec.md](/Users/zeljkodzafic/Documents/aukcije.ba/docs/frontend/08-buyer-dashboard-spec.md)
- [docs/frontend/09-watchlist-spec.md](/Users/zeljkodzafic/Documents/aukcije.ba/docs/frontend/09-watchlist-spec.md)
- [docs/frontend/10-seller-dashboard-spec.md](/Users/zeljkodzafic/Documents/aukcije.ba/docs/frontend/10-seller-dashboard-spec.md)
- [docs/frontend/11-create-auction-spec.md](/Users/zeljkodzafic/Documents/aukcije.ba/docs/frontend/11-create-auction-spec.md)
- [docs/frontend/12-seller-orders-spec.md](/Users/zeljkodzafic/Documents/aukcije.ba/docs/frontend/12-seller-orders-spec.md)
- [docs/frontend/13-wallet-spec.md](/Users/zeljkodzafic/Documents/aukcije.ba/docs/frontend/13-wallet-spec.md)
