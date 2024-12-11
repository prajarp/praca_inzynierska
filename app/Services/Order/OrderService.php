<?php

namespace App\Services\Order;

use App\Models\Order;
use Illuminate\Support\Facades\Http;

class OrderService
{
    private string $TOM_TOM_API_KEY;
    private float $START_LAT = 52.955022599573766;
    private float $START_LON = 22.294275637435177;
    private string $START_ADDRESS = 'Stary Laskowiec 4, 18-300 Stary Laskowiec';

    public function __construct()
    {
        $this->TOM_TOM_API_KEY = env('TOMTOM_API_KEY');
    }

    public function roundToNearestHalf(float $value): float
    {
        $floor = floor($value);
        $fraction = $value - $floor;

        return $fraction <= 0.5 ? $floor + 0.5 : ceil($value);
    }

    public function getCoordinatesWithOrders(): array
    {
        $coordinates = [[
            'address'   => $this->START_ADDRESS,
            'latitude'  => $this->START_LAT,
            'longitude' => $this->START_LON,
            'windows'   => [],
        ]];

        $orders = Order::with('orderItems')->orderBy('id')->take(6)->get();

        foreach ($orders as $order) {
            $address = $order->delivery_address;
            $geocodeData = $this->fetchCoordinatesFromApi($address);

            if ($geocodeData) {
                $windows = $order->orderItems
                    ->filter(fn ($item) => $item->item_type === 'window')
                    ->map(fn ($item) => [
                        'weight' => $item->weight,
                        'height' => $this->roundToNearestHalf($item->height),
                        'width'  => $this->roundToNearestHalf($item->width),
                        'length' => $this->roundToNearestHalf($item->length),
                    ])
                    ->values()
                    ->toArray();

                $coordinates[] = [
                    'order_id'  => $order->id,
                    'address'   => $address,
                    'latitude'  => $geocodeData['lat'],
                    'longitude' => $geocodeData['lon'],
                    'windows'   => $windows,
                ];
            }
        }

        return $this->getOptimizedRoute($coordinates);
    }

    private function fetchCoordinatesFromApi(string $address): ?array
    {
        $response = Http::get(
            'https://api.tomtom.com/search/2/geocode/'.urlencode($address).'.json',
            ['key' => $this->TOM_TOM_API_KEY, 'limit' => 1]
        );

        if ($response->successful() && isset($response['results'][0]['position'])) {
            return [
                'lat' => $response['results'][0]['position']['lat'],
                'lon' => $response['results'][0]['position']['lon'],
            ];
        }

        return null;
    }

    private function getOptimizedRoute(array $coordinates): array
    {
        $startPoint = array_shift($coordinates);
        $sortedRoute = [$startPoint];

        while (!empty($coordinates)) {
            $lastPoint = end($sortedRoute);
            $nearestKey = null;
            $nearestDistance = PHP_INT_MAX;

            foreach ($coordinates as $key => $point) {
                $distance = $this->calculateDistance(
                    $lastPoint['latitude'],
                    $lastPoint['longitude'],
                    $point['latitude'],
                    $point['longitude']
                );

                if ($distance < $nearestDistance) {
                    $nearestDistance = $distance;
                    $nearestKey = $key;
                }
            }

            $sortedRoute[] = $coordinates[$nearestKey];
            unset($coordinates[$nearestKey]);
        }

        return $sortedRoute;
    }

    private function calculateDistance(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2
    ): float {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2 +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) ** 2;

        return 2 * $earthRadius * atan2(sqrt($a), sqrt(1 - $a));
    }

    public function calculateRoute()
    {
        $coordinates = $this->getCoordinatesWithOrders();
        $apiKey = $this->TOM_TOM_API_KEY;

        foreach (range(0, count($coordinates) - 2) as $i) {
            $origin = $coordinates[$i];
            $destination = $coordinates[$i + 1];
            $results = [];

            $url = sprintf(
                'https://api.tomtom.com/routing/1/calculateRoute/%s,%s:%s,%s/json',
                $origin['latitude'], $origin['longitude'],
                $destination['latitude'], $destination['longitude']
            );

            $response = Http::get($url, [
                'key'        => $apiKey,
                'routeType'  => 'fastest',
                'travelMode' => 'truck',
                'traffic'    => 'true',
            ]);

            if ($response->failed()) {
                $results[] = [
                    'from'  => $origin['address'],
                    'to'    => $destination['address'],
                    'error' => 'Nie udało się obliczyć trasy.',
                ];
                continue;
            }

            $routeData = $response->json();

            if (!isset($routeData['routes'][0]['summary'])) {
                $results[] = [
                    'from'  => $origin['address'],
                    'to'    => $destination['address'],
                    'error' => 'Brak danych o trasie.',
                ];
                continue;
            }

            $routeSummary = $routeData['routes'][0]['summary'];

            $results[] = [
                'from'                   => $origin['address'],
                'to'                     => $destination['address'],
                'distance_in_km'         => round($routeSummary['lengthInMeters'] / 1000, 2),
                'travel_time_in_minutes' => round($routeSummary['travelTimeInSeconds'] / 60, 2),
                'traffic_delay_in_minutes' => round($routeSummary['trafficDelayInSeconds'] / 60, 2),
            ];
            $coordinates[$i + 1] += [
                'travel_info' => $results,
            ];
        };
        return response()->json($coordinates);
    }

    public function getStartLocation(): array
    {
        return [
            'latitude' => $this->START_LAT,
            'longitude' => $this->START_LON,
            'address' => $this->START_ADDRESS,
        ];
    }
}
