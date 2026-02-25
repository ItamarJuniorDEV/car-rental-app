<?php

namespace Database\Factories;

use App\Models\Line;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarFactory extends Factory
{
    public function definition()
    {
        $letters = strtoupper(
            $this->faker->randomLetter() .
            $this->faker->randomLetter() .
            $this->faker->randomLetter()
        );
        $plate = $letters . '-' .
            $this->faker->numberBetween(1, 9) .
            strtoupper($this->faker->randomLetter()) .
            $this->faker->numberBetween(10, 99);

        return [
            'line_id'   => Line::factory(),
            'plate'     => $plate,
            'available' => true,
            'km'        => $this->faker->numberBetween(5000, 120000),
        ];
    }
}
