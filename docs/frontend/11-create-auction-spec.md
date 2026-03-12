# 11 - Create Auction Specification

## Scope

Ovaj dokument razrađuje `T-501: Create/Edit Auction Form`.

## Files To Create

- `app/Livewire/CreateAuction.php`
- pripadajući Blade view

## Flow

Multi-step forma sa pet koraka:

1. Osnovni podaci
2. Slike
3. Cijena i tip aukcije
4. Dostava
5. Trajanje i pregled

## Step Details

### Step 1: Osnovno
- Naslov
- Kategorija
- Opis
- Stanje artikla

### Step 2: Slike
- Drag and drop upload
- Max 10 slika
- Reorder slika
- Oznaka primarne slike

### Step 3: Cijena
- Startna cijena
- Reserve cijena
- `Kupi odmah` opcija
- Tip aukcije
- Feature flag check za holandsku aukciju ako ikad bude aktivna

### Step 4: Dostava
- Dostavne opcije
- Cijena dostave
- Lokacija slanja
- Lokalno preuzimanje toggle

### Step 5: Trajanje i Preview
- Trajanje aukcije
- Anti-sniping toggle
- Full preview prije objave

## Draft / Edit Rules

- Autosave draft gdje je moguće
- Edit mode ograničen na draft ili dozvoljena polja na aktivnoj aukciji
- Jasno razlikovati `Sačuvaj draft` i `Objavi aukciju`

## Tier Constraints

- Free: 5 aktivnih aukcija
- Premium: 50
- Storefront: neograničeno

Ako limit bude dosegnut, forma mora prikazati upgrade blok umjesto tihe greške.

## Test Checklist

- Svaki korak validira samo relevantna polja
- Preview prikazuje iste podatke kao finalna aukcija
- Tier limit error je razumljiv i vodi ka upgrade toku
