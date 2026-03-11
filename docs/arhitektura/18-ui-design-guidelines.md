# 18 - UI & Design Guidelines

## Brand Identity

### Ime i Tagline
- **Ime:** (TBD — npr. "Aukcije.ba", "BidBalkan", "LicitBa")
- **Tagline:** "Licitiraj. Pobijedi. Uživaj."

### Brand Vrijednosti
- **Povjerenje** — Sigurna platforma za kupoprodaju
- **Brzina** — Real-time, instant feedback
- **Jednostavnost** — Lako za korištenje, čak i za neiskusne
- **Fer igra** — Anti-sniping, transparentnost, escrow

---

## Color Palette

### Primary: "Trust Blue"

```
Primary:        #1E40AF  (Trust Blue — glavni CTA, linkovi)
Primary Light:  #3B82F6  (Hover states, secondary elementi)
Primary Dark:   #1E3A8A  (Header, footer)

Secondary:      #F59E0B  (Amber — "Ending Soon", urgency)
Secondary Dark: #D97706  (Hover za urgency elemente)

Success:        #10B981  (Emerald — potvrde, "Sold!", winning bid)
Danger:         #EF4444  (Red — greške, "Outbid!", warnings)
Warning:        #F59E0B  (Amber — upozorenja)

Neutral:
  50:  #F8FAFC
  100: #F1F5F9
  200: #E2E8F0
  300: #CBD5E1
  500: #64748B
  700: #334155
  900: #0F172A

Background:     #F8FAFC  (Light grey — čist, prostran)
Surface:        #FFFFFF  (Card backgrounds)
Text Primary:   #0F172A  (Slate 900)
Text Secondary: #64748B  (Slate 500)
```

### Korištenje Boja

| Kontekst | Boja | Razlog |
|----------|------|--------|
| CTA button (Licitiraj) | Primary Blue | Povjerenje, profesionalnost |
| Countdown timer (< 1h) | Secondary Amber | Urgency |
| Winning bid | Success Green | Pozitivan feedback |
| Outbid alert | Danger Red | Hitnost, akcija potrebna |
| Featured badge | Amber gradient | Vizualno izdvajanje |

---

## Typography

```
Headings:  Inter (Google Fonts) — čist, moderan, odlična čitljivost
Body text: Inter — isti font za konzistentnost
Monospace: JetBrains Mono — cijene, bidovi, countdown
```

| Element | Size | Weight | Line Height |
|---------|------|--------|-------------|
| H1 (Page title) | 2rem (32px) | 700 | 1.2 |
| H2 (Section) | 1.5rem (24px) | 600 | 1.3 |
| H3 (Card title) | 1.25rem (20px) | 600 | 1.4 |
| Body | 1rem (16px) | 400 | 1.5 |
| Small | 0.875rem (14px) | 400 | 1.5 |
| Price (large) | 1.5rem (24px) | 700 | 1.2 |
| Countdown | 1.25rem (20px) | 600 (mono) | 1.0 |

---

## Component Styles

### Auction Card

```
┌─────────────────────────────────────┐
│ ┌─────────────────────┐             │
│ │                     │  ♡          │
│ │     PRODUCT IMAGE   │             │
│ │                     │  ⭐ FEATURED │
│ └─────────────────────┘             │
│                                     │
│  Samsung Galaxy S24 Ultra           │  ← H3, 600 weight
│  Elektronika > Mobiteli             │  ← Small, text-secondary
│                                     │
│  Trenutna cijena:                   │
│  250.00 BAM                         │  ← Price, bold, primary
│                                     │
│  ████████████░░░░  2d 14h           │  ← Progress bar + countdown
│                                     │
│  👁 45  |  🏷 23 bida  |  📍 Sarajevo │
│                                     │
│  ┌─────────────┐  ┌──────────────┐  │
│  │  LICITIRAJ  │  │ ♡ WATCHLIST  │  │  ← Primary + Outline buttons
│  └─────────────┘  └──────────────┘  │
└─────────────────────────────────────┘

Card: white bg, rounded-xl, shadow-sm, hover:shadow-md
Image: aspect-ratio 4:3, object-cover
Featured badge: amber gradient, positioned top-right
```

### Buttons

```css
/* Primary (Licitiraj) */
.btn-primary {
  @apply bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg
         hover:bg-blue-800 active:bg-blue-900
         transition-colors duration-150;
}

/* Secondary (Watchlist) */
.btn-secondary {
  @apply border-2 border-blue-700 text-blue-700 font-semibold px-6 py-3 rounded-lg
         hover:bg-blue-50
         transition-colors duration-150;
}

/* Danger (Cancel, Remove) */
.btn-danger {
  @apply bg-red-500 text-white font-semibold px-6 py-3 rounded-lg
         hover:bg-red-600;
}

/* Success (Confirm receipt) */
.btn-success {
  @apply bg-emerald-500 text-white font-semibold px-6 py-3 rounded-lg
         hover:bg-emerald-600;
}
```

### Badge Styles

| Badge | Stil |
|-------|------|
| Featured | `bg-amber-100 text-amber-800 border-amber-300` |
| Verified Seller | `bg-blue-100 text-blue-800 border-blue-300` + ✓ icon |
| Top Rated | `bg-emerald-100 text-emerald-800` + ⭐ icon |
| Ending Soon | `bg-red-100 text-red-800` + pulsing dot |
| New | `bg-purple-100 text-purple-800` |

---

## Accessibility (WCAG 2.1 AA)

| Zahtjev | Implementacija |
|---------|---------------|
| Color contrast | Min 4.5:1 za text, 3:1 za large text |
| Focus indicators | Vidljiv focus ring na svim interaktivnim elementima |
| Alt text | Sve slike aukcija imaju alt text |
| Keyboard navigation | Tab kroz sve akcije, Enter za submit |
| Screen reader | ARIA labels na custom komponentama |
| Touch targets | Min 44x44px za mobilne uređaje |

**Napomena:** Visok kontrast je posebno važan jer regionalna kolekcionar zajednica ima stariju demografiju.

---

## Mobile-First Design Principles

1. **Bidding button uvijek vidljiv** — sticky footer na auction detail stranici
2. **Swipe gestures** — swipe left za watchlist, swipe right za bid
3. **Thumb-friendly zones** — ključne akcije u donjem dijelu ekrana
4. **Minimalna tipkanja** — predložene bid vrijednosti, one-tap bid
5. **Kompresovane slike** — WebP format, lazy loading, placeholder blur
6. **Offline friendly** — cached aukcija sa "last known price" indikatorom

### Responsive Breakpoints (Tailwind)

```
sm:  640px   — Telefon (landscape)
md:  768px   — Tablet
lg:  1024px  — Laptop
xl:  1280px  — Desktop
2xl: 1536px  — Large desktop
```

### Grid Layout

| Breakpoint | Auction Grid | Sidebar |
|-----------|-------------|---------|
| < 640px | 1 kolona | Hidden (bottom sheet filter) |
| 640-1024px | 2 kolone | Collapsible |
| > 1024px | 3-4 kolone | Always visible |
