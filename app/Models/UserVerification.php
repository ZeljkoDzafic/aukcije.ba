<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserVerification extends Model
{
    /** @use HasFactory<Factory<self>> */
    use HasFactory;
    use HasUuids;

    /**
     * The primary key.
     */
    protected $primaryKey = 'id';

    /**
     * UUID primary key type.
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
        'user_id',
        'type',
        'status',
        'document_url',
        'document_back_url',
        'notes',
        'rejection_reason',
        'reviewer_id',
        'verified_at',
        'expires_at',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'verified_at' => 'datetime',
        'expires_at' => 'datetime',
        'metadata' => 'array',
    ];

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    /**
     * Get the user this verification belongs to.
     */
    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who reviewed this verification.
     */
    /**
     * @return BelongsTo<User, $this>
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Check if this verification is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if this verification is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if this verification is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
