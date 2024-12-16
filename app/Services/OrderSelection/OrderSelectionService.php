<?php

namespace App\Services\OrderSelection;

use App\Models\Order;
use App\Models\SelectedOrder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class OrderSelectionService
{
    public function index()
    {
        return Order::query()->with('orderItems', 'coordinates');
    }

    public function voivodeshipsDropdown()
    {
        return Order::query()
            ->select('voivodeship')
            ->distinct()
            ->pluck('voivodeship')
            ->sort()
            ->values();
    }

    public function filterByExpectedDeliveryDate($query, ?string $direction)
    {
        if (in_array($direction, ['asc', 'desc'])) {
            $query->orderBy('expected_delivery_date', $direction);
        }
        return $query;
    }

    public function filterByVoivodeship($query, ?string $voivodeship)
    {
        if(! empty($voivodeship)) {
            $query->where('voivodeship', $voivodeship);
        }
        return $query;
    }

    public function getSelectedList()
    {
        return SelectedOrder::query()->with('order')->get();
    }
}