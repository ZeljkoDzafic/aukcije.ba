<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasRoles;
    use HasUuids;
    use Notifiable;

    /**
     * The primary key type.
     */
    protected $primaryKey = 'id';

    /**
     * The primary key is a string (UUID).
     */
    protected $keyType = 'string';

    /**
     * UUIDs are not auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'email_verified_at',
        'phone_verified_at',
        'kyc_level',
        'trust_score',
        'is_banned',
        'banned_at',
        'ban_reason',
        'notification_preferences',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_banned' => 'boolean',
        'banned_at' => 'datetime',
        'trust_score' => 'decimal:2',
        'notification_preferences' => 'array',
    ];

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    /**
     * Get the user's profile.
     */
    /**
     * @return HasOne<UserProfile, $this>
     */
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Get all KYC verifications for the user.
     */
    /**
     * @return HasMany<UserVerification, $this>
     */
    public function verifications(): HasMany
    {
        return $this->hasMany(UserVerification::class);
    }

    /**
     * Get the user's wallet.
     */
    /**
     * @return HasOne<Wallet, $this>
     */
    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    /**
     * Get the user's auctions (as seller).
     */
    /**
     * @return HasMany<Auction, $this>
     */
    public function auctions(): HasMany
    {
        return $this->hasMany(Auction::class, 'seller_id');
    }

    /**
     * Get the user's bids.
     */
    /**
     * @return HasMany<Bid, $this>
     */
    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    /**
     * Get the user's proxy bids.
     */
    /**
     * @return HasMany<ProxyBid, $this>
     */
    public function proxyBids(): HasMany
    {
        return $this->hasMany(ProxyBid::class);
    }

    /**
     * Get ratings given by this user (as rater).
     */
    /**
     * @return HasMany<UserRating, $this>
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(UserRating::class, 'rater_id');
    }

    /**
     * Get ratings received by this user.
     */
    /**
     * @return HasMany<UserRating, $this>
     */
    public function ratingsReceived(): HasMany
    {
        return $this->hasMany(UserRating::class, 'rated_user_id');
    }

    /**
     * Get the user's orders as buyer.
     */
    /**
     * @return HasMany<Order, $this>
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    /**
     * Get the user's orders as seller.
     */
    /**
     * @return HasMany<Order, $this>
     */
    public function soldOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'seller_id');
    }

    /**
     * Get the user's watchlist (auctions being watched).
     */
    /**
     * @return BelongsToMany<Auction, $this>
     */
    public function watchlist(): BelongsToMany
    {
        return $this->belongsToMany(Auction::class, 'auction_watchers')
            ->withTimestamps();
    }

    /**
     * Get messages received by the user.
     */
    /**
     * @return HasMany<Message, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Get messages sent by the user.
     */
    /**
     * @return HasMany<Message, $this>
     */
    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get the user's active seller subscription.
     */
    /**
     * @return HasOne<SellerSubscription, $this>
     */
    public function subscription(): HasOne
    {
        return $this->hasOne(SellerSubscription::class)->latestOfMany('starts_at');
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    /**
     * Get the KYC level based on approved verifications.
     * 0 = none, 1 = phone_sms approved, 2 = +id_document, 3 = +address_proof
     */
    public function getKycLevelAttribute(): int
    {
        return $this->kycLevel();
    }

    /**
     * Get the user's trust score (cached/computed).
     */
    public function getTrustScoreAttribute(): float
    {
        // Return stored trust_score or compute from ratings
        $stored = $this->attributes['trust_score'] ?? null;

        if ($stored !== null) {
            return (float) $stored;
        }

        $avg = $this->ratingsReceived()->avg('rating');

        return $avg ? round((float) $avg, 2) : 0.0;
    }

    /**
     * Get the seller's commission rate based on their subscription tier.
     */
    public function getCommissionRateAttribute(): float
    {
        return $this->getCommissionRate();
    }

    /**
     * Get the user's active subscription tier name.
     */
    public function getActiveTierAttribute(): string
    {
        return $this->getTier()['name'] ?? 'free';
    }

    // -------------------------------------------------------------------------
    // Methods
    // -------------------------------------------------------------------------

    /**
     * Get KYC verification level (0–3).
     * 0 = no verified verifications
     * 1 = phone_sms approved
     * 2 = phone_sms + id_document approved
     * 3 = phone_sms + id_document + address_proof approved
     */
    public function kycLevel(): int
    {
        if ($this->is_banned) {
            return 0;
        }

        $approvedTypes = $this->verifications()
            ->where('status', 'approved')
            ->pluck('type')
            ->toArray();

        $level = 0;

        if (in_array('phone_sms', $approvedTypes)) {
            $level = 1;
        }

        if ($level >= 1 && in_array('id_document', $approvedTypes)) {
            $level = 2;
        }

        if ($level >= 2 && in_array('address_proof', $approvedTypes)) {
            $level = 3;
        }

        return $level;
    }

    /**
     * Determine whether this user can create a new auction.
     * Requires seller role and must be within the tier's active auction limit.
     */
    public function canCreateAuction(): bool
    {
        if ($this->is_banned) {
            return false;
        }

        if (! $this->hasRole(['seller', 'verified_seller'])) {
            return false;
        }

        if (! $this->hasVerifiedEmail()) {
            return false;
        }

        $tier = $this->getTier();
        $auctionLimit = $tier['auction_limit'] ?? 0;

        // -1 means unlimited
        if ($auctionLimit < 0) {
            return true;
        }

        $activeAuctions = $this->auctions()->active()->count();

        return $activeAuctions < $auctionLimit;
    }

    /**
     * Get the user's seller tier configuration array.
     */
    /**
     * @return array<string, mixed>
     */
    public function getTier(): array
    {
        if ($this->subscription?->tier && config('tiers.tiers.'.$this->subscription->tier)) {
            return config('tiers.tiers.'.$this->subscription->tier);
        }

        if ($this->hasRole('verified_seller')) {
            return config('tiers.tiers.storefront', ['name' => 'storefront', 'auction_limit' => -1, 'commission_rate' => 3]);
        }

        if ($this->hasRole('seller')) {
            return config('tiers.tiers.free', ['name' => 'free', 'auction_limit' => 5, 'commission_rate' => 10]);
        }

        return config('tiers.tiers.free', ['name' => 'free', 'auction_limit' => 3, 'commission_rate' => 10]);
    }

    /**
     * Get the seller's commission rate as a decimal fraction.
     */
    public function getCommissionRate(): float
    {
        return ($this->getTier()['commission_rate'] ?? 10) / 100;
    }

    /**
     * Check if user has verified email.
     */
    public function hasVerifiedEmail(): bool
    {
        return $this->email_verified_at !== null;
    }

    /**
     * Check if user has verified phone.
     */
    public function hasVerifiedPhone(): bool
    {
        return $this->phone_verified_at !== null;
    }

    /**
     * Check if user prefers email notifications for a specific type.
     */
    public function prefersEmailNotification(string $type): bool
    {
        $defaults = config('mail.preferences.defaults', []);
        $preferences = $this->notification_preferences ?? $defaults;

        return $preferences["email_{$type}"] ?? true;
    }

    /**
     * Check if user prefers SMS notifications.
     */
    public function prefersSmsNotification(): bool
    {
        return $this->phone_verified_at !== null
            && config('mail.notifications.sms.enabled', false);
    }

    /**
     * Check if user prefers push notifications.
     */
    public function prefersPushNotification(): bool
    {
        return config('mail.notifications.push.enabled', false);
    }

    /**
     * Get the user's trust badge label.
     */
    public function getTrustBadge(): ?string
    {
        if ((float) ($this->attributes['trust_score'] ?? 0) >= 4.5) {
            return 'Top Rated';
        }

        if ($this->hasRole('verified_seller')) {
            return 'Verified Seller';
        }

        if ($this->hasVerifiedEmail()) {
            return 'Verified';
        }

        return null;
    }

    /**
     * Get the user's avatar URL via their profile.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        return $this->profile?->avatar_url;
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Scope: only active (non-banned) users.
     */
    /**
     * @param \Illuminate\Database\Eloquent\Builder<$this> $query
     * @return \Illuminate\Database\Eloquent\Builder<$this>
     */
    public function scopeActive($query)
    {
        return $query->where('is_banned', false);
    }

    /**
     * Scope: only verified sellers.
     */
    /**
     * @param \Illuminate\Database\Eloquent\Builder<$this> $query
     * @return \Illuminate\Database\Eloquent\Builder<$this>
     */
    public function scopeVerifiedSellers($query)
    {
        return $query->role('verified_seller');
    }

    /**
     * Scope: only users with verified email.
     */
    /**
     * @param \Illuminate\Database\Eloquent\Builder<$this> $query
     * @return \Illuminate\Database\Eloquent\Builder<$this>
     */
    public function scopeVerifiedEmail($query)
    {
        return $query->whereNotNull('email_verified_at');
    }
}
