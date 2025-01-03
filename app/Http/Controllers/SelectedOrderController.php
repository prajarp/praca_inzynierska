<?php

namespace App\Http\Controllers;

use App\Http\Requests\SelectedOrders\StoreSelectedOrdersRequest;
use App\Http\Resources\SelectedOrderResource;
use App\Models\SelectedOrder;
use App\Models\Trailer;
use App\Services\SelectedOrders\SelectedOrdersService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SelectedOrderController extends Controller
{
    public function __construct(readonly SelectedOrdersService $selectedOrdersService) {}

    public function index(): \Inertia\Response
    {
        return Inertia::render('OrdersSummary', [
            'selectedOrders' => $this->selectedOrdersService->calculateRoute(),
            'vehicle' => Trailer::first(),
        ]);
    }

    public function store(StoreSelectedOrdersRequest $request): RedirectResponse
    {
        $this->selectedOrdersService->store($request);

        return redirect()->route('orders.selected.index');
    }
}
