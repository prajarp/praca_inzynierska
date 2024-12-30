<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    public function definition()
    {
        return [
            'client_name' => $this->faker->name,
            'delivery_address' => $this->faker->address,
            'voivodeship' => $this->faker->state,
            'expected_delivery_date' => $this->faker->dateTimeBetween('+1 week', '+1 month'),
        ];
    }
}
