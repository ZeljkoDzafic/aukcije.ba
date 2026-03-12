<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Shipment;
use App\Services\Couriers\BhPostaCourier;
use App\Services\Couriers\CourierInterface;
use App\Services\Couriers\EuroExpressCourier;
use App\Services\Couriers\PostExpressCourier;
use Exception;

class ShippingService
{
    protected array $couriers = [];

    public function __construct()
    {
        $this->initializeCouriers();
    }

    /**
     * Initialize all configured couriers
     */
    protected function initializeCouriers(): void
    {
        $this->couriers = [
            'euroexpress' => new EuroExpressCourier(),
            'postexpress' => new PostExpressCourier(),
            'bhposta' => new BhPostaCourier(),
        ];
    }

    /**
     * Get a specific courier
     */
    public function getCourier(string $name): ?CourierInterface
    {
        return $this->couriers[$name] ?? null;
    }

    /**
     * Get all available couriers
     */
    public function getAvailableCouriers(): array
    {
        $available = [];
        
        foreach ($this->couriers as $name => $courier) {
            if ($courier->isAvailable()) {
                $available[$name] = [
                    'name' => $courier->getName(),
                    'services' => method_exists($courier, 'getServices') ? $courier->getServices() : [],
                ];
            }
        }

        return $available;
    }

    /**
     * Get best courier for shipment (cheapest option)
     */
    public function getBestCourier(string $fromCity, string $toCity, float $weightKg): ?array
    {
        $quotes = [];

        foreach ($this->couriers as $name => $courier) {
            if ($courier->isAvailable()) {
                $quote = $courier->estimateShipping($fromCity, $toCity, $weightKg);
                if ($quote['success']) {
                    $quotes[$name] = $quote;
                }
            }
        }

        if (empty($quotes)) {
            return null;
        }

        // Find cheapest option
        $cheapest = null;
        $cheapestPrice = PHP_FLOAT_MAX;

        foreach ($quotes as $name => $quote) {
            if ($quote['price'] < $cheapestPrice) {
                $cheapestPrice = $quote['price'];
                $cheapest = array_merge(['courier' => $name], $quote);
            }
        }

        return $cheapest;
    }

    /**
     * Get shipping quotes from all available couriers
     */
    public function getShippingQuotes(string $fromCity, string $toCity, float $weightKg): array
    {
        $quotes = [];

        foreach ($this->couriers as $name => $courier) {
            if ($courier->isAvailable()) {
                $quote = $courier->estimateShipping($fromCity, $toCity, $weightKg);
                $quote['courier'] = $name;
                $quote['courier_name'] = $courier->getName();
                $quotes[] = $quote;
            }
        }

        // Sort by price
        usort($quotes, function ($a, $b) {
            return $a['price'] <=> $b['price'];
        });

        return $quotes;
    }

    /**
     * Create waybill for order
     */
    public function createWaybill(Order $order, string $courierName, array $shipmentData): array
    {
        $courier = $this->getCourier($courierName);

        if (!$courier) {
            return [
                'success' => false,
                'error' => 'Courier not found',
            ];
        }

        if (!$courier->isAvailable()) {
            return [
                'success' => false,
                'error' => 'Courier is not available',
            ];
        }

        // Prepare shipment data
        $waybillData = array_merge([
            'sender_name' => $order->seller->name,
            'sender_phone' => $order->seller->phone ?? $order->seller->profile?->phone,
            'recipient_name' => $order->buyer->name,
            'recipient_address' => $order->shipping_address,
            'recipient_city' => $order->shipping_city,
            'recipient_postal_code' => $order->shipping_postal_code,
            'recipient_country' => $order->shipping_country ?? 'BA',
            'recipient_phone' => $order->buyer->phone ?? $order->buyer->profile?->phone,
            'recipient_email' => $order->buyer->email,
            'weight_kg' => $shipmentData['weight_kg'] ?? 1,
            'description' => $order->auction->title,
            'declared_value' => $order->total_amount,
            'cod_amount' => 0, // Already paid through platform
            'service_type' => $shipmentData['service_type'] ?? 'standard',
        ], $shipmentData);

        // Create waybill via courier
        $result = $courier->createWaybill($waybillData);

        if ($result['success']) {
            // Create shipment record
            $shipment = Shipment::create([
                'order_id' => $order->id,
                'courier' => $courierName,
                'tracking_number' => $result['tracking_number'],
                'waybill_number' => $result['waybill_number'],
                'waybill_url' => $result['waybill_url'] ?? null,
                'tracking_url' => $result['tracking_url'] ?? null,
                'status' => 'label_created',
                'estimated_delivery' => $result['estimated_delivery'] ?? null,
                'metadata' => $result,
            ]);

            $result['shipment_id'] = $shipment->id;
        }

        return $result;
    }

    /**
     * Get tracking information for shipment
     */
    public function trackShipment(Shipment $shipment): array
    {
        $courier = $this->getCourier($shipment->courier);

        if (!$courier) {
            return [
                'success' => false,
                'error' => 'Courier not found',
            ];
        }

        $trackingInfo = $courier->getTrackingInfo($shipment->tracking_number);

        if ($trackingInfo['success']) {
            // Update shipment status
            $shipment->update([
                'status' => $trackingInfo['status'],
                'status_label' => $trackingInfo['status_label'] ?? null,
                'estimated_delivery' => $trackingInfo['estimated_delivery'] ?? null,
                'tracking_events' => $trackingInfo['events'] ?? [],
                'last_tracking_update' => now(),
            ]);
        }

        return $trackingInfo;
    }

    /**
     * Cancel shipment
     */
    public function cancelShipment(Shipment $shipment): array
    {
        $courier = $this->getCourier($shipment->courier);

        if (!$courier) {
            return [
                'success' => false,
                'error' => 'Courier not found',
            ];
        }

        $result = $courier->cancelShipment($shipment->waybill_number);

        if ($result['success']) {
            $shipment->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);
        }

        return $result;
    }

    /**
     * Update shipment status
     */
    public function updateShipmentStatus(Shipment $shipment, string $status): void
    {
        $shipment->update([
            'status' => $status,
            'status_updated_at' => now(),
        ]);

        // If delivered, notify order
        if ($status === 'delivered') {
            $shipment->order->markAsDelivered();
        }
    }

    /**
     * Handle webhook from courier
     */
    public function handleWebhook(string $courierName, array $data): void
    {
        $shipment = Shipment::where('courier', $courierName)
            ->where('tracking_number', $data['tracking_number'] ?? null)
            ->first();

        if (!$shipment) {
            throw new Exception('Shipment not found');
        }

        // Update tracking info
        $this->trackShipment($shipment);
    }
}
