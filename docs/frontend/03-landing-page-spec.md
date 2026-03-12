# 03 - Landing Page Specification

## Scope

Ovaj dokument razrađuje `T-402: Landing Page`.

## Route And Template

- Route: `/`
- View: `resources/views/pages/home.blade.php`
- Layout: `layouts.guest`

## Page Sections

## Hero

- H1 fokusiran na regionalnu aukcijsku platformu
- Kratki supporting copy o sigurnosti, real-time bidovima i escrow zaštiti
- CTA dugmad:
  - `Počni licitirati`
  - `Prodaj odmah`
- Vizual desno: istaknuta aukcija ili kompozit auction card preview

## Featured Auctions

- Grid od 6 kartica
- Fokus na `ending soon`
- Svaka kartica prikazuje: sliku, naslov, trenutnu cijenu, countdown, broj bidova

## Categories

- Grid popularnih kategorija sa ikonama
- Minimum: `Elektronika`, `Satovi`, `Auto oprema`, `Kolekcionarstvo`, `Dom`, `Moda`

## How It Works

Tri koraka:

1. `Registruj se`
2. `Licitiraj ili objavi aukciju`
3. `Pobijedi i završi kupovinu sigurno`

## Trust Section

Istaknuti benefiti:

- Escrow zaštita
- Verified seller bedževi
- Sistem ocjena
- Anti-sniping zaštita

## Stats Strip

Prikaz 3 do 4 metrike:

- aktivne aukcije
- registrirani korisnici
- uspješno završene transakcije
- procenat pozitivnih ocjena

Vrijednosti mogu biti placeholder dok backend ne isporuči prave metrike.

## SEO Requirements

- Jedan jasan `h1`
- Meta title i description definisani po view-u
- OpenGraph title, description i image
- Semantični landmark elementi: `header`, `main`, `section`, `footer`
- Sve category i auction slike imaju `alt`

## Responsive Notes

- Mobile: single-column flow, CTA dugmad stacked
- Tablet: hero prelazi u 2 kolone
- Desktop: featured auctions u 3-kolonskom gridu
- Sticky CTA nije potreban na landing stranici

## Test Checklist

- Guest korisnik vidi sve sekcije bez auth zavisnosti
- CTA linkovi vode na validne rute ili named route placeholders
- Heading hierarchy nema skokova
- Featured kartice se uredno lome na `sm`, `md`, `lg` breakpointima
