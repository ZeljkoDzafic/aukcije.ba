<?php

namespace App\Jobs;

use App\Models\Metric;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * SLO Monitoring Job
 * 
 * Measures p99 latency for critical endpoints:
 * - Bidding: p99 < 500ms
 * - Search: p99 < 300ms
 * - Checkout: p99 < 1000ms
 * 
 * Alerts if SLO is breached
 */
class SloMonitoringJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * SLO thresholds in milliseconds
     */
    protected array $sloThresholds = [
        'bidding' => 500,
        'search' => 300,
        'checkout' => 1000,
        'auction_detail' => 400,
        'auction_listing' => 350,
    ];

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $metrics = $this->collectMetrics();
        
        foreach ($metrics as $endpoint => $latency) {
            $this->recordMetric($endpoint, $latency);
            $this->checkSlo($endpoint, $latency);
        }
    }

    /**
     * Collect latency metrics from various endpoints
     */
    protected function collectMetrics(): array
    {
        $baseUrl = config('app.url');
        $metrics = [];

        // Bidding endpoint latency (from Redis/cache)
        $metrics['bidding'] = $this->getP99Latency('bidding');

        // Search endpoint latency
        $metrics['search'] = $this->getP99Latency('search');

        // Checkout endpoint latency
        $metrics['checkout'] = $this->getP99Latency('checkout');

        // Auction detail latency
        $metrics['auction_detail'] = $this->getP99Latency('auction_detail');

        // Auction listing latency
        $metrics['auction_listing'] = $this->getP99Latency('auction_listing');

        return $metrics;
    }

    /**
     * Get p99 latency for an endpoint from cache/Redis
     */
    protected function getP99Latency(string $endpoint): float
    {
        // In production, this would query Prometheus/Redis for actual metrics
        // For now, simulate with cache-stored values
        
        $latencies = cache()->get("slo:latencies:{$endpoint}", []);
        
        if (empty($latencies)) {
            return 0.0;
        }

        // Calculate p99
        sort($latencies);
        $p99Index = (int) ceil(count($latencies) * 0.99) - 1;
        
        return $latencies[max(0, $p99Index)];
    }

    /**
     * Record metric to database and Prometheus
     */
    protected function recordMetric(string $endpoint, float $latency): void
    {
        // Store in database for historical analysis
        Metric::create([
            'name' => "slo:{$endpoint}:p99",
            'value' => $latency,
            'unit' => 'ms',
            'tags' => ['endpoint' => $endpoint, 'metric_type' => 'slo'],
        ]);

        // Log for Prometheus exporter
        Log::channel('prometheus')->info('slo_metric', [
            'name' => "slo:{$endpoint}:p99",
            'value' => $latency,
            'unit' => 'ms',
        ]);
    }

    /**
     * Check if SLO is breached and send alert
     */
    protected function checkSlo(string $endpoint, float $latency): void
    {
        $threshold = $this->sloThresholds[$endpoint] ?? 500;

        if ($latency > $threshold && $latency > 0) {
            $this->sendSloBreachAlert($endpoint, $latency, $threshold);
        }
    }

    /**
     * Send SLO breach alert
     */
    protected function sendSloBreachAlert(string $endpoint, float $latency, float $threshold): void
    {
        $message = "SLO Breach: {$endpoint} p99 latency is {$latency}ms (threshold: {$threshold}ms)";

        Log::channel('slack')->error($message, [
            'endpoint' => $endpoint,
            'latency_ms' => $latency,
            'threshold_ms' => $threshold,
            'breach_percentage' => round(($latency / $threshold - 1) * 100, 2),
        ]);

        // Send to Slack
        if (config('services.slack.webhook_url')) {
            Http::post(config('services.slack.webhook_url'), [
                'text' => "🚨 {$message}",
                'channel' => '#alerts-slo',
                'username' => 'SLO Monitor',
                'icon_emoji' => ':chart_with_downwards_trend:',
            ]);
        }

        // Create alert record
        \App\Models\Alert::create([
            'type' => 'slo_breach',
            'severity' => 'high',
            'title' => "SLO Breach: {$endpoint}",
            'message' => $message,
            'metadata' => [
                'endpoint' => $endpoint,
                'latency_ms' => $latency,
                'threshold_ms' => $threshold,
            ],
        ]);
    }
}
