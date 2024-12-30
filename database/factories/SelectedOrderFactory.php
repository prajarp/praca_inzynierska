<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\SelectedOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SelectedOrder>
 */
class SelectedOrderFactory extends Factory
{
    protected $model = SelectedOrder::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'order_id' => Order::factory(), // Tworzy powiązane zamówienie
        ];
    }
}
