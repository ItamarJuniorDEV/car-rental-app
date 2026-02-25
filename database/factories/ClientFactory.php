<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition()
    {
        $cpf = sprintf(
            '%03d.%03d.%03d-%02d',
            $this->faker->numberBetween(0, 999),
            $this->faker->numberBetween(0, 999),
            $this->faker->numberBetween(0, 999),
            $this->faker->numberBetween(0, 99)
        );

        return [
            'name'  => $this->faker->name(),
            'cpf'   => $cpf,
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->numerify('(##) #####-####'),
        ];
    }
}
