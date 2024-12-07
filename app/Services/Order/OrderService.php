<?php

namespace App\Services\Order;

use App\Models\Order;
use Illuminate\Support\Facades\Http;

class OrderService
{
    private $TOM_TOM_API_KEY;
    private $startLat = 52.955022599573766;
    private $startLon = 22.294275637435177;
    private $startAddress = "Stary Laskowiec 4, 18-300 Stary Laskowiec";

    public function __construct()
    {
        $this->TOM_TOM_API_KEY = env('TOMTOM_API_KEY');
    }

    public function getCoordinatesWithOrders()
    {
        $startLat = 52.955022599573766;
        $startLon = 22.294275637435177;
        $startAddress = 'Stary Laskowiec 4, 18-300 Stary Laskowiec';

        $coordinates = [
            [
                'address' => $startAddress,
                'latitude' => $startLat,
                'longitude' => $startLon,
                'windows' => [],
            ],
        ];

        // $orders = Order::with('orderItems')->get();
        $orders = Order::with('orderItems')
        ->orderBy('id')
        ->take(6)
        ->get();

        foreach ($orders as $order) {
            $address = $order->delivery_address;

            $geocodeData = $this->fetchCoordinatesFromApi($address);

            if ($geocodeData) {
                $windows = [];

                foreach ($order->orderItems as $item) {
                    if ($item->item_type === 'window') {
                        $windows[] = [
                            'weight' => ceil($item->weight),
                            'height' => ceil($item->height),
                            'width' => ceil($item->width),
                            'length' => ceil($item->length),
                        ];
                    }
                }

                $coordinates[] = [
                    'order_id' => $order->id,
                    'address' => $order->delivery_address,
                    'latitude' => $geocodeData['lat'],
                    'longitude' => $geocodeData['lon'],
                    'windows' => $windows,
                ];
            }
        }

        return $this->getOptimizedRoute($coordinates);
    }

    private function fetchCoordinatesFromApi($address)
    {
        $response = Http::get('https://api.tomtom.com/search/2/geocode/'.urlencode($address).'.json', [
            'key' => $this->TOM_TOM_API_KEY,
            'limit' => 1,
        ]);

        if ($response->successful() && isset($response['results'][0]['position'])) {
            return [
                'lat' => $response['results'][0]['position']['lat'],
                'lon' => $response['results'][0]['position']['lon'],
            ];
        }

        return null;
    }

    private function getOptimizedRoute($coordinates)
    {
        $startPoint = array_shift($coordinates);
        $sortedRoute = [$startPoint];

        while (! empty($coordinates)) {
            $lastPoint = end($sortedRoute);

            $nearestKey = null;
            $nearestDistance = PHP_INT_MAX;

            foreach ($coordinates as $key => $point) {
                $distance = $this->calculateDistance(
                    $lastPoint['latitude'], $lastPoint['longitude'],
                    $point['latitude'], $point['longitude']
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

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function calculateRoute()
    {
        $coordinates = $this->getCoordinatesWithOrders();

        $apiKey = $this->TOM_TOM_API_KEY;

        for ($i = 0; $i < count($coordinates) - 1; $i++) {
            $origin = $coordinates[$i];
            $destination = $coordinates[$i + 1];

            $results = [];

            $url = "https://api.tomtom.com/routing/1/calculateRoute/{$origin['latitude']},{$origin['longitude']}:{$destination['latitude']},{$destination['longitude']}/json";

            $response = Http::get($url, [
                'key' => $apiKey,
                'routeType' => 'fastest',
                'travelMode' => 'truck',
                'traffic' => 'true',
            ]);
            if ($response->failed()) {
                $results[] = [
                    'from' => $origin['address'],
                    'to' => $destination['address'],
                    'error' => 'Nie udało się obliczyć trasy.'
                ];
                continue;
            }

            $routeData = $response->json();
            if (!isset($routeData['routes'][0]['summary'])) {
                $results[] = [
                    'from' => $origin['address'],
                    'to' => $destination['address'],
                    'error' => 'Brak danych o trasie.'
                ];
                continue;
            }

            $routeSummary = $routeData['routes'][0]['summary'];
            $results[] = [
                'from' => $origin['address'],
                'to' => $destination['address'],
                'distance_in_km' => round($routeSummary['lengthInMeters'] / 1000, 2),
                'travel_time_in_minutes' => round($routeSummary['travelTimeInSeconds'] / 60, 2),
                'traffic_delay_in_minutes' => round($routeSummary['trafficDelayInSeconds'] / 60, 2)
            ];
            $coordinates[$i + 1] += [
                'travel_info' => $results,
            ];
        };
        $cwel = response()->json($coordinates);
        dd($cwel);
        return response()->json($coordinates);
        // return $coordinates;
    }

}
