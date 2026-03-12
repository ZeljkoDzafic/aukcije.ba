<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $dispute_id
 * @property string $user_id
 * @property string $message
 * @property bool $is_from_admin
 * @property User|null $user
 */
class DisputeMessage extends Model
{
    /** @use HasFactory<Factory<self>> */
    use HasFactory;

    protected $fillable = [
        'dispute_id',
        'user_id',
        'message',
        'is_from_admin',
    ];

    /**
     * @return BelongsTo<Dispute, $this>
     */
    public function dispute(): BelongsTo
    {
        return $this->belongsTo(Dispute::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
