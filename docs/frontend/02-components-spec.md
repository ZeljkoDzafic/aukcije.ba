# 02 - Components Specification

## Scope

Ovaj dokument razrađuje `T-401: UI Components Library`.

## Core Components First

Prvi PR treba sadržavati ove komponente:

1. `button.blade.php`
2. `input.blade.php`
3. `card.blade.php`
4. `badge.blade.php`
5. `alert.blade.php`
6. `avatar.blade.php`

Drugi talas:

1. `modal.blade.php`
2. `select.blade.php`
3. `price-display.blade.php`
4. `countdown-timer.blade.php`
5. `pagination.blade.php`
6. `progress-bar.blade.php`
7. `toast.blade.php`
8. `data-table.blade.php`
9. `image-gallery.blade.php`

## API Expectations

Komponente trebaju imati predvidive props-e i male, stabilne varijante.

### Button
- Varijante: `primary`, `secondary`, `danger`, `success`, `ghost`
- Veličine: `sm`, `md`, `lg`
- States: `loading`, `disabled`
- Podržati leading/trailing icon slot

### Input
- Tipovi: `text`, `email`, `password`, `number`, `textarea`
- Label, hint i error poruka kao standardni API
- Optional prefix/suffix za valutu ili ikonice

### Card
- Default: white surface, rounded-xl, soft shadow
- Opcionalni `header`, `footer`, `actions` slotovi

### Badge
- Varijante: `featured`, `verified`, `ending-soon`, `new`, `success`, `danger`

### Avatar
- Slika ili fallback inicijali
- Veličine: `xs`, `sm`, `md`, `lg`

## Styling Rules

- Ne duplicirati utility stringove po viewovima; centralizovati kroz komponente
- Focus ring mora biti konzistentan kroz cijeli UI
- Error state koristi crvenu samo za validacijski problem, ne za neutralne statuse
- Countdown i price prikazi koriste monospace numerale

## BHS Copy Conventions

- `Licitiraj`
- `Dodaj u praćenje`
- `Sačuvaj promjene`
- `Greška pri učitavanju`
- `Nema rezultata`

Ne miješati engleske CTA-je sa BHS interfejsom.

## Test Checklist

- Svaka komponenta ima barem jedan render test
- Disabled i loading state ne šalju form submit
- Error i hint tekstovi imaju pravilno povezane `id` atribute
- Komponente rade u light temi bez oslanjanja na page-specific CSS
