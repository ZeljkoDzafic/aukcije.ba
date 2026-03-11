# 07 - Frontend Structure

## Arhitektura

```
resources/
├── views/
│   ├── layouts/
│   │   ├── guest.blade.php          -- Landing, login, register
│   │   ├── app.blade.php            -- Authenticated user layout
│   │   └── admin.blade.php          -- Admin panel layout
│   │
│   ├── pages/
│   │   ├── home.blade.php           -- Landing page
│   │   ├── auctions/
│   │   │   ├── index.blade.php      -- Auction listing + search
│   │   │   └── show.blade.php       -- Single auction detail
│   │   ├── dashboard.blade.php      -- User dashboard
│   │   └── static/                  -- About, FAQ, Terms, etc.
│   │
│   ├── livewire/
│   │   ├── auction-search.blade.php
│   │   ├── auction-card.blade.php
│   │   ├── bidding-console.blade.php
│   │   ├── countdown-timer.blade.php
│   │   ├── watchlist.blade.php
│   │   ├── wallet-balance.blade.php
│   │   ├── message-thread.blade.php
│   │   └── notification-bell.blade.php
│   │
│   ├── components/
│   │   ├── alert.blade.php
│   │   ├── button.blade.php
│   │   ├── modal.blade.php
│   │   ├── badge.blade.php
│   │   ├── price-display.blade.php
│   │   └── image-gallery.blade.php
│   │
│   └── admin/
│       ├── dashboard.blade.php
│       ├── auctions/
│       ├── users/
│       ├── categories/
│       ├── disputes/
│       └── settings/
│
├── js/
│   ├── app.js                       -- Laravel Echo setup, global JS
│   ├── echo.js                      -- WebSocket configuration
│   ├── countdown.js                 -- Client-side countdown sync
│   └── bidding.js                   -- Vue.js bidding console
│
├── css/
│   └── app.css                      -- Tailwind directives
│
└── vue/                             -- Vue.js components (complex UIs only)
    ├── BiddingConsole.vue
    ├── AuctionTimer.vue
    └── ImageUploader.vue
```

## Ključne Komponente

### 1. AuctionCard (Livewire)

Prikazuje aukciju u grid/list prikazu sa real-time ažuriranjem.

```
┌─────────────────────────────┐
│  ┌─────────┐                │
│  │  IMAGE   │  Samsung S24  │
│  │          │  ─────────    │
│  └─────────┘  Elektronika   │
│                             │
│  Trenutna cijena: 125 BAM   │
│  ██████████░░ 3 dana 4h     │
│  👁 23 watchera  🏷 15 bids │
│                             │
│  [Licitiraj]  [♡ Watchlist] │
└─────────────────────────────┘
```

### 2. BiddingConsole (Vue.js)

Kompleksna komponenta za postavljanje bidova sa real-time feedback-om.

```
┌─────────────────────────────────┐
│  Trenutna cijena    125.00 BAM  │
│  Minimalni bid      127.00 BAM  │
│  ──────────────────────────     │
│  Vaš bid: [  130.00  ] BAM     │
│                                 │
│  ☐ Proxy bid (max: ____ BAM)   │
│                                 │
│  [ LICITIRAJ ]                  │
│                                 │
│  Aukcija završava za:          │
│  02 : 14 : 33 : 07             │
│  dana  sati  min   sek         │
└─────────────────────────────────┘
```

### 3. CountdownTimer

Client-side timer koji se sinkronizuje sa serverskim vremenom.

```javascript
// countdown.js
class AuctionCountdown {
    constructor(endAt, elementId) {
        this.endAt = new Date(endAt);
        this.el = document.getElementById(elementId);
        this.serverOffset = 0; // kalibrirano sa server NTP
    }

    start() {
        this.interval = setInterval(() => this.tick(), 1000);
    }

    tick() {
        const now = new Date(Date.now() + this.serverOffset);
        const diff = this.endAt - now;

        if (diff <= 0) {
            clearInterval(this.interval);
            this.el.textContent = 'ZAVRŠENO';
            return;
        }

        const d = Math.floor(diff / 86400000);
        const h = Math.floor((diff % 86400000) / 3600000);
        const m = Math.floor((diff % 3600000) / 60000);
        const s = Math.floor((diff % 60000) / 1000);

        this.el.textContent = `${d}d ${h}h ${m}m ${s}s`;
    }

    // Ažuriraj end time (anti-sniping produženje)
    extend(newEndAt) {
        this.endAt = new Date(newEndAt);
    }
}
```

### 4. WebSocket Setup (Laravel Echo)

```javascript
// echo.js
import Echo from 'laravel-echo';

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT,
    forceTLS: true,
    enabledTransports: ['ws', 'wss'],
});
```

## Assets Build (Vite)

```javascript
// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/vue/BiddingConsole.vue',
            ],
            refresh: true,
        }),
        vue(),
    ],
});
```

## Responsive Design

| Breakpoint | Prikaz | Fokus |
|-----------|--------|-------|
| < 640px (mobile) | 1 kolona, stacked layout | Bidding button uvijek vidljiv |
| 640-1024px (tablet) | 2 kolone grid | Sidebar za filtere |
| > 1024px (desktop) | 3-4 kolone grid | Full featured layout |

**Mobile-first pristup** — 70% saobraćaja se očekuje sa mobilnih uređaja.
Svi auction card-ovi moraju biti "thumb-friendly" (min touch target 44x44px).
