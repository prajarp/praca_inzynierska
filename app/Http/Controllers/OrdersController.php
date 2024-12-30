<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Trailer;
use App\Services\Order\OrderService;
use Inertia\Inertia;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function __construct(readonly OrderService $orderService) {}

    public function index(): \Inertia\Response
    {
        return Inertia::render('Orders', [
            'coordinates' => $this->orderService->getCoordinatesWithOrders(),
            'vehicle' => Trailer::first(),
        ]);
    }
}
