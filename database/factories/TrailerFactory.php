<?php

namespace Database\Factories;

use App\Models\Trailer;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrailerFactory extends Factory
{
    protected $model = Trailer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'type' => $this->faker->randomElement(['flatbed', 'box', 'tanker']), // Przykładowe typy naczep
            'height' => $this->faker->numberBetween(250, 400), // Wysokość w cm
            'width' => $this->faker->numberBetween(200, 250), // Szerokość w cm
            'length' => $this->faker->numberBetween(800, 1500), // Długość w cm
            'max_weight' => $this->faker->numberBetween(10000, 40000), // Maksymalny ciężar w kg
            'total_height' => $this->faker->numberBetween(300, 450), // Całkowita wysokość w cm
            'total_length' => $this->faker->numberBetween(850, 1600), // Całkowita długość w cm
            'axle_weight' => $this->faker->numberBetween(5000, 15000), // Obciążenie osi w kg
            'total_weight' => $this->faker->numberBetween(8000, 35000), // Całkowita waga w kg
        ];
    }
}
