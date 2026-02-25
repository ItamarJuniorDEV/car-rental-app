<?php

namespace Database\Factories;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;

class LineFactory extends Factory
{
    public function definition()
    {
        return [
            'brand_id'   => Brand::factory(),
            'name'       => $this->faker->word(),
            'image'      => strtolower($this->faker->word()) . '.png',
            'door_count' => $this->faker->randomElement([2, 4]),
            'seats'      => $this->faker->randomElement([4, 5, 7]),
            'air_bag'    => $this->faker->boolean(80),
            'abs'        => $this->faker->boolean(80),
        ];
    }
}
