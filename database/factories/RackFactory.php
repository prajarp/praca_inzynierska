<?php

namespace Database\Factories;

use App\Models\Rack;
use Illuminate\Database\Eloquent\Factories\Factory;

class RackFactory extends Factory
{
    protected $model = Rack::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'rack_type' => $this->faker->randomElement(['standard', 'heavy-duty', 'lightweight']), // Przykładowe typy stojaków
            'outer_height' => $this->faker->numberBetween(100, 300), // Zewnętrzna wysokość w cm
            'outer_width' => $this->faker->numberBetween(100, 200), // Zewnętrzna szerokość w cm
            'outer_length' => $this->faker->numberBetween(100, 400), // Zewnętrzna długość w cm
            'loading_height' => $this->faker->numberBetween(80, 250), // Wysokość załadunku w cm
            'loading_width' => $this->faker->numberBetween(80, 200), // Szerokość załadunku w cm
            'loading_length' => $this->faker->numberBetween(100, 350), // Długość załadunku w cm
            'net_weight' => $this->faker->numberBetween(500, 2000), // Masa netto w kg
        ];
    }
}
