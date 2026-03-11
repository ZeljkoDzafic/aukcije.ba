# 05 - API Design

## Dva API Sloja

### Sloj 1: Laravel Web Routes (Livewire)

Za standardne web operacije. Livewire komponente komuniciraju sa serverom bez pisanja API endpointova.

```php
// Primjer: Livewire komponenta za pretragu aukcija
class AuctionSearch extends Component
{
    public string $query = '';
    public string $category = '';
    public string $sort = 'ending_soon';

    public function render()
    {
        $auctions = Auction::query()
            ->active()
            ->when($this->query, fn($q) => $q->search($this->query))
            ->when($this->category, fn($q) => $q->inCategory($this->category))
            ->sort($this->sort)
            ->paginate(24);

        return view('livewire.auction-search', compact('auctions'));
    }
}
```

### Sloj 2: RESTful JSON API (za mobilne apps i verified sellers)

```
BASE_URL: https://api.aukcije.ba/v1
Auth: Bearer token (Sanctum)
Format: JSON
Versioning: URL prefix /v1/
```

#### Auctions

```
GET    /auctions                    Filtrirana lista aukcija
GET    /auctions/{id}               Detalji aukcije
POST   /auctions                    Kreiraj novu aukciju (seller+)
PUT    /auctions/{id}               Ažuriraj aukciju (owner, status=draft)
DELETE /auctions/{id}               Otkaži aukciju (owner, rules apply)
GET    /auctions/{id}/bids          Lista bidova za aukciju
POST   /auctions/{id}/bid           Postavi bid (atomic endpoint)
POST   /auctions/{id}/watch         Dodaj u watchlist
DELETE /auctions/{id}/watch         Ukloni iz watchliste
```

#### Users

```
GET    /users/me                    Trenutni korisnik
PUT    /users/me                    Ažuriraj profil
GET    /users/me/auctions           Moje aukcije (seller)
GET    /users/me/bids               Moji bidovi
GET    /users/me/watchlist          Moja watchlista
GET    /users/me/orders             Moje narudžbe
GET    /users/me/wallet             Wallet balance
GET    /users/{id}/ratings          Ocjene korisnika (public)
```

#### Orders & Payments

```
POST   /orders/{id}/pay             Plati narudžbu
POST   /orders/{id}/ship            Označi kao poslano (seller)
POST   /orders/{id}/confirm         Potvrdi prijem (buyer)
POST   /orders/{id}/dispute         Otvori dispute
```

#### Messages

```
GET    /messages                    Lista konverzacija
GET    /messages/{userId}           Poruke sa korisnikom
POST   /messages/{userId}           Pošalji poruku
```

### Bidding Endpoint (Atomic)

Najkritičniji endpoint na platformi. Detaljno:

```
POST /auctions/{id}/bid
Content-Type: application/json
Authorization: Bearer {token}

{
  "amount": 25.50,
  "is_proxy": false,
  "max_proxy_amount": null    // samo ako is_proxy = true
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "bid_id": "uuid",
    "auction_id": "uuid",
    "amount": 25.50,
    "current_price": 25.50,
    "next_minimum_bid": 26.50,
    "auction_ends_at": "2024-03-15T18:03:00Z",
    "was_extended": true
  }
}
```

**Error Responses:**

| Status | Razlog |
|--------|--------|
| 400 | Bid ispod minimuma |
| 403 | Seller ne može licitirati na vlastitoj aukciji |
| 409 | Conflict — neko je postavio veći bid u međuvremenu |
| 410 | Aukcija je završena |
| 422 | Validacijska greška |
| 429 | Rate limited — previše bidova |

### WebSocket Kanali (Laravel Reverb)

```javascript
// Javni kanal — svi mogu slušati
Echo.channel(`auction.${auctionId}`)
  .listen('BidPlaced', (e) => {
    // Ažuriraj cijenu, bid count, timer
    updateAuctionCard(e.auction_id, e.current_price, e.ends_at);
  })
  .listen('AuctionEnded', (e) => {
    // Prikaži rezultat
    showAuctionResult(e.auction_id, e.winner_id, e.final_price);
  });

// Privatni kanal — samo autentificirani korisnik
Echo.private(`user.${userId}`)
  .listen('OutbidNotification', (e) => {
    showNotification(`Nadlicitirani ste na "${e.auction_title}"`);
  })
  .listen('AuctionWon', (e) => {
    showNotification(`Čestitamo! Dobili ste "${e.auction_title}" za ${e.price}`);
  });
```

### API Response Format (standardizirani)

```json
// Uspjeh
{
  "success": true,
  "data": { ... },
  "meta": {
    "current_page": 1,
    "per_page": 24,
    "total": 156
  }
}

// Greška
{
  "success": false,
  "error": {
    "code": "BID_TOO_LOW",
    "message": "Vaša ponuda mora biti najmanje 26.50 BAM",
    "details": {
      "minimum_bid": 26.50,
      "current_price": 25.50
    }
  }
}
```
