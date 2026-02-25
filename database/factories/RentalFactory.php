<?php

namespace Database\Factories;

use App\Models\Car;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class RentalFactory extends Factory
{
    public function definition()
    {
        $startDate   = $this->faker->dateTimeBetween('-6 months', '-2 months');
        $expectedEnd = Carbon::instance($startDate)->addDays($this->faker->numberBetween(2, 7));
        $initialKm   = $this->faker->numberBetween(5000, 100000);

        return [
            'client_id'                => Client::factory(),
            'car_id'                   => Car::factory(),
            'period_start_date'        => $startDate,
            'period_expected_end_date' => $expectedEnd,
            'daily_rate'               => $this->faker->randomElement([150.00, 180.00, 200.00, 250.00, 300.00]),
            'initial_km'               => $initialKm,
            'period_actual_end_date'   => null,
            'final_km'                 => null,
        ];
    }
}
