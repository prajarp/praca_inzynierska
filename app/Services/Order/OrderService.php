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

        // array with starting point
        $coordinates = [
            [
                'address' => $startAddress,
                'latitude' => $startLat,
                'longitude' => $startLon,
                'windows' => []
            ]
        ];

        $orders = Order::with('orderItems')->get();

        foreach ($orders as $order) {
            $address = $order->delivery_address;

            $geocodeData = $this->fetchCoordinatesFromApi($address);

            if ($geocodeData) {
                $windows = [];

                // Dodanie szczegółów okien z zamówienia
                foreach ($order->orderItems as $item) {
                    if ($item->item_type === 'window') {
                        $windows[] = [
                            'weight' => $item->weight,
                            'height' => $item->height,
                            'width' => $item->width,
                            'length' => $item->length,
                        ];
                    }
                }

                $coordinates[] = [
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
        $response = Http::get('https://api.tomtom.com/search/2/geocode/' . urlencode($address) . '.json', [
            'key' => $this->TOM_TOM_API_KEY,
            'limit' => 1
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

        while (!empty($coordinates)) {
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
        // Haversine
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


// Metoda do wyznaczania optymalnej trasy za pomocą TomTom Routing API
// private function calculateOptimizedRouteWithTomTom($coordinates)
// {
//     // Przygotowanie listy punktów do przekazania do API
//     $locations = array_map(function ($coord) {
//         return $coord['latitude'] . ',' . $coord['longitude'];
//     }, $coordinates);

//     // Łączenie punktów w odpowiedni format
//     $locationParam = implode(':', $locations);

//     // Wywołanie TomTom Routing API dla pojazdu ciężarowego typu TIR
//     $response = Http::get('https://api.tomtom.com/routing/1/calculateRoute/' . $locationParam . '/json', [
//         'key' => $this->TOM_TOM_API_KEY,
//         'computeBestOrder' => 'true', // Opcja optymalizacji
//         'routeType' => 'shortest',
//         // 'vehicleMaxSpeed' => 90,      // Maksymalna prędkość TIR-a
//         // 'vehicleWeight' => 40000,     // Waga TIR-a w kg
//         // 'vehicleCommercial' => 'true' // Ustawienie dla pojazdów komercyjnych
//     ]);
//     // dd($response->json());

//     // Sprawdzenie odpowiedzi z API
//     if ($response->successful() && isset($response['optimizedWaypoints']) && count($response['optimizedWaypoints']) === count($coordinates)) {
//         $optimizedRoute = [];
//         foreach ($response['optimizedWaypoints'] as $waypoint) {
//             $providedIndex = $waypoint['providedIndex'];
//             $optimizedRoute[] = $coordinates[$providedIndex];
//         }
//         return $optimizedRoute;
//     }
    

//     // W przypadku błędu zwraca oryginalną listę
//     return $coordinates;
// }

        // public function getCoordinatesAndSortBySortestRoute()
    // {
    //     $startLat = 52.955022599573766;
    //     $startLon = 22.294275637435177;
    //     $startAddress = 'Stary Laskowiec 4, 18-300 Stary Laskowiec';
        
    //     $orders = Order::with('orderItems')->get();
    //     // dd($orders->toArray());

    //     // Zainicjowanie tablicy coordinates od punktu startowego
    //     $coordinates = [
    //         [
    //             'address' => $startAddress,
    //             'latitude' => $startLat,
    //             'longitude' => $startLon,
    //             'windows' => [] // Brak elementów okien dla punktu początkowego
    //         ]
    //     ];

    //     // Dodanie pozostałych punktów na podstawie zamówień
    //     foreach ($orders as $order) {

    //         $address = $order->delivery_address;
    //         $cacheKey = 'geocode_' . md5($address . '_' . $order->id);
        
    //         // Bez cache, aby wymusić wywołanie API
    //         $geocodeData = $this->fetchCoordinatesFromApi($address);
    //         // dd($geocodeData); // Sprawdzenie odpowiedzi dla każdego adresu, w tym dla Zakopanego
        

    //         if ($geocodeData) {
    //             $windows = [];
    //             foreach ($order->orderItems as $item) {
    //                 if ($item->item_type === 'window') {
    //                     $windows[] = [
    //                         'weight' => $item->weight,
    //                         'height' => $item->height,
    //                         'width' => $item->width,
    //                         'length' => $item->length,
    //                     ];
    //                 }
    //             }

    //             $coordinates[] = [
    //                 'address' => $order->delivery_address,
    //                 'latitude' => $geocodeData['lat'],
    //                 'longitude' => $geocodeData['lon'],
    //                 'windows' => $windows,
    //             ];
    //         }
    //     }

    //     // Wyznaczenie najbardziej optymalnej trasy z TomTom Routing API
    //     $sortedCoordinates = $this->calculateOptimalRouteWithTomTom($coordinates);
    //     dd($sortedCoordinates); 
    //     return $sortedCoordinates;
    // }

    // Metoda do wyznaczania optymalnej trasy za pomocą TomTom Routing API
//     private function calculateOptimalRouteWithTomTom($coordinates)
// {
//     // Budowanie listy punktów trasy
//     $locations = array_map(function ($coord) {
//         return $coord['latitude'] . ',' . $coord['longitude'];
//     }, $coordinates);

//     $locationParam = implode(':', $locations);

//     // Wywołanie TomTom Routing API
//     $response = Http::get('https://api.tomtom.com/routing/1/calculateRoute/' . $locationParam . '/json', [
//         'key' => $this->TOM_TOM_API_KEY,
//         'computeBestOrder' => 'true', // Opcja optymalizacji
//         'routeType' => 'shortest',
//     ]);

//     // Sprawdzenie odpowiedzi z API
//     if ($response->successful() && isset($response['optimizedWaypoints'])) {
//         $optimizedRoute = [];

//         // Dopasowanie współrzędnych do optymalnej kolejności
//         foreach ($response['optimizedWaypoints'] as $waypoint) {
//             $providedIndex = $waypoint['providedIndex'];
//             $optimizedRoute[] = $coordinates[$providedIndex];
//         }

//         return $optimizedRoute;
//     }

//     // W przypadku błędu zwraca oryginalną listę
//     return $coordinates;
// }