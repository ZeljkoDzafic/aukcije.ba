<?php

namespace App\Services\Couriers;

interface CourierInterface
{
    /**
     * Create waybill (tovarni list)
     *
     * @param  array  $shipmentData
     * @return array [success, waybill_number, waybill_url, tracking_number]
     */
    public function createWaybill(array $shipmentData): array;

    /**
     * Get tracking information
     *
     * @param  string  $trackingNumber
     * @return array [status, events, estimated_delivery]
     */
    public function getTrackingInfo(string $trackingNumber): array;

    /**
     * Estimate shipping cost
     *
     * @param  string  $fromCity
     * @param  string  $toCity
     * @param  float  $weightKg
     * @return array [price, currency, delivery_days]
     */
    public function estimateShipping(string $fromCity, string $toCity, float $weightKg): array;

    /**
     * Cancel shipment
     *
     * @param  string  $waybillNumber
     * @return array [success, error?]
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
