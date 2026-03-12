<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Alert Model
 * 
 * Stores system alerts for monitoring
 */
class Alert extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'severity',
        'title',
        'message',
        'metadata',
        'status',
        'acknowledged_by',
        'acknowledged_at',
        'resolved_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'acknowledged_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    /**
     * Scope for active alerts
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for high severity alerts
     */
    public function scopeHighSeverity($query)
    {
        return $query->where('severity', 'high');
    }

    /**
     * Scope for SLO breach alerts
     */
    public function scopeSloBreach($query)
    {
        return $query->where('type', 'slo_breach');
    }

    /**
     * Acknowledge alert
     */
    public function acknowledge($user): void
    {
        $this->update([
            'status' => 'acknowledged',
            'acknowledged_by' => $user->id,
            'acknowledged_at' => now(),
        ]);
    }

    /**
     * Resolve alert
     */
    public function resolve(): void
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);
    }

    /**
     * Check if alert is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get alert icon by severity
     */
    public function getIconAttribute(): string
    {
        return match($this->severity) {
            'critical' => '🔴',
            'high' => '🟠',
            'medium' => '🟡',
            'low' => '🟢',
            default => '⚪',
        };
    }

    /**
     * Get Slack channel for severity
     */
    public function getSlackChannelAttribute(): string
    {
        return match($this->severity) {
            'critical' => '#alerts-critical',
            'high' => '#alerts-high',
            'medium' => '#alerts-medium',
            default => '#alerts-low',
        };
    }
}
