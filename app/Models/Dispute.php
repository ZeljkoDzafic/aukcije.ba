<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dispute extends Model
{
    /** @use HasFactory<Factory<self>> */
    use HasFactory;
    use HasUuids;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'order_id',
        'opened_by_id',
        'opened_by',
        'reason',
        'description',
        'status',
        'resolution',
        'resolved_by_id',
        'resolved_by',
        'seller_response',
        'evidence',
        'created_at',
        'updated_at',
        'escalated_at',
        'resolved_at',
    ];

    protected $casts = [
        'evidence' => 'array',
        'escalated_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<Order, $this>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function openedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by_id');
    }

    /**
     * @return HasMany<DisputeMessage, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(DisputeMessage::class);
    }
}
