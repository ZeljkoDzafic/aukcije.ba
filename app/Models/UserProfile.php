<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasFactory, HasUuids;

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
        'full_name',
        'avatar_url',
        'bio',
        'location',
        'city',
        'country',
        'address',
        'postal_code',
        'date_of_birth',
        'gender',
        'website_url',
        'social_links',
        'notification_preferences',
        'language',
        'currency',
        'timezone',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'notification_preferences' => 'array',
        'social_links' => 'array',
        'date_of_birth' => 'date',
    ];

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    /**
     * Get the user that owns this profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
