<?php

namespace App\Http\Controllers;

use App\Models\SelectedOrder;
use App\Models\Trailer;
use App\Services\Packing\PackingService;
use Inertia\Inertia;

class PackingController extends Controller
{
    public function __construct(private PackingService $packingService) {}

    public function index(): \Inertia\Response
    {
        $trailerData = $this->packingService->packOrdersIntoTrailer();

        return Inertia::render('Packing', [
            'trailer' => $trailerData['trailer'],
            'vehicle' => Trailer::first(),
            'orderList' => $this->packingService->getSortedItemsForEachOrder(),
        ]);
    }
}
