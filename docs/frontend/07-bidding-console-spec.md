# 07 - Bidding Console Specification

## Scope

Ovaj dokument razrađuje `T-405: BiddingConsole (Vue.js)`.

## Files To Create

- `resources/vue/BiddingConsole.vue`
- `resources/vue/AuctionTimer.vue`
- Optional helper entry in `resources/js/bidding.js`

## Required Props / Data

Bidding console ne treba implementirati dok backend ne isporuči:

- `auctionId`
- `currentPrice`
- `minimumBid`
- `endsAt`
- `currency`
- `userLeading`
- `canBid`
- endpoint URL za `POST /auctions/{id}/bid`

## Interaction Model

### Default State
- Prikaz trenutne cijene
- Prikaz minimalnog bida
- Input za amount
- Proxy bid toggle
- CTA `Licitiraj`

### Proxy Flow
- Kada je proxy uključen, pojavljuje se `Maksimalni proxy bid`
- Client-side validacija mora spriječiti proxy max manji od minimalnog bida

### Submit Flow

1. Disable CTA
2. Prikaži loading state
3. Pošalji bid request
4. Na success:
   - ažuriraj cijenu
   - resetuj relevantna polja
   - prikaži success feedback
5. Na error:
   - prikaži BHS poruku iz API response-a

## Realtime Events

### Public Channel `auction.{id}`
- `BidPlaced`: update current price, next minimum bid, bid history preview
- `AuctionExtended`: update `endsAt`
- `AuctionEnded`: zaključa komponentu i pokaže rezultat

### Private Channel `user.{id}`
- `OutbidNotification`: alert banner ili toast
- `AuctionWon`: celebratory success state

## AuctionTimer Rules

- Zelena boja: više od 1h
- Narandžasta: manje od 1h
- Crvena: manje od 5 min
- Pulsing state: zadnje 2 minute
- Kad vrijeme istekne, prikaz `Završeno`

## Error Mapping

- `400` -> prenizak bid
- `403` -> nije dozvoljeno licitirati
- `409` -> neko vas je pretekao u međuvremenu
- `410` -> aukcija završena
- `429` -> previše pokušaja, sačekajte

## Animation Notes

- Ne koristiti teške animacije na svakoj promjeni cijene
- Confetti koristiti samo za win state ili eksplicitni success milestone
- Outbid feedback mora biti primjetan, ali ne smije blokirati formu

## Test Checklist

- Client-side validation blokira očigledno nevalidan request
- Success response pravilno ažurira state
- Echo event pravilno mijenja timer i cijenu
- Auction end zaključava unos i submit akciju
