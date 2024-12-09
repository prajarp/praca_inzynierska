<?php

namespace App\Services\Packing;

use App\Models\Order;
use App\Models\Rack;
use App\Services\Order\OrderService;
use Latuconsinafr\BinPackager\BinPackager3D\Bin;
use Latuconsinafr\BinPackager\BinPackager3D\Item;
use Latuconsinafr\BinPackager\BinPackager3D\Packager;

class PackingService
{
    public function __construct(private OrderService $orderService) {}

    /**
     * @return array<int, array{order_id: int|null, address: string, sorted_windows: array<int, array<string, mixed>>}>
     */
    public function getSortedItemsForEachOrder(): array
    {
        $coordinatesWithOrders = $this->orderService->getCoordinatesWithOrders();

        $sortedOrders = [];

        foreach ($coordinatesWithOrders as $orderData) {
            if (! empty($orderData['windows'])) {
                $sortedItems = collect($orderData['windows'])->sortByDesc('height')->toArray();

                $sortedOrders[] = [
                    'order_id' => $orderData['order_id'] ?? null,
                    'address' => $orderData['address'],
                    'sorted_windows' => $sortedItems,
                ];
            }
        }

        return $sortedOrders;
    }

    private function handleUnfittedItems($packager, $bin, $orderId)
    {
        $unfittedItems = $bin->getUnfittedItems();

        if (empty($unfittedItems)) {
            return;
        }

        usort($unfittedItems, function ($a, $b) {
            return $b->getBreadth() <=> $a->getBreadth();
        });

        $largestBreadthItem = $unfittedItems[0];
        $widestItem = $largestBreadthItem->getBreadth();

        $rack = Rack::where('loading_height', '>=', $widestItem)->first();

        $newBin = new Bin(
            $orderId.'.'.(count($packager->getBins()) + 1),
            $rack->loading_length,
            $rack->loading_width,
            $rack->loading_height,
            $rack->net_weight,
        );

        foreach ($unfittedItems as $index => $window) {
            $uniqueId = $window->getId().'_rest_'.($index + 1);

            $item = new Item(
                $uniqueId,
                $window->getLength(),
                $window->getHeight(),
                $window->getBreadth(),
                $window->getWeight(),
                false,
            );

            $packager->addItem($item);
            $packager->packItemToBin($newBin, $item);
        }
        $packager->addBin($newBin);

        $bin->clearUnfittedItems();

        $this->handleUnfittedItems($packager, $newBin, $orderId);
    }
    public function packOrdersIntoTrailer()
{
    $sortedOrders = collect($this->getSortedItemsForEachOrder());

    $trailer = [
        'matrix' => [],
        'total_weight' => 0,
        'total_volume' => 0,
    ];

    // Przygotowanie wyników bez zagnieżdżonych pętli
    $results = $sortedOrders->map(function ($order) {
        $packager = new Packager();

        $weightSum = collect($order['sorted_windows'])->sum('weight');
        $heighestItem = collect($order['sorted_windows'])->max('height');

        $rack = Rack::where('net_weight', '>=', $weightSum)
            ->where('loading_height', '>=', $heighestItem)
            ->first();

        if (! $rack) {
            $rack = Rack::where('loading_height', '>=', ($heighestItem))->first();
        }

        $height = max($heighestItem, $rack->loading_height);

        $bin = new Bin(
            $order['order_id'].'.1',
            $rack->loading_length,
            $rack->loading_width,
            $height,
            $rack->net_weight,
        );

        // Zbieramy wszystkie elementy do spakowania
        $items = collect($order['sorted_windows'])->map(function ($window, $index) use ($order) {
            return new Item(
                'window_'.$order['order_id'].'_'.($index + 1),
                $window['length'],
                $window['height'],
                $window['width'],
                $window['weight'],
                false,
            );
        });

        // Dodanie elementów i ich spakowanie
        foreach ($items as $item) {
            $packager->addItem($item);
            $packager->packItemToBin($bin, $item);
        }

        $packager->addBin($bin);
        $this->handleUnfittedItems($packager, $bin, $order['order_id']);

        // Pobranie informacji o zamówieniu
        $orderInfo = Order::where('id', $order['order_id'])->first();

        // Wynik dla tego zamówienia
        return collect($packager->getBins())->map(function ($packedBin) use ($order, $orderInfo) {
            return [
                'order' => $order['order_id'],
                'info' => $orderInfo,
                'bin' => $packedBin,
            ];
        });
    })->flatten(1);

    // Łączenie wyników z trailer['matrix']
    $trailer['matrix'] = $results->all();

    // Przesuwanie ostatnich trzech elementów na indeksy 3, 4, 5
    $this->moveLastThreeToIndexes($trailer['matrix']);

    // Obliczanie łącznej wagi i objętości
    $trailer['total_weight'] = $results->sum(fn($result) => $result['bin']->getWeight());
    $trailer['total_volume'] = $results->sum(fn($result) => $result['bin']->getVolume());
    return [
        'trailer' => $trailer,
    ];
}

    // public function packOrdersIntoTrailer()
    // {
    //     $results = [];
    //     $sortedOrders = collect($this->getSortedItemsForEachOrder());

    //     $trailer = [
    //         'matrix' => [],
    //         'total_weight' => 0,
    //         'total_volume' => 0,
    //     ];

    //     foreach ($sortedOrders as $order) {
    //         $packager = new Packager();

    //         $weightSum = collect($order['sorted_windows'])->sum('weight');
    //         $heighestItem = collect($order['sorted_windows'])->max('height');

    //         $rack = Rack::where('net_weight', '>=', $weightSum)
    //             ->where('loading_height', '>=', $heighestItem)
    //             ->first();

    //         if (! $rack) {
    //             $rack = Rack::where('loading_height', '>=', ($heighestItem / 2))->first();
    //         }

    //         $height = $heighestItem > $rack->loading_height ? $heighestItem : $rack->loading_height;

    //         $bin = new Bin(
    //             $order['order_id'].'.1',
    //             $rack->loading_length,
    //             $rack->loading_width,
    //             $height,
    //             $rack->net_weight,
    //         );

    //         foreach ($order['sorted_windows'] as $index => $window) {
    //             $item = new Item(
    //                 'window_'.$order['order_id'].'_'.($index + 1),
    //                 $window['length'],
    //                 $window['height'],
    //                 $window['width'],
    //                 $window['weight'],
    //                 false,
    //             );
    //             $packager->addItem($item);
    //             $packager->packItemToBin($bin, $item);
    //         }

    //         $packager->addBin($bin);

    //         $this->handleUnfittedItems($packager, $bin, $order['order_id']);

    //         $orderInfo = Order::where('id', $order['order_id'])->get();

    //         foreach ($packager->getBins() as $packedBin) {
    //             $results[] = [
    //                 'order' => $order['order_id'],
    //                 'info' => $orderInfo,
    //                 'bin' => $packedBin,
    //             ];
    //         }
    //     }

    //     $trailer['matrix'] = array_merge($trailer['matrix'], $results);

    //     $this->moveLastThreeToIndexes($trailer['matrix']);

    //     foreach ($results as $result) {
    //         $trailer['total_weight'] += $result['bin']->getWeight();
    //         $trailer['total_volume'] += $result['bin']->getVolume();
    //     }

    //     dd($trailer);
    //     return [
    //         'trailer' => $trailer,
    //     ];
    // }

    function moveLastThreeToIndexes(array &$matrix)
    {
        $resultsCount = count($matrix);

        if ($resultsCount < 7) {
            return;
        }

        $elementsToMove = array_splice($matrix, -3);

        array_splice($matrix, 3, 0, $elementsToMove);
    }

    private function fillMatrixWithResults(array $results)
    {
        $matrix = [
            [null, null, null],
            [null, null, null],
            [null, null, null],
        ];

        $resultsCount = count($results);

        for ($i = 0; $i < 3; $i++) {
            if ($resultsCount - 3 + $i >= 0) {
                $matrix[1][$i] = $results[$resultsCount - 3 + $i];
            }
        }

        $remainingIndex = 0;
        for ($row = 0; $row < 3; $row++) {
            for ($col = 0; $col < 3; $col++) {
                if ($row === 1) {
                    continue;
                }
                if ($remainingIndex >= $resultsCount - 3) {
                    break;
                }
                $matrix[$row][$col] = $results[$remainingIndex];
                $remainingIndex++;
            }
        }

        return $matrix;
    }
}
