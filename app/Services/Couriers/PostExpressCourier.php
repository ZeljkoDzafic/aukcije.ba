<?php

namespace App\Services\Couriers;

use Exception;

class PostExpressCourier implements CourierInterface
{
    protected string $apiKey;
    protected string $apiSecret;
    protected string $apiUrl;
    protected array $sender;

    public function __construct()
    {
        $this->apiKey = config('shipping.postexpress.api_key');
        $this->apiSecret = config('shipping.postexpress.api_secret');
        $this->apiUrl = config('shipping.postexpress.sandbox', true)
            ? config('shipping.postexpress.api_url')
            : config('shipping.postexpress.api_url_production');
        $this->sender = config('shipping.postexpress.sender', []);
    }

    public function createWaybill(array $shipmentData): array
    {
        try {
            $waybillData = [
                'sender' => array_merge($this->sender, [
                    'name' => $shipmentData['sender_name'] ?? $this->sender['name'],
                    'phone' => $shipmentData['sender_phone'] ?? $this->sender['phone'],
                    'oib' => $shipmentData['sender_oib'] ?? $this->sender['oib'] ?? '',
                ]),
                'recipient' => [
                    'name' => $shipmentData['recipient_name'],
                    'address' => $shipmentData['recipient_address'],
                    'city' => $shipmentData['recipient_city'],
                    'postal_code' => $shipmentData['recipient_postal_code'],
                    'country' => $shipmentData['recipient_country'] ?? 'HR',
                    'phone' => $shipmentData['recipient_phone'],
                    'email' => $shipmentData['recipient_email'] ?? null,
                ],
                'package' => [
                    'weight' => $shipmentData['weight_kg'] ?? 1,
                    'dimensions' => [
                        'length' => $shipmentData['length_cm'] ?? 40,
                        'width' => $shipmentData['width_cm'] ?? 30,
                        'height' => $shipmentData['height_cm'] ?? 20,
                    ],
                    'description' => $shipmentData['description'] ?? 'Roba',
                    'value' => $shipmentData['declared_value'] ?? 0,
                ],
                'service' => $shipmentData['service_type'] ?? 'standard',
                'cod_amount' => $shipmentData['cod_amount'] ?? 0,
                'insurance_amount' => $shipmentData['insurance_amount'] ?? 0,
            ];

            // In production, send request to PostExpress API
            $waybillNumber = 'PE' . date('ymd') . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
            $trackingNumber = 'HR' . str_pad(rand(100000000, 999999999), 9, '0', STR_PAD_LEFT);

            return [
                'success' => true,
                'waybill_number' => $waybillNumber,
                'tracking_number' => $trackingNumber,
                'waybill_url' => route('shipping.waybill.download', $waybillNumber),
                'tracking_url' => $this->getTrackingUrl($trackingNumber),
                'courier' => 'postexpress',
                'estimated_delivery' => now()->addDays(3)->format('Y-m-d'),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to create waybill: ' . $e->getMessage(),
                'courier' => 'postexpress',
            ];
        }
    }

    public function getTrackingInfo(string $trackingNumber): array
    {
        try {
            // Mock tracking response
            return [
                'success' => true,
                'tracking_number' => $trackingNumber,
                'courier' => 'postexpress',
                'status' => 'in_transit',
                'status_label' => 'U tranzitu',
                'estimated_delivery' => now()->addDays(3)->format('Y-m-d'),
                'events' => [
                    [
                        'timestamp' => now()->subHours(4)->toIso8601String(),
                        'status' => 'in_transit',
                        'location' => 'Zagreb',
                        'description' => 'Pošiljka je u sortiranju',
                    ],
                    [
                        'timestamp' => now()->subHours(18)->toIso8601String(),
                        'status' => 'picked_up',
                        'location' => 'Rijeka',
                        'description' => 'Pošiljka je preuzeta',
                    ],
                ],
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to get tracking info: ' . $e->getMessage(),
                'courier' => 'postexpress',
            ];
        }
    }

    public function estimateShipping(string $fromCity, string $toCity, float $weightKg): array
    {
        try {
            $pricing = config('shipping.postexpress.pricing', []);
            $basePrice = $pricing['base_price'] ?? 3.50;
            $perKg = $pricing['per_kg'] ?? 0.80;
            
            $price = $basePrice + ($weightKg * $perKg);
            
            return [
                'success' => true,
                'courier' => 'postexpress',
                'service' => 'standard',
                'price' => round($price, 2),
                'currency' => 'EUR',
                'delivery_days' => '2-3',
                'estimated_delivery' => now()->addDays(3)->format('Y-m-d'),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to estimate shipping: ' . $e->getMessage(),
                'courier' => 'postexpress',
            ];
        }
    }

    public function cancelShipment(string $waybillNumber): array
    {
        try {
            return [
                'success' => true,
                'waybill_number' => $waybillNumber,
                'status' => 'cancelled',
                'courier' => 'postexpress',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to cancel shipment: ' . $e->getMessage(),
                'courier' => 'postexpress',
            ];
        }
    }

    public function getName(): string
    {
        return 'PostExpress';
    }

    public function isAvailable(): bool
    {
        return config('shipping.postexpress.enabled', true)
            && !empty($this->apiKey);
    }

    protected function getTrackingUrl(string $trackingNumber): string
    {
        return 'https://track.postexpress.hr/' . $trackingNumber;
    }
}
