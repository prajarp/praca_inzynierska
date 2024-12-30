<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderSelectionResource;
use App\Http\Resources\SelectedOrderResource;
use App\Models\Order;
use App\Models\SelectedOrder;
use App\Models\Trailer;
use App\Services\OrderSelection\OrderSelectionService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrdersSelectionController extends Controller
{
    public function __construct(readonly OrderSelectionService $orderSelectionSerivce) { }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Inertia\Response
    {
        $query = $this->orderSelectionSerivce->index();

        $query = $this->orderSelectionSerivce->filterByExpectedDeliveryDate(
            $query,
            $request->get('expected_delivery_date')
        );

        $query = $this->orderSelectionSerivce->filterByVoivodeship(
            $query,
            $request->get('voivodeship')
        );

        $orders = $query->paginate(10);

        $trailer = Trailer::first();
        return Inertia::render('OrderSelection', [
            'orders' => fn () => OrderResource::collection($orders),
            'pagination' => fn () => [
            'current_page' => $orders->currentPage(),
            'last_page' => $orders->lastPage(),
            'per_page' => $orders->perPage(),
            'total' => $orders->total(),
            ],
            'trailer' => $trailer,
            'voivodeships' => $this->orderSelectionSerivce->voivodeshipsDropdown(),
            'filtersFromServer' => [
            'expected_delivery_date' => $request->expected_delivery_date,
            'voivodeship' => $request->voivodeship,
            ],
        ]);
    }
}
