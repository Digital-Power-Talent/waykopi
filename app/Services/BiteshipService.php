<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BiteshipService
{
    protected string $apiKey;

    protected string $baseUrl = 'https://api.biteship.com/v1';

    protected string $originAreaId;

    protected string $originPostalCode;

    protected float $originLatitude;

    protected float $originLongitude;

    public function __construct()
    {
        $this->apiKey = (string) config('services.biteship.api_key', '');
        $this->originAreaId = (string) config('services.biteship.origin_area_id', 'IDNP6IDNC384IDND3355');
        $this->originPostalCode = (string) config('services.biteship.origin_postal_code', '16320');
        $this->originLatitude = (float) config('services.biteship.origin_latitude', -6.467812);
        $this->originLongitude = (float) config('services.biteship.origin_longitude', 106.758412);
    }

    /**
     * Create HTTP client with headers and local SSL verification bypass.
     */
    protected function httpClient()
    {
        $client = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
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
    public function getRatesByCoordinates(float $destinationLat, float $destinationLng, array $items, ?float $originLat = null, ?float $originLng = null, string $couriers = 'jne,jnt,sicepat,pos'): array
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
    public function getRatesByPostalCodes(int|string $destinationPostalCode, array $items, int|string|null $originPostalCode = null, string $couriers = 'jne,jnt,sicepat,pos'): array
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
    public function getRatesByAreaId(string $destinationAreaId, array $items, ?string $originAreaId = null, string $couriers = 'jne,jnt,sicepat,pos'): array
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
    public function getRatesByMix(array $origin, array $destination, array $items, string $couriers = 'jne,jnt,sicepat,pos'): array
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
    public function getRatesByType(string $destinationAreaId, array $items, string $type = 'standard', ?string $originAreaId = null, string $couriers = 'jne,jnt,sicepat,pos'): array
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
    public function calculateRates(string $destinationAreaId, array $items, string $couriers = 'jne,jnt,sicepat,pos'): array
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
                'id' => 'IDNP6IDNC384IDND3355',
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
}
