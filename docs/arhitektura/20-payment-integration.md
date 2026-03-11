# 20 - Payment Integration Architecture

## Payment Strategy

```
Princip: NIKAD ne dodirujemo kartične podatke
Strategija: SAQ A (Outsourced) — redirect na hosted payment page

Podržani gateway-i:
  1. Stripe Checkout (international, EUR/USD)
  2. Monri WebPay (BiH, BAM)
  3. CorvusPay (HR, EUR)
  4. Interni Wallet (preloaded balance)
```

## Payment Flow

### 1. Wallet Deposit Flow

```
Buyer klikne "Dopuni Wallet"
    │
    ▼
Izabere iznos (10, 25, 50, 100, custom) + gateway
    │
    ├── Stripe → Redirect to Stripe Checkout
    │              │
    │              ▼
    │          Stripe Hosted Page (card input)
    │              │
    │              ▼
    │          Success → Webhook: payment_intent.succeeded
    │              │
    │              ▼
    │          ProcessStripeWebhook job
    │              │
    │              ▼
    │          WalletService::deposit($user, $amount)
    │
    ├── Monri → Redirect to Monri WebPay Form
    │              │
    │              ▼
    │          Monri Hosted Page (card input, supports Activa/Visa BA)
    │              │
    │              ▼
    │          Callback → ProcessMonriCallback
    │              │
    │              ▼
    │          Verify signature → deposit
    │
    └── CorvusPay → Similar redirect flow
```

### 2. Auction Win → Payment Flow

```
Auction ends → Winner determined
    │
    ▼
Order created (status: pending_payment)
    │
    ▼
Buyer receives email: "Čestitamo! Platite u roku od 3 dana"
    │
    ▼
Buyer visits /orders/{id}/pay
    │
    ├── Wallet balance sufficient?
    │   ├── Yes → One-click: "Plati iz Walleta"
    │   │         → EscrowService::holdFunds($order)
    │   │         → Order status → payment_received
    │   │
    │   └── No → "Nedovoljan balans. Dopunite wallet ili platite karticom."
    │            → Redirect to gateway
    │            → On success: deposit to wallet → then escrow hold
    │
    ▼
Seller notified: "Uplata primljena, pošaljite artikal u roku od 5 dana"
```

### 3. Seller Payout Flow

```
Order completed (buyer confirmed delivery)
    │
    ▼
EscrowService::releaseFunds($order)
    │
    ├── Calculate commission: amount × tier_rate (8%/5%/3%)
    ├── Platform wallet += commission
    ├── Seller wallet += (amount - commission)
    │
    ▼
Seller can withdraw:
    │
    ▼
POST /wallet/withdraw
    ├── Amount + bank details
    ├── Validation: min 10 BAM, max wallet balance
    ├── Admin approval for > 500 BAM
    │
    ▼
Admin approves withdrawal
    │
    ▼
Manual bank transfer (MVP) / Automated via Monri Payout API (Phase 2)
    │
    ▼
WalletService::withdraw($user, $amount)
    └── wallet_transactions record: type=withdrawal
```

## Gateway Adapter Pattern

```php
// app/Contracts/PaymentGateway.php
interface PaymentGateway
{
    public function createCheckout(float $amount, string $currency, array $meta): CheckoutResult;
    public function verifyWebhook(Request $request): WebhookPayload;
    public function refund(string $transactionId, float $amount): RefundResult;
    public function getTransactionStatus(string $transactionId): string;
}

// app/Services/Gateways/StripeGateway.php
class StripeGateway implements PaymentGateway
{
    public function createCheckout(float $amount, string $currency, array $meta): CheckoutResult
    {
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'mode' => 'payment',
            'line_items' => [[
                'price_data' => [
                    'currency' => strtolower($currency),
                    'unit_amount' => (int) ($amount * 100),
                    'product_data' => ['name' => $meta['description'] ?? 'Wallet Deposit'],
                ],
                'quantity' => 1,
            ]],
            'success_url' => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('payment.cancel'),
            'metadata' => $meta,
        ]);

        return new CheckoutResult(
            redirectUrl: $session->url,
            transactionId: $session->id,
        );
    }
}

// app/Services/Gateways/MonriGateway.php
class MonriGateway implements PaymentGateway
{
    // Monri WebPay integration
    // Supports: Visa, MasterCard, Activa (BiH local cards)
    // Currency: BAM (convertible mark)
    // Documentation: https://ipg.monri.com/en/documentation
}

// app/Services/PaymentService.php
class PaymentService
{
    public function processPayment(string $gateway, float $amount, string $currency, array $meta): CheckoutResult
    {
        $adapter = match ($gateway) {
            'stripe' => app(StripeGateway::class),
            'monri' => app(MonriGateway::class),
            'corvuspay' => app(CorvusPayGateway::class),
            'wallet' => app(WalletGateway::class),
            default => throw new UnsupportedGatewayException($gateway),
        };

        $result = $adapter->createCheckout($amount, $currency, $meta);

        // Log payment attempt
        Payment::create([
            'user_id' => auth()->id(),
            'gateway' => $gateway,
            'gateway_transaction_id' => $result->transactionId,
            'amount' => $amount,
            'currency' => $currency,
            'status' => 'pending',
        ]);

        return $result;
    }
}
```

## Webhook Security

```php
// routes/webhooks.php (no CSRF, no auth)
Route::post('/webhooks/stripe', [StripeWebhookController::class, 'handle'])
    ->withoutMiddleware(['csrf', 'auth']);

Route::post('/webhooks/monri', [MonriWebhookController::class, 'handle'])
    ->withoutMiddleware(['csrf', 'auth']);

// Stripe webhook verification
class StripeWebhookController
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                config('services.stripe.webhook_secret')
            );
        } catch (\Exception $e) {
            Log::warning('Stripe webhook verification failed', ['error' => $e->getMessage()]);
            return response('Invalid signature', 400);
        }

        // Idempotency: check if already processed
        if (Payment::where('gateway_transaction_id', $event->data->object->id)
            ->where('status', 'completed')->exists()) {
            return response('Already processed', 200);
        }

        match ($event->type) {
            'checkout.session.completed' => $this->handleCheckoutComplete($event->data->object),
            'payment_intent.payment_failed' => $this->handlePaymentFailed($event->data->object),
            'charge.refunded' => $this->handleRefund($event->data->object),
            default => null,
        };

        return response('OK', 200);
    }
}

// Monri webhook verification (digest-based)
class MonriWebhookController
{
    public function handle(Request $request)
    {
        $digest = hash('sha512',
            config('services.monri.key') .
            $request->input('order_number') .
            $request->input('amount') .
            $request->input('currency')
        );

        if ($digest !== $request->input('digest')) {
            return response('Invalid digest', 400);
        }

        // Process payment...
    }
}
```

## Multi-Currency Support

```php
// config/currencies.php
return [
    'default' => 'BAM',
    'supported' => [
        'BAM' => ['name' => 'KM', 'symbol' => 'KM', 'decimals' => 2, 'gateway' => 'monri'],
        'EUR' => ['name' => 'Euro', 'symbol' => '€', 'decimals' => 2, 'gateway' => 'stripe'],
        'RSD' => ['name' => 'Dinar', 'symbol' => 'RSD', 'decimals' => 2, 'gateway' => 'stripe'],
    ],
    // Exchange rates updated daily via ECB API
    'exchange_rate_source' => 'https://api.exchangerate-api.com/v4/latest/BAM',
];

// Display: always show in auction's original currency
// Wallet: per-currency wallets (BAM wallet, EUR wallet)
// Payout: in seller's preferred currency
```

## Refund Policy

| Scenario | Refund Type | Who Pays | Timeline |
|----------|------------|----------|----------|
| Buyer wins, never pays | No refund needed | Buyer (strike) | Auto-cancel after 3 days |
| Item not received | Full refund from escrow | Seller (escrow held) | After dispute resolution |
| Item not as described | Full/partial from escrow | Seller | After dispute |
| Buyer returns item | Refund minus shipping | Seller | After item received back |
| Platform error | Full refund | Platform covers | Immediately |

```php
class RefundService
{
    public function processRefund(Order $order, float $amount, string $reason): void
    {
        DB::transaction(function () use ($order, $amount, $reason) {
            // 1. Refund to buyer wallet
            $this->walletService->deposit($order->buyer, $amount, 'refund');

            // 2. Debit from escrow hold
            $this->escrowService->releaseForRefund($order, $amount);

            // 3. Log
            WalletTransaction::create([
                'wallet_id' => $order->buyer->wallet->id,
                'type' => 'refund',
                'amount' => $amount,
                'reference_type' => 'order',
                'reference_id' => $order->id,
                'description' => "Refund: {$reason}",
            ]);

            // 4. If gateway payment, initiate gateway refund too
            if ($order->payment?->gateway !== 'wallet') {
                $gateway = $this->resolveGateway($order->payment->gateway);
                $gateway->refund($order->payment->gateway_transaction_id, $amount);
            }
        });
    }
}
```

## Financial Reporting

```
Daily:
  - Total deposits by gateway
  - Total commission earned
  - Total payouts processed
  - Escrow balance (held funds)
  - Failed transactions

Monthly:
  - GMV (Gross Merchandise Value)
  - Net revenue (commissions + featured fees)
  - Refund rate
  - Average transaction value
  - Revenue per user (ARPU)
  - Gateway fee breakdown

Quarterly:
  - Financial statement (for accounting)
  - Tax report (VAT/PDV if applicable)
  - Fraud loss report
```

## Compliance Checklist

- [ ] PCI-DSS SAQ A completed
- [ ] No card data stored, processed, or transmitted
- [ ] TLS 1.2+ on all payment pages
- [ ] Webhook signatures verified for all gateways
- [ ] Payment logs retained for 7 years
- [ ] Refund procedures documented
- [ ] AML (Anti-Money Laundering) checks for large transactions (> 5000 BAM)
- [ ] KYC required for seller payouts
- [ ] Transaction limits per user tier
- [ ] Quarterly reconciliation with gateway reports
