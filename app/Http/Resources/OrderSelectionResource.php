<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderSelectionResource extends JsonResource
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
            'client_name' => $this->client_name,
            'delivery_address' => $this->delivery_address,
            'voivodeship' => $this->voivodeship,
            'expected_delivery_date' => $this->expected_delivery_date,
            'window_quantity' => $this->window_quantity,
            'other_elements_quantity' => $this->other_elements_quantity,
            'windows_weight' => $this->windows_weight,
            'total_weight' => $this->total_weight,
            'window_area' => $this->window_area,
            'window_dimensions' => $this->window_dimensions,
        ];
    }
}
