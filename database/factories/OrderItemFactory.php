<?php

namespace Database\Factories;

use App\Models\OrderItem;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'order_id' => Order::factory(), // Tworzy powiązane zamówienie
            'item_type' => $this->faker->randomElement(['window', 'door', 'frame']), // Przykładowe typy
            'weight' => $this->faker->numberBetween(10, 200), // Waga między 10 a 200 kg
            'height' => $this->faker->numberBetween(100, 500), // Wysokość między 100 a 500 cm
            'width' => $this->faker->numberBetween(50, 200),   // Szerokość między 50 a 200 cm
            'length' => $this->faker->numberBetween(50, 300),  // Długość między 50 a 300 cm
        ];
    }
}