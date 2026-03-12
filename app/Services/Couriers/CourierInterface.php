<?php

declare(strict_types=1);

namespace App\Services\Couriers;

interface CourierInterface
{
    /**
     * Create waybill (tovarni list)
     *
     * @param array<string, mixed> $shipmentData
     *
     * @return array<string, mixed>
     */
    public function createWaybill(array $shipmentData): array;

    /**
     * Get tracking information
     *
     * @param string $trackingNumber
     *
     * @return array<string, mixed>
     */
    public function getTrackingInfo(string $trackingNumber): array;

    /**
     * Estimate shipping cost
     *
     * @param string $fromCity
     * @param string $toCity
     * @param float  $weightKg
     *
     * @return array<string, mixed>
     */
    public function estimateShipping(string $fromCity, string $toCity, float $weightKg): array;

    /**
     * Cancel shipment
     *
     * @param string $waybillNumber
     *
     * @return array<string, mixed>
     */
    public function cancelShipment(string $waybillNumber): array;

    /**
     * Get courier name
     */
    public function getName(): string;

    /**
     * Check if courier is available
     */
    public function isAvailable(): bool;
}
