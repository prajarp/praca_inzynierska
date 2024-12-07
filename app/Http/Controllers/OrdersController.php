<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Order\OrderService;
use Inertia\Inertia;

class OrdersController extends Controller
{
    public function __construct(readonly OrderService $orderService) {}

    public function index()
    {
        
        return Inertia::render('Orders', [
            'coordinates' => $this->orderService->calculateRoute(),
            // 'secondCoordinates' => $this->orderService->calculateRoute2(),
        ]);
    }
}
