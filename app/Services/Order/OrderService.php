<?php

namespace App\Services\Order;

use App\Models\Order;
use Illuminate\Support\Facades\Http;

class OrderService
{
    private $TOM_TOM_API_KEY;

    public function __construct()
    {
        $this->TOM_TOM_API_KEY = env('TOMTOM_API_KEY');
    }

    public function index()
    {
        return $this->getCoordinatesWithOrders();
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

        $orders = Order::with('orderItems')->get();

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
}
