<?php

declare(strict_types=1);

namespace App\Services\Couriers;

use Exception;

class BhPostaCourier implements CourierInterface
{
    protected string $apiKey;

    protected string $apiSecret;

    protected string $apiUrl;

    /** @var array<string, mixed> */
    protected array $sender;

    public function __construct()
    {
        $this->apiKey = config('shipping.bhposta.api_key');
        $this->apiSecret = config('shipping.bhposta.api_secret');
        $this->apiUrl = config('shipping.bhposta.sandbox', true)
            ? config('shipping.bhposta.api_url')
            : config('shipping.bhposta.api_url_production');
        $this->sender = config('shipping.bhposta.sender', []);
    }

    public function createWaybill(array $shipmentData): array
    {
        try {
            $waybillData = [
                'sender' => array_merge($this->sender, [
                    'name' => $shipmentData['sender_name'] ?? $this->sender['name'],
                    'phone' => $shipmentData['sender_phone'] ?? $this->sender['phone'],
                ]),
                'recipient' => [
                    'name' => $shipmentData['recipient_name'],
                    'address' => $shipmentData['recipient_address'],
                    'city' => $shipmentData['recipient_city'],
                    'postal_code' => $shipmentData['recipient_postal_code'],
                    'country' => $shipmentData['recipient_country'] ?? 'BA',
                    'phone' => $shipmentData['recipient_phone'],
                ],
                'package' => [
                    'weight' => $shipmentData['weight_kg'] ?? 1,
                    'description' => $shipmentData['description'] ?? 'Roba',
                    'value' => $shipmentData['declared_value'] ?? 0,
                ],
                'service' => $shipmentData['service_type'] ?? 'standard',
                'cod_amount' => $shipmentData['cod_amount'] ?? 0,
            ];

            // Mock response
            $waybillNumber = 'BP'.date('Ymd').str_pad((string) rand(1, 99999), 5, '0', STR_PAD_LEFT);
            $trackingNumber = 'BA'.str_pad((string) rand(100000000, 999999999), 9, '0', STR_PAD_LEFT);

            return [
                'success' => true,
                'waybill_number' => $waybillNumber,
                'tracking_number' => $trackingNumber,
                'waybill_url' => route('shipping.waybill.download', $waybillNumber),
                'tracking_url' => $this->getTrackingUrl($trackingNumber),
                'courier' => 'bhposta',
                'estimated_delivery' => now()->addDays(3)->format('Y-m-d'),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to create waybill: '.$e->getMessage(),
                'courier' => 'bhposta',
            ];
        }
    }

    public function getTrackingInfo(string $trackingNumber): array
    {
        try {
            return [
                'success' => true,
                'tracking_number' => $trackingNumber,
                'courier' => 'bhposta',
                'status' => 'in_transit',
                'status_label' => 'U tranzitu',
                'estimated_delivery' => now()->addDays(4)->format('Y-m-d'),
                'events' => [
                    [
                        'timestamp' => now()->subHours(6)->toIso8601String(),
                        'status' => 'in_transit',
                        'location' => 'Sarajevo',
                        'description' => 'Pošiljka je u distribuciji',
                    ],
                    [
                        'timestamp' => now()->subDays(1)->toIso8601String(),
                        'status' => 'picked_up',
                        'location' => 'Tuzla',
                        'description' => 'Pošiljka je preuzeta',
                    ],
                ],
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to get tracking info: '.$e->getMessage(),
                'courier' => 'bhposta',
            ];
        }
    }

    public function estimateShipping(string $fromCity, string $toCity, float $weightKg): array
    {
        try {
            $pricing = config('shipping.bhposta.pricing', []);
            $basePrice = $pricing['base_price'] ?? 4.00;
            $perKg = $pricing['per_kg'] ?? 0.90;

            $price = $basePrice + ($weightKg * $perKg);

            return [
                'success' => true,
                'courier' => 'bhposta',
                'service' => 'standard',
                'price' => round($price, 2),
                'currency' => 'BAM',
                'delivery_days' => '2-4',
                'estimated_delivery' => now()->addDays(4)->format('Y-m-d'),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to estimate shipping: '.$e->getMessage(),
                'courier' => 'bhposta',
            ];
        }
    }

    public function cancelShipment(string $waybillNumber): array
    {
        return [
            'success' => true,
            'waybill_number' => $waybillNumber,
            'status' => 'cancelled',
            'courier' => 'bhposta',
        ];
    }

    public function getName(): string
    {
        return 'BH Pošta';
    }

    public function isAvailable(): bool
    {
        return config('shipping.bhposta.enabled', true)
            && ! empty($this->apiKey);
    }

    protected function getTrackingUrl(string $trackingNumber): string
    {
        return 'https://track.bhposta.ba/'.$trackingNumber;
    }
}
