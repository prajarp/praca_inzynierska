<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderSelectionResource;
use App\Http\Resources\SelectedOrderResource;
use App\Models\Order;
use App\Models\SelectedOrder;
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

        return Inertia::render('OrderSelection', [
            'orders' => fn () => OrderSelectionResource::collection($orders),
            'selectedList' => fn () => SelectedOrderResource::collection($this->orderSelectionSerivce->getSelectedList()),
            'pagination' => fn () => [
            'current_page' => $orders->currentPage(),
            'last_page' => $orders->lastPage(),
            'per_page' => $orders->perPage(),
            'total' => $orders->total(),
            ],
            'voivodeships' => $this->orderSelectionSerivce->voivodeshipsDropdown(),
            'filtersFromServer' => [
            'expected_delivery_date' => $request->expected_delivery_date,
            'voivodeship' => $request->voivodeship,
            ],
        ]);
    }

    // public function storeSelected(Request $request)
    // {
    //     // Przechowaj dane w sesji
    //     $request->session()->put('selected_orders', $request->selected);

    //     // Przekieruj do innego widoku
    //     return redirect()->route('orders.summary');
    // }

    // public function summary(Request $request)
    // {
    //     // Pobierz dane z sesji
    //     $selectedOrders = $request->session()->get('selected_orders', []);

    //     return Inertia::render('OrderSummary', [
    //         'selectedOrders' => $selectedOrders,
    //     ]);
    // }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
