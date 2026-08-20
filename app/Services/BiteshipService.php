<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BiteshipService
{
    protected string $apiKey;

    protected string $baseUrl = 'https://api.biteship.com/v1';

    protected string $originContactName;

    protected string $originContactPhone;

    protected string $originAddress;

    protected string $originNote;

    protected string $originAreaId;

    protected string $originPostalCode;

    protected float $originLatitude;

    protected float $originLongitude;

    public function __construct()
    {
        $this->apiKey = (string) config('services.biteship.api_key', '');
        $this->originContactName = (string) config('services.biteship.origin_contact_name', 'Way Kopi Roastery');
        $this->originContactPhone = (string) config('services.biteship.origin_contact_phone', '081234567890');
        $this->originAddress = (string) config('services.biteship.origin_address', 'Jl. Raya Tajurhalang No. 12, Kab. Bogor, Jawa Barat');
        $this->originNote = (string) config('services.biteship.origin_note', 'Dekat gerbang utama Way Kopi Roastery');
        $this->originAreaId = (string) config('services.biteship.origin_area_id', 'IDNP9IDNC74IDND6752IDZ16320');
        $this->originPostalCode = (string) config('services.biteship.origin_postal_code', '16320');
        $this->originLatitude = (float) config('services.biteship.origin_latitude', -6.467812);
        $this->originLongitude = (float) config('services.biteship.origin_longitude', 106.758412);
    }

    /**
     * Create HTTP client with headers and local SSL verification bypass.
     */
    protected function httpClient()
    {
        $token = trim($this->apiKey);
        if (str_starts_with($token, 'Bearer ')) {
            $token = trim(substr($token, 7));
        }

        $client = Http::withHeaders([
            'Authorization' => $token,
            'Content-Type' => 'application/json',
        ]);

        if (app()->environment('local', 'testing') || config('app.debug')) {
            $client = $client->withoutVerifying();
        }

        return $client;
    }

    /**
     * Search location areas for autocomplete (cached for 7 days).
     *
     * @return array<int, array{id: string, name: string, country_name: string, administrative_division_level_1_name: string, administrative_division_level_2_name: string, administrative_division_level_3_name: string, postal_code: int|string}>
     */
    public function searchAreas(string $query): array
    {
        $cleanQuery = trim($query);
        if (empty($cleanQuery)) {
            return [];
        }

        $cacheKey = 'biteship_area_'.md5(strtolower($cleanQuery));

        return Cache::remember($cacheKey, now()->addDays(7), function () use ($cleanQuery) {
            if (empty($this->apiKey)) {
                return $this->getMockAreas($cleanQuery);
            }

            try {
                $response = $this->httpClient()->get("{$this->baseUrl}/maps/areas", [
                    'countries' => 'ID',
                    'input' => $cleanQuery,
                    'type' => 'single',
                ]);

                if ($response->successful() && isset($response->json()['areas'])) {
                    /** @var array<int, array{id: string, name: string, country_name: string, administrative_division_level_1_name: string, administrative_division_level_2_name: string, administrative_division_level_3_name: string, postal_code: int|string}> $areas */
                    $areas = $response->json()['areas'];

                    return $areas;
                }
            } catch (\Throwable $e) {
                Log::warning("Biteship searchAreas failed: {$e->getMessage()}");
            }

            return $this->getMockAreas($cleanQuery);
        });
    }

    /**
     * Core Rate API caller (POST /v1/rates/couriers) (cached for 6 hours).
     * Flexible caller supporting coordinates, postal codes, area IDs, mix, or type filtering.
     *
     * @param  array<string, mixed>  $payload
     * @return array<int, array{courier_code: string, courier_name: string, courier_service_code: string, courier_service_name: string, price: float, duration: string, ship_date?: string, type?: string}>
     */
    public function getRates(array $payload): array
    {
        if (empty($payload['items'] ?? [])) {
            return [];
        }

        $cacheKey = 'biteship_rates_'.md5((string) json_encode($payload));

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($payload) {
            if (empty($this->apiKey)) {
                return $this->getMockRates($payload['items']);
            }

            try {
                $response = $this->httpClient()->post("{$this->baseUrl}/rates/couriers", $payload);

                if ($response->successful() && isset($response->json()['pricing'])) {
                    $rates = [];
                    foreach ($response->json()['pricing'] as $rate) {
                        $rates[] = [
                            'courier_code' => $rate['courier_code'] ?? 'courier',
                            'courier_name' => $rate['courier_name'] ?? 'Kurir',
                            'courier_service_code' => $rate['courier_service_code'] ?? 'reg',
                            'courier_service_name' => $rate['courier_service_name'] ?? 'Regular',
                            'price' => (float) ($rate['price'] ?? 10000),
                            'duration' => $rate['duration'] ?? '1-3 hari',
                            'ship_date' => $rate['ship_date'] ?? null,
                            'type' => $rate['type'] ?? null,
                        ];
                    }

                    return $rates;
                }
            } catch (\Throwable $e) {
                Log::warning("Biteship getRates failed: {$e->getMessage()}");
            }

            return $this->getMockRates($payload['items']);
        });
    }

    /**
     * POST /v1/rates/couriers — by coordinates
     *
     * @param  array<int, array{name: string, value: float, quantity: int, weight: int}>  $items
     * @return array<int, array{courier_code: string, courier_name: string, courier_service_code: string, courier_service_name: string, price: float, duration: string}>
     */
    public function getRatesByCoordinates(float $destinationLat, float $destinationLng, array $items, ?float $originLat = null, ?float $originLng = null, string $couriers = 'jne,jnt,sicepat,tiki,anteraja,pos'): array
    {
        $payload = [
            'origin_latitude' => $originLat ?: $this->originLatitude,
            'origin_longitude' => $originLng ?: $this->originLongitude,
            'destination_latitude' => $destinationLat,
            'destination_longitude' => $destinationLng,
            'couriers' => $couriers,
            'items' => $items,
        ];

        return $this->getRates($payload);
    }

    /**
     * POST /v1/rates/couriers — by postal codes
     *
     * @param  array<int, array{name: string, value: float, quantity: int, weight: int}>  $items
     * @return array<int, array{courier_code: string, courier_name: string, courier_service_code: string, courier_service_name: string, price: float, duration: string}>
     */
    public function getRatesByPostalCodes(int|string $destinationPostalCode, array $items, int|string|null $originPostalCode = null, string $couriers = 'jne,jnt,sicepat,tiki,anteraja,pos'): array
    {
        $payload = [
            'origin_postal_code' => (int) ($originPostalCode ?: $this->originPostalCode),
            'destination_postal_code' => (int) $destinationPostalCode,
            'couriers' => $couriers,
            'items' => $items,
        ];

        return $this->getRates($payload);
    }

    /**
     * POST /v1/rates/couriers — by area id
     *
     * @param  array<int, array{name: string, value: float, quantity: int, weight: int}>  $items
     * @return array<int, array{courier_code: string, courier_name: string, courier_service_code: string, courier_service_name: string, price: float, duration: string}>
     */
    public function getRatesByAreaId(string $destinationAreaId, array $items, ?string $originAreaId = null, string $couriers = 'jne,jnt,sicepat,tiki,anteraja,pos'): array
    {
        if (empty($destinationAreaId)) {
            return [];
        }

        $payload = [
            'origin_area_id' => $originAreaId ?: $this->originAreaId,
            'destination_area_id' => $destinationAreaId,
            'couriers' => $couriers,
            'items' => $items,
        ];

        return $this->getRates($payload);
    }

    /**
     * POST /v1/rates/couriers — by mix (e.g. origin area_id + destination postal_code or coordinates)
     *
     * @param  array<string, mixed>  $origin
     * @param  array<string, mixed>  $destination
     * @param  array<int, array{name: string, value: float, quantity: int, weight: int}>  $items
     * @return array<int, array{courier_code: string, courier_name: string, courier_service_code: string, courier_service_name: string, price: float, duration: string}>
     */
    public function getRatesByMix(array $origin, array $destination, array $items, string $couriers = 'jne,jnt,sicepat,tiki,anteraja,pos'): array
    {
        $payload = array_merge($origin, $destination, [
            'couriers' => $couriers,
            'items' => $items,
        ]);

        return $this->getRates($payload);
    }

    /**
     * POST /v1/rates/couriers — by type (e.g. instant, same_day, standard, express, overnight)
     *
     * @param  array<int, array{name: string, value: float, quantity: int, weight: int}>  $items
     * @return array<int, array{courier_code: string, courier_name: string, courier_service_code: string, courier_service_name: string, price: float, duration: string}>
     */
    public function getRatesByType(string $destinationAreaId, array $items, string $type = 'standard', ?string $originAreaId = null, string $couriers = 'jne,jnt,sicepat,tiki,anteraja,pos'): array
    {
        $payload = [
            'origin_area_id' => $originAreaId ?: $this->originAreaId,
            'destination_area_id' => $destinationAreaId,
            'couriers' => $couriers,
            'type' => $type,
            'items' => $items,
        ];

        return $this->getRates($payload);
    }

    /**
     * Legacy helper method for backward compatibility.
     *
     * @param  array<int, array{name: string, value: float, quantity: int, weight: int}>  $items
     * @return array<int, array{courier_code: string, courier_name: string, courier_service_code: string, courier_service_name: string, price: float, duration: string}>
     */
    public function calculateRates(string $destinationAreaId, array $items, string $couriers = 'jne,jnt,sicepat,tiki,anteraja,pos'): array
    {
        return $this->getRatesByAreaId($destinationAreaId, $items, null, $couriers);
    }

    /**
     * Mock areas fallback for dev/testing when API key is missing.
     *
     * @return array<int, array{id: string, name: string, country_name: string, administrative_division_level_1_name: string, administrative_division_level_2_name: string, administrative_division_level_3_name: string, postal_code: int|string}>
     */
    protected function getMockAreas(string $query): array
    {
        $allMocks = [
            [
                'id' => 'IDNP9IDNC74IDND6752IDZ16320',
                'name' => 'Tajurhalang, Kab. Bogor, Jawa Barat (16320)',
                'country_name' => 'Indonesia',
                'administrative_division_level_1_name' => 'Jawa Barat',
                'administrative_division_level_2_name' => 'Kabupaten Bogor',
                'administrative_division_level_3_name' => 'Tajurhalang',
                'postal_code' => 16320,
            ],
            [
                'id' => 'IDNP6IDNC385IDND3366',
                'name' => 'Bogor Barat, Kota Bogor, Jawa Barat (16115)',
                'country_name' => 'Indonesia',
                'administrative_division_level_1_name' => 'Jawa Barat',
                'administrative_division_level_2_name' => 'Kota Bogor',
                'administrative_division_level_3_name' => 'Bogor Barat',
                'postal_code' => 16115,
            ],
            [
                'id' => 'IDNP6IDNC385IDND3367',
                'name' => 'Bogor Tengah, Kota Bogor, Jawa Barat (16121)',
                'country_name' => 'Indonesia',
                'administrative_division_level_1_name' => 'Jawa Barat',
                'administrative_division_level_2_name' => 'Kota Bogor',
                'administrative_division_level_3_name' => 'Bogor Tengah',
                'postal_code' => 16121,
            ],
            [
                'id' => 'IDNP31IDNC157IDND1280',
                'name' => 'Kebayoran Baru, Kota Jakarta Selatan, DKI Jakarta (12110)',
                'country_name' => 'Indonesia',
                'administrative_division_level_1_name' => 'DKI Jakarta',
                'administrative_division_level_2_name' => 'Kota Jakarta Selatan',
                'administrative_division_level_3_name' => 'Kebayoran Baru',
                'postal_code' => 12110,
            ],
            [
                'id' => 'IDNP18IDNC329IDND2841',
                'name' => 'Tanggamus, Kota Bandar Lampung, Lampung (35111)',
                'country_name' => 'Indonesia',
                'administrative_division_level_1_name' => 'Lampung',
                'administrative_division_level_2_name' => 'Kota Bandar Lampung',
                'administrative_division_level_3_name' => 'Tanggamus',
                'postal_code' => 35111,
            ],
            [
                'id' => 'IDNP6IDNC149IDND853IDZ13110',
                'name' => 'Matraman, Kota Jakarta Timur, DKI Jakarta (13110)',
                'country_name' => 'Indonesia',
                'administrative_division_level_1_name' => 'DKI Jakarta',
                'administrative_division_level_2_name' => 'Kota Jakarta Timur',
                'administrative_division_level_3_name' => 'Matraman',
                'postal_code' => 13110,
            ],
        ];

        $q = strtolower(trim($query));

        if (empty($q)) {
            return $allMocks;
        }

        $filtered = array_values(array_filter($allMocks, function ($area) use ($q) {
            return str_contains(strtolower($area['name']), $q);
        }));

        return ! empty($filtered) ? $filtered : $allMocks;
    }

    /**
     * Mock rates fallback for dev/testing.
     *
     * @param  array<int, mixed>  $items
     * @return array<int, array{courier_code: string, courier_name: string, courier_service_code: string, courier_service_name: string, price: float, duration: string}>
     */
    protected function getMockRates(array $items): array
    {
        $totalWeight = 0;
        foreach ($items as $item) {
            $totalWeight += ((int) ($item['weight'] ?? 200)) * ((int) ($item['quantity'] ?? 1));
        }

        $multiplier = max(1, (int) ceil($totalWeight / 1000));

        return [
            [
                'courier_code' => 'sicepat',
                'courier_name' => 'SiCepat',
                'courier_service_code' => 'reg',
                'courier_service_name' => 'REG (Reguler)',
                'price' => 10000.00 * $multiplier,
                'duration' => '1 - 2 Hari',
            ],
            [
                'courier_code' => 'jnt',
                'courier_name' => 'J&T Express',
                'courier_service_code' => 'ez',
                'courier_service_name' => 'EZ (Reguler)',
                'price' => 11000.00 * $multiplier,
                'duration' => '1 - 2 Hari',
            ],
            [
                'courier_code' => 'jne',
                'courier_name' => 'JNE',
                'courier_service_code' => 'reg',
                'courier_service_name' => 'REG (Reguler)',
                'price' => 12000.00 * $multiplier,
                'duration' => '2 - 3 Hari',
            ],
        ];
    }

    /**
     * Create an Order in Biteship (POST /v1/orders).
     *
     * @param  array<string, mixed>  $overrideParams
     * @return array{success: bool, message: string, order_id?: string, tracking_number?: string, label_url?: string, status?: string, data?: array<string, mixed>}
     */
    public function createOrder(Order $order, array $overrideParams = []): array
    {
        $order->loadMissing(['items.productVariant.product', 'payment', 'shipment', 'user']);

        /** @var Shipment|null $shipment */
        $shipment = $order->shipment;
        $courierCompany = strtolower((string) ($overrideParams['courier_company'] ?? $shipment?->courier_code ?? 'jne'));
        $courierType = strtolower((string) ($overrideParams['courier_type'] ?? $shipment?->courier_service_code ?? 'reg'));

        if (empty($courierType) && ! empty($shipment?->courier_service)) {
            $parts = explode(' ', strtolower(trim((string) $shipment->courier_service)));
            $courierType = end($parts) ?: 'reg';
        }

        $itemsPayload = [];
        foreach ($order->items as $item) {
            $variant = $item->productVariant;
            $itemsPayload[] = [
                'name' => $item->product_name.($item->variant_label ? " ({$item->variant_label})" : ''),
                'description' => 'Produk Kopi Way Kopi',
                'category' => 'food_and_drink',
                'value' => (float) $item->price_at_purchase,
                'quantity' => (int) $item->quantity,
                'weight' => (int) ($variant?->weight_grams ?? 250),
                'length' => 10,
                'width' => 10,
                'height' => 10,
            ];
        }

        if (empty($itemsPayload)) {
            $itemsPayload[] = [
                'name' => "Pesanan {$order->order_number}",
                'description' => 'Kopi Sangrai Way Kopi',
                'category' => 'food_and_drink',
                'value' => (float) ($order->subtotal > 0 ? $order->subtotal : 50000),
                'quantity' => 1,
                'weight' => 250,
                'length' => 10,
                'width' => 10,
                'height' => 10,
            ];
        }

        $isCod = ($order->payment?->method === 'cod');

        $payload = [
            'shipper_contact_name' => $this->originContactName,
            'shipper_contact_phone' => $this->originContactPhone,
            'origin_contact_name' => $this->originContactName,
            'origin_contact_phone' => $this->originContactPhone,
            'origin_address' => $this->originAddress,
            'origin_note' => $this->originNote,
            'origin_postal_code' => (int) $this->originPostalCode,
            'origin_area_id' => $this->originAreaId,
            'origin_coordinate' => [
                'latitude' => $this->originLatitude,
                'longitude' => $this->originLongitude,
            ],
            'destination_contact_name' => $order->recipient_name,
            'destination_contact_phone' => (string) ($order->recipient_phone ?: ($order->guest_phone ?: '081234567890')),
            'destination_contact_email' => $order->guest_email ?: ($order->user?->email ?: null),
            'destination_address' => $order->shipping_address,
            'destination_postal_code' => (int) ($order->postal_code ?: 16115),
            'courier_company' => $courierCompany,
            'courier_type' => $courierType,
            'delivery_type' => 'now',
            'order_note' => (string) ($order->notes ?: ''),
            'reference_id' => (string) $order->order_number,
            'items' => $itemsPayload,
        ];

        if (! empty($shipment?->destination_area_id)) {
            $payload['destination_area_id'] = $shipment->destination_area_id;
        }

        if ($isCod) {
            $payload['destination_cash_on_delivery'] = (float) $order->total;
            $payload['destination_cash_on_delivery_type'] = '7_days';
        }

        $payload = array_merge($payload, $overrideParams);

        if (empty($this->apiKey) || str_starts_with($this->apiKey, 'mock_')) {
            return $this->getMockCreatedOrder($order, $payload);
        }

        try {
            $response = $this->httpClient()->post("{$this->baseUrl}/orders", $payload);
            $responseData = $response->json() ?? [];

            if ($response->successful() && ! empty($responseData['id'])) {
                $biteshipOrderId = (string) $responseData['id'];
                $courierData = $responseData['courier'] ?? [];
                $waybillId = (string) ($courierData['waybill_id'] ?? $courierData['tracking_id'] ?? '');
                $labelUrl = (string) ($courierData['link'] ?? '');
                $biteshipStatus = (string) ($responseData['status'] ?? 'placed');

                if ($shipment) {
                    $shipmentStatus = match (strtolower($biteshipStatus)) {
                        'delivered' => 'delivered',
                        'cancelled', 'rejected' => 'failed',
                        'picking_up', 'picked', 'dropping_off', 'in_transit' => 'in_transit',
                        default => 'booked',
                    };

                    $shipment->update([
                        'biteship_order_id' => $biteshipOrderId,
                        'tracking_number' => $waybillId ?: $shipment->tracking_number,
                        'label_url' => $labelUrl ?: $shipment->label_url,
                        'status' => $shipmentStatus,
                    ]);
                }

                $order->recordStatusChange(
                    $order->status === 'pending_payment' ? 'processing' : $order->status,
                    Auth::id(),
                    "Pesanan berhasil didaftarkan ke Biteship (ID: {$biteshipOrderId})".($waybillId ? ", Resi: {$waybillId}" : '')
                );

                return [
                    'success' => true,
                    'message' => 'Pesanan berhasil didaftarkan ke Biteship.',
                    'order_id' => $biteshipOrderId,
                    'tracking_number' => $waybillId,
                    'label_url' => $labelUrl,
                    'status' => $biteshipStatus,
                    'data' => $responseData,
                ];
            }

            $errorMessage = $responseData['error'] ?? $responseData['message'] ?? 'Biteship API Error ('.$response->status().')';
            Log::warning("Biteship createOrder error: {$errorMessage}", ['response' => $responseData]);

            return [
                'success' => false,
                'message' => "Gagal membuat pesanan di Biteship: {$errorMessage}",
                'data' => $responseData,
            ];
        } catch (\Throwable $e) {
            Log::error("Biteship createOrder exception: {$e->getMessage()}");

            return [
                'success' => false,
                'message' => "Terjadi kesalahan koneksi ke Biteship: {$e->getMessage()}",
            ];
        }
    }

    /**
     * Retrieve Order from Biteship (GET /v1/orders/:id).
     *
     * @return array<string, mixed>|null
     */
    public function getOrder(string $biteshipOrderId): ?array
    {
        if (empty($biteshipOrderId)) {
            return null;
        }

        if (empty($this->apiKey) || str_starts_with($this->apiKey, 'mock_')) {
            return [
                'id' => $biteshipOrderId,
                'status' => 'allocated',
                'courier' => [
                    'company' => 'jne',
                    'type' => 'reg',
                    'waybill_id' => 'WYB-'.substr(md5($biteshipOrderId), 0, 10),
                    'link' => "https://biteship.com/labels/{$biteshipOrderId}",
                ],
            ];
        }

        try {
            $response = $this->httpClient()->get("{$this->baseUrl}/orders/{$biteshipOrderId}");
            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Throwable $e) {
            Log::warning("Biteship getOrder failed for {$biteshipOrderId}: {$e->getMessage()}");
        }

        return null;
    }

    /**
     * Cancel an Order in Biteship (POST /v1/orders/:id/cancel).
     *
     * @return array{success: bool, message: string, data?: array<string, mixed>}
     */
    public function cancelOrder(string $biteshipOrderId, string $reasonCode = 'others', ?string $reason = null): array
    {
        if (empty($biteshipOrderId)) {
            return ['success' => false, 'message' => 'Biteship Order ID tidak boleh kosong.'];
        }

        if (empty($this->apiKey) || str_starts_with($this->apiKey, 'mock_')) {
            return [
                'success' => true,
                'message' => 'Pesanan Biteship berhasil dibatalkan (Mock).',
                'data' => [
                    'id' => $biteshipOrderId,
                    'status' => 'cancelled',
                ],
            ];
        }

        try {
            $response = $this->httpClient()->post("{$this->baseUrl}/orders/{$biteshipOrderId}/cancel", [
                'cancellation_reason_code' => $reasonCode,
                'cancellation_reason' => $reason ?: 'Dibatalkan oleh Penjual / Admin',
            ]);

            $responseData = $response->json() ?? [];

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Pesanan di Biteship berhasil dibatalkan.',
                    'data' => $responseData,
                ];
            }

            $errorMessage = $responseData['error'] ?? $responseData['message'] ?? 'Gagal membatalkan pesanan Biteship.';

            return [
                'success' => false,
                'message' => $errorMessage,
                'data' => $responseData,
            ];
        } catch (\Throwable $e) {
            Log::error("Biteship cancelOrder failed: {$e->getMessage()}");

            return [
                'success' => false,
                'message' => "Terjadi kesalahan saat membatalkan order di Biteship: {$e->getMessage()}",
            ];
        }
    }

    /**
     * Get Public Tracking by Waybill & Courier Code.
     *
     * @return array<string, mixed>|null
     */
    public function getTracking(string $waybillId, string $courierCode): ?array
    {
        if (empty($waybillId) || empty($courierCode)) {
            return null;
        }

        try {
            $response = $this->httpClient()->get("{$this->baseUrl}/trackings/{$waybillId}/couriers/{$courierCode}");
            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Throwable $e) {
            Log::warning("Biteship getTracking failed for {$waybillId}: {$e->getMessage()}");
        }

        return null;
    }

    /**
     * Mock order creation for testing or missing API key.
     *
     * @param  array<string, mixed>  $payload
     * @return array{success: bool, message: string, order_id: string, tracking_number: string, label_url: string, status: string, data: array<string, mixed>}
     */
    protected function getMockCreatedOrder(Order $order, array $payload): array
    {
        $mockId = 'BTS-'.strtoupper(bin2hex(random_bytes(6)));
        $mockWaybill = 'WYB-'.rand(1000000000, 9999999999);
        $mockLabel = "https://biteship.com/labels/{$mockId}";

        /** @var Shipment|null $shipment */
        $shipment = $order->shipment;
        if ($shipment) {
            $shipment->update([
                'biteship_order_id' => $mockId,
                'tracking_number' => $mockWaybill,
                'label_url' => $mockLabel,
                'status' => 'booked',
            ]);
        }

        $order->recordStatusChange(
            $order->status === 'pending_payment' ? 'processing' : $order->status,
            Auth::id(),
            "Pesanan berhasil didaftarkan ke Biteship (Mock ID: {$mockId}, Resi: {$mockWaybill})"
        );

        return [
            'success' => true,
            'message' => 'Pesanan berhasil dibuat di Biteship (Mode Mock/Testing).',
            'order_id' => $mockId,
            'tracking_number' => $mockWaybill,
            'label_url' => $mockLabel,
            'status' => 'placed',
            'data' => [
                'id' => $mockId,
                'status' => 'placed',
                'courier' => [
                    'company' => $payload['courier_company'] ?? 'jne',
                    'type' => $payload['courier_type'] ?? 'reg',
                    'waybill_id' => $mockWaybill,
                    'link' => $mockLabel,
                ],
            ],
        ];
    }
}
