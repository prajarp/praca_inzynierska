<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SelectedOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'order' => [
                'id' => $this->order->id,
                'client_name' => $this->order->client_name,
                'delivery_address' => $this->order->delivery_address,
                'expected_delivery_date' => $this->order->expected_delivery_date,
                'voivodeship' => $this->order->voivodeship,
                'order_items' => OrderItemResource::collection($this->order->orderItems),
            ],
        ];
    }
}
