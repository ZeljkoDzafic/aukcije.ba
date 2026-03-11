# 03 - Database Schema (PostgreSQL 16+)

## ER Diagram (high-level)

```
                         ┌──────────────┐
                         │   categories │
                         └──────┬───────┘
                                │
users ──< auctions >── categories
  │          │
  │          ├── bids
  │          │     └── proxy_bids
  │          ├── auction_images
  │          ├── auction_watchers
  │          └── auction_extensions (anti-sniping log)
  │
  ├── user_profiles
  ├── user_verifications (KYC)
  ├── user_ratings
  │
  ├── wallets ──< wallet_transactions
  │
  ├── orders ──< order_items
  │     │
  │     ├── payments
  │     ├── shipments ──< shipment_tracking
  │     └── disputes
  │
  ├── messages
  ├── notifications
  │
  └── seller_tiers

feature_flags    admin_logs    bid_increments
```

## TABLES

---

### SECTION 1: Users & Authentication

#### 1. users

```sql
CREATE TABLE users (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  email VARCHAR(255) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  email_verified_at TIMESTAMPTZ,
  remember_token VARCHAR(100),
  created_at TIMESTAMPTZ DEFAULT now(),
  updated_at TIMESTAMPTZ DEFAULT now()
);
```

#### 2. user_profiles

```sql
CREATE TABLE user_profiles (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  user_id UUID REFERENCES users(id) ON DELETE CASCADE UNIQUE,
  full_name VARCHAR(255) NOT NULL,
  phone VARCHAR(50),
  avatar_url TEXT,
  city VARCHAR(100),
  country VARCHAR(50) DEFAULT 'BA',
  bio TEXT,
  date_of_birth DATE,
  preferred_language VARCHAR(5) DEFAULT 'bs',
  notification_preferences JSONB DEFAULT '{"email": true, "push": true, "sms": false}',
  created_at TIMESTAMPTZ DEFAULT now(),
  updated_at TIMESTAMPTZ DEFAULT now()
);

CREATE INDEX idx_user_profiles_user ON user_profiles(user_id);
```

#### 3. user_verifications (KYC)

```sql
CREATE TABLE user_verifications (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  user_id UUID REFERENCES users(id) ON DELETE CASCADE,
  type VARCHAR(50) NOT NULL CHECK (type IN ('phone_sms', 'id_document', 'address_proof')),
  status VARCHAR(20) DEFAULT 'pending' CHECK (status IN ('pending', 'approved', 'rejected')),
  document_url TEXT,
  verified_at TIMESTAMPTZ,
  reviewer_id UUID REFERENCES users(id),
  notes TEXT,
  created_at TIMESTAMPTZ DEFAULT now()
);
```

---

### SECTION 2: Roles & Permissions (Spatie)

Koristimo `spatie/laravel-permission` — automatski kreira:
- `roles` tabela
- `permissions` tabela
- `model_has_roles` pivot
- `model_has_permissions` pivot
- `role_has_permissions` pivot

Predefinisane role:

| Rola | Opis |
|------|------|
| `super_admin` | Konfiguracija sistema, dispute resolution |
| `moderator` | Verifikacija sadržaja, upravljanje kategorijama |
| `verified_seller` | Aukcije bez limita, API pristup za inventar |
| `seller` | Osnovne aukcije sa limitima |
| `buyer` | Licitiranje, watchliste |

---

### SECTION 3: Categories

#### 4. categories

```sql
CREATE TABLE categories (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  parent_id UUID REFERENCES categories(id) ON DELETE SET NULL,
  name VARCHAR(255) NOT NULL,
  slug VARCHAR(255) UNIQUE NOT NULL,
  icon VARCHAR(50),
  sort_order INTEGER DEFAULT 0,
  is_active BOOLEAN DEFAULT true,
  created_at TIMESTAMPTZ DEFAULT now()
);

CREATE INDEX idx_categories_parent ON categories(parent_id);
CREATE INDEX idx_categories_slug ON categories(slug);
```

---

### SECTION 4: Auctions (Core)

#### 5. auctions

```sql
CREATE TABLE auctions (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  seller_id UUID REFERENCES users(id) ON DELETE CASCADE,
  category_id UUID REFERENCES categories(id),
  title VARCHAR(500) NOT NULL,
  description TEXT,
  condition VARCHAR(20) CHECK (condition IN ('new', 'like_new', 'used', 'for_parts')),

  -- Pricing
  start_price DECIMAL(12,2) NOT NULL,
  current_price DECIMAL(12,2) NOT NULL,
  reserve_price DECIMAL(12,2),                -- minimalna cijena za prodaju
  buy_now_price DECIMAL(12,2),                -- "Kupi odmah" cijena
  currency VARCHAR(3) DEFAULT 'BAM',

  -- Auction type & status
  type VARCHAR(20) DEFAULT 'standard' CHECK (type IN ('standard', 'buy_now', 'dutch')),
  status VARCHAR(20) DEFAULT 'draft' CHECK (status IN ('draft', 'active', 'finished', 'cancelled', 'sold')),

  -- Timing
  starts_at TIMESTAMPTZ NOT NULL,
  ends_at TIMESTAMPTZ NOT NULL,
  original_end_at TIMESTAMPTZ,                -- čuva originalni end time prije anti-sniping produženja
  auto_extension BOOLEAN DEFAULT true,         -- anti-sniping enabled
  extension_minutes INTEGER DEFAULT 3,

  -- Metadata
  location_city VARCHAR(100),
  location_country VARCHAR(50) DEFAULT 'BA',
  shipping_available BOOLEAN DEFAULT true,
  shipping_cost DECIMAL(8,2),
  views_count INTEGER DEFAULT 0,
  bids_count INTEGER DEFAULT 0,
  is_featured BOOLEAN DEFAULT false,           -- promoted na naslovnoj

  -- Search
  search_vector TSVECTOR,

  created_at TIMESTAMPTZ DEFAULT now(),
  updated_at TIMESTAMPTZ DEFAULT now()
);

CREATE INDEX idx_auctions_seller ON auctions(seller_id);
CREATE INDEX idx_auctions_category ON auctions(category_id);
CREATE INDEX idx_auctions_status ON auctions(status);
CREATE INDEX idx_auctions_ends_at ON auctions(ends_at) WHERE status = 'active';
CREATE INDEX idx_auctions_featured ON auctions(is_featured) WHERE is_featured = true;
CREATE INDEX idx_auctions_search ON auctions USING GIN(search_vector);
```

#### 6. auction_images

```sql
CREATE TABLE auction_images (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  auction_id UUID REFERENCES auctions(id) ON DELETE CASCADE,
  url TEXT NOT NULL,
  sort_order INTEGER DEFAULT 0,
  is_primary BOOLEAN DEFAULT false,
  created_at TIMESTAMPTZ DEFAULT now()
);

CREATE INDEX idx_auction_images_auction ON auction_images(auction_id);
```

---

### SECTION 5: Bids

#### 7. bids

```sql
CREATE TABLE bids (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  auction_id UUID REFERENCES auctions(id) ON DELETE CASCADE,
  user_id UUID REFERENCES users(id) ON DELETE CASCADE,
  amount DECIMAL(12,2) NOT NULL,
  is_proxy BOOLEAN DEFAULT false,
  is_auto BOOLEAN DEFAULT false,              -- auto-generated od proxy bidding sistema
  ip_address INET,
  created_at TIMESTAMPTZ DEFAULT now()
);

CREATE INDEX idx_bids_auction ON bids(auction_id);
CREATE INDEX idx_bids_user ON bids(user_id);
CREATE INDEX idx_bids_auction_amount ON bids(auction_id, amount DESC);
CREATE INDEX idx_bids_created ON bids(created_at);
```

#### 8. proxy_bids

```sql
CREATE TABLE proxy_bids (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  auction_id UUID REFERENCES auctions(id) ON DELETE CASCADE,
  user_id UUID REFERENCES users(id) ON DELETE CASCADE,
  max_amount DECIMAL(12,2) NOT NULL,
  is_active BOOLEAN DEFAULT true,
  created_at TIMESTAMPTZ DEFAULT now(),
  updated_at TIMESTAMPTZ DEFAULT now(),

  UNIQUE(auction_id, user_id)                 -- jedan proxy bid po korisniku po aukciji
);
```

#### 9. bid_increments

```sql
CREATE TABLE bid_increments (
  id SERIAL PRIMARY KEY,
  price_from DECIMAL(12,2) NOT NULL,
  price_to DECIMAL(12,2),                     -- NULL = bez gornje granice
  increment DECIMAL(12,2) NOT NULL
);

-- Seed data
INSERT INTO bid_increments (price_from, price_to, increment) VALUES
  (0, 10, 0.50),
  (10, 50, 1.00),
  (50, 100, 2.00),
  (100, 500, 5.00),
  (500, 1000, 10.00),
  (1000, 5000, 25.00),
  (5000, NULL, 50.00);
```

#### 10. auction_extensions (anti-sniping log)

```sql
CREATE TABLE auction_extensions (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  auction_id UUID REFERENCES auctions(id) ON DELETE CASCADE,
  triggered_by_bid_id UUID REFERENCES bids(id),
  old_end_at TIMESTAMPTZ NOT NULL,
  new_end_at TIMESTAMPTZ NOT NULL,
  created_at TIMESTAMPTZ DEFAULT now()
);
```

---

### SECTION 6: Watchlists

#### 11. auction_watchers

```sql
CREATE TABLE auction_watchers (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  auction_id UUID REFERENCES auctions(id) ON DELETE CASCADE,
  user_id UUID REFERENCES users(id) ON DELETE CASCADE,
  created_at TIMESTAMPTZ DEFAULT now(),

  UNIQUE(auction_id, user_id)
);
```

---

### SECTION 7: Wallet & Payments

#### 12. wallets

```sql
CREATE TABLE wallets (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  user_id UUID REFERENCES users(id) ON DELETE CASCADE UNIQUE,
  balance DECIMAL(12,2) DEFAULT 0.00,
  currency VARCHAR(3) DEFAULT 'BAM',
  is_frozen BOOLEAN DEFAULT false,
  created_at TIMESTAMPTZ DEFAULT now(),
  updated_at TIMESTAMPTZ DEFAULT now()
);
```

#### 13. wallet_transactions

```sql
CREATE TABLE wallet_transactions (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  wallet_id UUID REFERENCES wallets(id) ON DELETE CASCADE,
  type VARCHAR(30) NOT NULL CHECK (type IN ('deposit', 'withdrawal', 'escrow_hold', 'escrow_release', 'commission', 'refund')),
  amount DECIMAL(12,2) NOT NULL,
  balance_after DECIMAL(12,2) NOT NULL,
  reference_type VARCHAR(50),                  -- 'order', 'dispute', etc.
  reference_id UUID,
  description TEXT,
  created_at TIMESTAMPTZ DEFAULT now()
);

CREATE INDEX idx_wallet_txn_wallet ON wallet_transactions(wallet_id);
```

#### 14. payments

```sql
CREATE TABLE payments (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  user_id UUID REFERENCES users(id),
  order_id UUID,
  gateway VARCHAR(50) NOT NULL CHECK (gateway IN ('stripe', 'paypal', 'monri', 'corvuspay', 'wallet')),
  gateway_transaction_id VARCHAR(255),
  amount DECIMAL(12,2) NOT NULL,
  currency VARCHAR(3) DEFAULT 'BAM',
  status VARCHAR(20) DEFAULT 'pending' CHECK (status IN ('pending', 'completed', 'failed', 'refunded')),
  created_at TIMESTAMPTZ DEFAULT now(),
  updated_at TIMESTAMPTZ DEFAULT now()
);
```

---

### SECTION 8: Orders & Shipping

#### 15. orders

```sql
CREATE TABLE orders (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  auction_id UUID REFERENCES auctions(id),
  buyer_id UUID REFERENCES users(id),
  seller_id UUID REFERENCES users(id),
  amount DECIMAL(12,2) NOT NULL,
  commission DECIMAL(12,2) NOT NULL,
  status VARCHAR(30) DEFAULT 'pending' CHECK (status IN (
    'pending', 'payment_received', 'shipped', 'delivered', 'completed', 'disputed', 'cancelled'
  )),
  shipping_address JSONB,
  created_at TIMESTAMPTZ DEFAULT now(),
  updated_at TIMESTAMPTZ DEFAULT now()
);
```

#### 16. shipments

```sql
CREATE TABLE shipments (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  order_id UUID REFERENCES orders(id) ON DELETE CASCADE,
  courier VARCHAR(50) CHECK (courier IN ('euroexpress', 'postexpress', 'overseas', 'bh_posta', 'other')),
  tracking_number VARCHAR(100),
  waybill_url TEXT,
  status VARCHAR(30) DEFAULT 'pending',
  shipped_at TIMESTAMPTZ,
  delivered_at TIMESTAMPTZ,
  created_at TIMESTAMPTZ DEFAULT now()
);
```

---

### SECTION 9: Ratings & Disputes

#### 17. user_ratings

```sql
CREATE TABLE user_ratings (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  order_id UUID REFERENCES orders(id) ON DELETE CASCADE,
  rater_id UUID REFERENCES users(id),
  rated_user_id UUID REFERENCES users(id),
  score INTEGER NOT NULL CHECK (score BETWEEN 1 AND 5),
  comment TEXT,
  type VARCHAR(10) CHECK (type IN ('buyer', 'seller')),
  created_at TIMESTAMPTZ DEFAULT now(),

  UNIQUE(order_id, rater_id)
);
```

#### 18. disputes

```sql
CREATE TABLE disputes (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  order_id UUID REFERENCES orders(id) ON DELETE CASCADE,
  opened_by UUID REFERENCES users(id),
  reason VARCHAR(50) NOT NULL,
  description TEXT,
  status VARCHAR(20) DEFAULT 'open' CHECK (status IN ('open', 'in_review', 'resolved', 'closed')),
  resolution TEXT,
  resolved_by UUID REFERENCES users(id),
  created_at TIMESTAMPTZ DEFAULT now(),
  resolved_at TIMESTAMPTZ
);
```

---

### SECTION 10: Messaging & Notifications

#### 19. messages

```sql
CREATE TABLE messages (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  sender_id UUID REFERENCES users(id),
  receiver_id UUID REFERENCES users(id),
  auction_id UUID REFERENCES auctions(id),
  content TEXT NOT NULL,
  is_read BOOLEAN DEFAULT false,
  created_at TIMESTAMPTZ DEFAULT now()
);

CREATE INDEX idx_messages_receiver ON messages(receiver_id, is_read);
```

#### 20. notifications

```sql
CREATE TABLE notifications (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  user_id UUID REFERENCES users(id) ON DELETE CASCADE,
  type VARCHAR(50) NOT NULL,
  title VARCHAR(255),
  body TEXT,
  data JSONB,
  read_at TIMESTAMPTZ,
  created_at TIMESTAMPTZ DEFAULT now()
);

CREATE INDEX idx_notifications_user ON notifications(user_id, read_at);
```

---

### SECTION 11: Feature Flags & Admin

#### 21. feature_flags

```sql
CREATE TABLE feature_flags (
  id SERIAL PRIMARY KEY,
  name VARCHAR(100) UNIQUE NOT NULL,
  is_active BOOLEAN DEFAULT false,
  description TEXT,
  created_at TIMESTAMPTZ DEFAULT now()
);
```

#### 22. admin_logs

```sql
CREATE TABLE admin_logs (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  admin_id UUID REFERENCES users(id),
  action VARCHAR(100) NOT NULL,
  target_type VARCHAR(50),
  target_id UUID,
  metadata JSONB,
  created_at TIMESTAMPTZ DEFAULT now()
);
```
