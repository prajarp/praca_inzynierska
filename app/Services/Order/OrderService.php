<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\SelectedOrder;
use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;

class OrderService
{
    private string $TOM_TOM_API_KEY;
    private float $START_LAT;
    private float $START_LON;
    private string $START_ADDRESS;

    public function __construct()
    {
        $this->TOM_TOM_API_KEY = env('TOMTOM_API_KEY');
        $this->START_ADDRESS = env('START_ADDRESS');
        $this->START_LAT = env('START_LAT');
        $this->START_LON = env('START_LON');
    }

    public function getCoordinatesWithOrders(): array
    {
        $startedLocations = $this->getStartLocation();

        $coordinates = [[
            'address'   => $startedLocations['address'],
            'latitude'  => $startedLocations['latitude'],
            'longitude' => $startedLocations['longitude'],
            'windows'   => [],
        ]];

        $selectedOrders = SelectedOrder::with('order', 'order.coordinates', 'order.selectedOrders')->get();

        foreach ($selectedOrders as $selectedOrder) {
            $address = $selectedOrder->order->delivery_address;
            if (!is_null($selectedOrder->order->coordinates?->latitude) && !is_null($selectedOrder->order->coordinates?->longitude)) {
                $coordinates[] = [
                    'order_id'  => $selectedOrder->order->id,
                    'address'   => $address,
                    'latitude'  => $selectedOrder->order->coordinates->latitude,
                    'longitude' => $selectedOrder->order->coordinates->longitude,
                    'windows'   => $this->getOrderWindows($selectedOrder->order),
                ];
                continue;
            }

            $geocodeData = $this->fetchCoordinatesFromApi($address);

            if ($geocodeData) {
                $coordinates[] = [
                    'order_id'  => $selectedOrder->order->id,
                    'address'   => $address,
                    'latitude'  => $geocodeData['lat'],
                    'longitude' => $geocodeData['lon'],
                    'windows'   => $this->getOrderWindows($selectedOrder->order),
                ];
            }
        }

        return $this->getOptimizedRoute($coordinates);
    }

    private function getOrderWindows($order): array
    {
        return $order->orderItems
            ->whenNotEmpty(function ($items) {
                return $items->filter(fn($item) => $item->item_type === 'window')
                    ->map(fn($item) => [
                        'weight' => $item->weight,
                        'height' => $item->height,
                        'width'  => $item->width,
                        'length' => $item->length,
                    ]);
            }, fn() => collect([]))
            ->values()
            ->toArray();
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

    public function getStartLocation(): array
    {
        return [
            'latitude' => $this->START_LAT,
            'longitude' => $this->START_LON,
            'address' => $this->START_ADDRESS,
        ];
    }
}
