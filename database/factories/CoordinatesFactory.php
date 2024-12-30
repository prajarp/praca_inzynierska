<?php

namespace Database\Factories;

use App\Models\Coordinates;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class CoordinatesFactory extends Factory
{
    protected $model = Coordinates::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'longitude' => $this->faker->longitude,
            'latitude' => $this->faker->latitude,
            'order_id' => Order::factory(), // Powiązane zamówienie
        ];
    }
}
