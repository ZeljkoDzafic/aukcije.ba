<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'order_id',
        'courier',
        'tracking_number',
        'waybill_number',
        'waybill_url',
        'tracking_url',
        'status',
        'status_label',
        'estimated_delivery',
        'delivered_at',
        'tracking_events',
        'metadata',
        'last_tracking_update',
        'status_updated_at',
        'cancelled_at',
    ];

    protected $casts = [
        'tracking_events' => 'array',
        'metadata' => 'array',
        'estimated_delivery' => 'date',
        'delivered_at' => 'datetime',
        'last_tracking_update' => 'datetime',
        'status_updated_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get courier name
     */
    public function getCourierNameAttribute(): string
    {
        return match($this->courier) {
            'euroexpress' => 'EuroExpress',
            'postexpress' => 'PostExpress',
            'bhposta' => 'BH Pošta',
            default => $this->courier,
        };
    }

    /**
     * Get status label
     */
    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'label_created' => ['label' => 'Tovarni list kreiran', 'color' => 'info'],
            'picked_up' => ['label' => 'Preuzeto', 'color' => 'info'],
            'in_transit' => ['label' => 'U tranzitu', 'color' => 'warning'],
            'out_for_delivery' => ['label' => 'U dostavi', 'color' => 'warning'],
            'delivered' => ['label' => 'Dostavljeno', 'color' => 'success'],
            'delivery_failed' => ['label' => 'Dostava neuspješna', 'color' => 'danger'],
            'returned' => ['label' => 'Vraćeno', 'color' => 'danger'],
            'cancelled' => ['label' => 'Otkazano', 'color' => 'danger'],
            default => ['label' => $this->status_label ?? 'Nepoznato', 'color' => 'gray'],
        };
    }

    /**
     * Check if delivered
     */
    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }

    /**
     * Check if in transit
     */
    public function isInTransit(): bool
    {
        return in_array($this->status, ['in_transit', 'out_for_delivery']);
    }

    /**
     * Check if cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Get latest tracking event
     */
    public function getLatestEventAttribute(): ?array
    {
        $events = $this->tracking_events ?? [];
        return !empty($events) ? reset($events) : null;
    }

    /**
     * Get tracking timeline for display
     */
    public function getTrackingTimelineAttribute(): array
    {
        $events = $this->tracking_events ?? [];
        
        $timeline = [];
        $statusOrder = [
            'label_created' => 1,
            'picked_up' => 2,
            'in_transit' => 3,
            'out_for_delivery' => 4,
            'delivered' => 5,
            'delivery_failed' => 6,
            'returned' => 7,
        ];

        foreach ($events as $event) {
            $timeline[] = [
                'status' => $event['status'],
                'label' => $event['description'],
                'location' => $event['location'] ?? null,
                'timestamp' => $event['timestamp'],
                'completed' => ($statusOrder[$event['status']] ?? 0) <= ($statusOrder[$this->status] ?? 0),
            ];
        }

        return array_reverse($timeline);
    }
}
