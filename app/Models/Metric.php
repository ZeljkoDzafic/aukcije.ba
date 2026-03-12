<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Metric Model
 * 
 * Stores performance metrics for SLO monitoring
 */
class Metric extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'value',
        'unit',
        'tags',
    ];

    protected $casts = [
        'value' => 'decimal:4',
        'tags' => 'array',
    ];

    /**
     * Scope for SLO metrics
     */
    public function scopeSlo($query)
    {
        return $query->where('tags->metric_type', 'slo');
    }

    /**
     * Scope for specific endpoint
     */
    public function scopeForEndpoint($query, string $endpoint)
    {
        return $query->where('tags->endpoint', $endpoint);
    }

    /**
     * Get metrics for time range
     */
    public function scopeForTimeRange($query, string $from, string $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    /**
     * Calculate average for metric
     */
    public function scopeAverage($query, string $metricName)
    {
        return $query->where('name', $metricName)->avg('value');
    }

    /**
     * Calculate p95 for metric
     */
    public function scopeP95($query, string $metricName)
    {
        $values = $query->where('name', $metricName)
            ->orderBy('value')
            ->pluck('value');
        
        if ($values->isEmpty()) {
            return 0;
        }
        
        $p95Index = (int) ceil($values->count() * 0.95) - 1;
        return $values[max(0, $p95Index)];
    }

    /**
     * Calculate p99 for metric
     */
    public function scopeP99($query, string $metricName)
    {
        $values = $query->where('name', $metricName)
            ->orderBy('value')
            ->pluck('value');
        
        if ($values->isEmpty()) {
            return 0;
        }
        
        $p99Index = (int) ceil($values->count() * 0.99) - 1;
        return $values[max(0, $p99Index)];
    }
}
