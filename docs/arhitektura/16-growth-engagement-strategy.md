# 16 - Growth & Engagement Strategy

## Akvizicija Korisnika

### SEO Strategy

- **Dinamički meta tagovi** za svaku aukciju (title, description, OG image)
- **Structured data** (JSON-LD) za Product schema → bogati Google rezultati
- **Sitemap.xml** automatski generisan sa svim aktivnim aukcijama
- **Landing stranice** po kategorijama: `/elektronika`, `/automobili`, `/kolekcionarstvo`
- **Blog sadržaj:** "Kako sigurno kupovati online", "Vodič za prodavce", itd.

```html
<!-- Primjer: meta tagovi za aukciju -->
<title>Samsung Galaxy S24 - Aukcija | Trenutna cijena: 250 BAM</title>
<meta name="description" content="Licitirajte za Samsung Galaxy S24 Ultra 256GB. Trenutna cijena 250 BAM. Aukcija završava za 2 dana.">
<meta property="og:image" content="https://aukcije.ba/images/auctions/samsung-s24.jpg">

<!-- Structured data -->
<script type="application/ld+json">
{
  "@type": "Product",
  "name": "Samsung Galaxy S24",
  "offers": {
    "@type": "Offer",
    "price": "250.00",
    "priceCurrency": "BAM",
    "availability": "InStock"
  }
}
</script>
```

### Social Media

| Platforma | Strategija |
|-----------|-----------|
| Facebook | "Aukcija dana" post, grupe za kupoprodaju |
| Instagram | Slike zanimljivih aukcija, Stories countdown |
| TikTok | Reakcije na zadnje sekunde aukcija, unboxing |

### Referral System

```
Postojeći prodavac pozove novog prodavca:
  → Novi prodavac registruje se sa referral kodom
  → Novi prodavac dovrši KYC verifikaciju
  → Postojeći prodavac dobije: 1 mjesec Premium besplatno
  → Novi prodavac dobije: 3 promoted listinga besplatno
```

---

## Engagement

### "Ending Soon" Notifikacije

Najjači engagement mehanizam na aukcijskoj platformi:

```
Watchlist aukcija završava za:
  24h → Email reminder
  1h  → Push notification
  15m → Push + in-app alert
  5m  → Real-time countdown prominent na dashboardu
```

### Gamification

| Mehanizam | Opis |
|-----------|------|
| **Bid streak** | "Licitirao si 5 dana zaredom!" → Badge |
| **First win** | "Tvoja prva pobjeda!" → Confetti animation |
| **Seller milestone** | "50. uspješna prodaja!" → Power Seller badge |
| **Watchlist digest** | Sedmični email sa statusom praćenih aukcija |

### Personalizacija

- **Preporuke** na osnovu historije bidovanja i watchliste
- **"Slične aukcije"** sekcija na svakoj auction detail stranici
- **"Aukcije iz tvog grada"** — lokalne ponude prvo
- **Saved searches** sa email alertima za nove aukcije

---

## Retencija

### Trust Building

| Feature | Efekat |
|---------|--------|
| Verified badge | +40% vjerovatnoća da kupac licitira |
| Escrow zaštita | Eliminira strah od prevare |
| Transparentne ocjene | Korisnici se vraćaju pouzdanim prodavcima |
| Dispute resolution | Korisnici znaju da imaju zaštitu |

### Re-engagement

```
Korisnik neaktivan 7 dana:
  → Email: "Aukcije koje te mogu zanimati" (personalizirano)

Korisnik neaktivan 14 dana:
  → Email: "Propuštaš odlične ponude" + 1 besplatni promoted listing

Korisnik neaktivan 30 dana:
  → Email: "Tvoj nalog te čeka" + popust na Premium
```

### Seasonal Campaigns

| Period | Kampanja |
|--------|---------|
| Januar | "Novo Godišnja Rasprodaja" — snižene komisije |
| Mart | "Proljetno Čišćenje" — bonus za listinge |
| Septembar | "Back to School Tech" — kategorijski fokus |
| Novembar | "Black Friday Aukcije" — specijalni format aukcija |
| Decembar | "Pokloni na Aukciji" — gift-wrapping opcija |

---

## Metrics & KPIs

| Metrika | Cilj (mjesečno) |
|---------|-----------------|
| New registrations | +15% MoM |
| Activation rate (first bid) | > 30% |
| Seller conversion (listing → sale) | > 40% |
| Repeat buyer rate | > 50% |
| NPS score | > 40 |
| Churn rate (sellers) | < 10% |
