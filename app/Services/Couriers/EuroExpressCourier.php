<?php

declare(strict_types=1);

namespace App\Services\Couriers;

use Exception;
use Illuminate\Support\Facades\Http;

class EuroExpressCourier implements CourierInterface
{
    protected string $apiKey;

    protected string $apiSecret;

    protected string $apiUrl;

    /** @var array<string, mixed> */
    protected array $sender;

    public function __construct()
    {
        $this->apiKey = config('shipping.euroexpress.api_key');
        $this->apiSecret = config('shipping.euroexpress.api_secret');
        $this->apiUrl = config('shipping.euroexpress.sandbox', true)
            ? config('shipping.euroexpress.api_url')
            : config('shipping.euroexpress.api_url_production');
        $this->sender = config('shipping.euroexpress.sender', []);
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
                    'email' => $shipmentData['recipient_email'] ?? null,
                ],
                'package' => [
                    'weight' => $shipmentData['weight_kg'] ?? 1,
                    'length' => $shipmentData['length_cm'] ?? 40,
                    'width' => $shipmentData['width_cm'] ?? 30,
                    'height' => $shipmentData['height_cm'] ?? 20,
                    'description' => $shipmentData['description'] ?? 'Roba',
                    'value' => $shipmentData['declared_value'] ?? 0,
                ],
                'service' => $shipmentData['service_type'] ?? 'standard',
                'cod_amount' => $shipmentData['cod_amount'] ?? 0,
                'insurance_amount' => $shipmentData['insurance_amount'] ?? 0,
                'note' => $shipmentData['note'] ?? null,
            ];

            // In production, send request to EuroExpress API
            // $response = Http::withHeaders([
            //     'Authorization' => 'Bearer ' . $this->apiKey,
            //     'Content-Type' => 'application/json',
            // ])->post($this->apiUrl . '/waybills', $waybillData);

            // Mock response for now
            $waybillNumber = 'EE'.date('Ymd').str_pad((string) rand(1, 99999), 5, '0', STR_PAD_LEFT);
            $trackingNumber = 'TRK'.strtoupper(substr(md5($waybillNumber), 0, 10));

            return [
                'success' => true,
                'waybill_number' => $waybillNumber,
                'tracking_number' => $trackingNumber,
                'waybill_url' => route('shipping.waybill.download', $waybillNumber),
                'tracking_url' => $this->getTrackingUrl($trackingNumber),
                'courier' => 'euroexpress',
                'estimated_delivery' => now()->addDays(2)->format('Y-m-d'),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to create waybill: '.$e->getMessage(),
                'courier' => 'euroexpress',
            ];
        }
    }

    public function getTrackingInfo(string $trackingNumber): array
    {
        try {
            // In production, fetch from EuroExpress API
            // $response = Http::withHeaders([
            //     'Authorization' => 'Bearer ' . $this->apiKey,
            // ])->get($this->apiUrl . '/tracking/' . $trackingNumber);

            // Mock tracking events
            return [
                'success' => true,
                'tracking_number' => $trackingNumber,
                'courier' => 'euroexpress',
                'status' => 'in_transit',
                'status_label' => 'U tranzitu',
                'estimated_delivery' => now()->addDays(2)->format('Y-m-d'),
                'events' => [
                    [
                        'timestamp' => now()->subHours(2)->toIso8601String(),
                        'status' => 'in_transit',
                        'location' => 'Sarajevo',
                        'description' => 'Pošiljka je u tranzitu',
                    ],
                    [
                        'timestamp' => now()->subHours(12)->toIso8601String(),
                        'status' => 'picked_up',
                        'location' => 'Zenica',
                        'description' => 'Pošiljka je preuzeta',
                    ],
                    [
                        'timestamp' => now()->subDays(1)->toIso8601String(),
                        'status' => 'label_created',
                        'location' => 'Banja Luka',
                        'description' => 'Tovarni list je kreiran',
                    ],
                ],
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to get tracking info: '.$e->getMessage(),
                'courier' => 'euroexpress',
            ];
        }
    }

    public function estimateShipping(string $fromCity, string $toCity, float $weightKg): array
    {
        try {
            $pricing = config('shipping.euroexpress.pricing', []);
            $basePrice = $pricing['base_price'] ?? 5.00;
            $perKg = $pricing['per_kg'] ?? 1.00;

            $price = $basePrice + ($weightKg * $perKg);

            // Add insurance if declared value provided
            $insuranceRate = $pricing['insurance_percentage'] ?? 0.02;

            return [
                'success' => true,
                'courier' => 'euroexpress',
                'service' => 'standard',
                'price' => round($price, 2),
                'currency' => 'BAM',
                'delivery_days' => '1-2',
                'estimated_delivery' => now()->addDays(2)->format('Y-m-d'),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to estimate shipping: '.$e->getMessage(),
                'courier' => 'euroexpress',
            ];
        }
    }

    public function cancelShipment(string $waybillNumber): array
    {
        return [
            'success' => true,
            'waybill_number' => $waybillNumber,
            'status' => 'cancelled',
            'courier' => 'euroexpress',
        ];
    }

    public function getName(): string
    {
        return 'EuroExpress';
    }

    public function isAvailable(): bool
    {
        return config('shipping.euroexpress.enabled', true)
            && ! empty($this->apiKey);
    }

    /**
     * Get tracking URL
     */
    protected function getTrackingUrl(string $trackingNumber): string
    {
        return 'https://track.euroexpress.ba/'.$trackingNumber;
    }

    /**
     * Get available services
     */
    /**
     * @return array<string, array<string, string>>
     */
    public function getServices(): array
    {
        return config('shipping.euroexpress.services', [
            'standard' => ['name' => 'Standard', 'delivery_days' => '1-2'],
            'express' => ['name' => 'Express', 'delivery_days' => '1'],
        ]);
    }
}
