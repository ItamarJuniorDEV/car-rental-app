<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\Client;
use App\Models\Rental;
use Illuminate\Database\Seeder;

class RentalSeeder extends Seeder
{
    public function run()
    {
        $clients = Client::all();
        $cars = Car::all();

        $completed = [
            [
                'client_id' => $clients[0]->id,
                'car_id' => $cars[0]->id,
                'period_start_date' => '2025-10-05 08:00:00',
                'period_expected_end_date' => '2025-10-08 08:00:00',
                'period_actual_end_date' => '2025-10-08 10:00:00',
                'daily_rate' => 200.00,
                'initial_km' => 12000,
                'final_km' => 12850,
            ],
            [
                'client_id' => $clients[1]->id,
                'car_id' => $cars[1]->id,
                'period_start_date' => '2025-11-12 09:00:00',
                'period_expected_end_date' => '2025-11-15 09:00:00',
                'period_actual_end_date' => '2025-11-17 09:00:00',
                'daily_rate' => 250.00,
                'initial_km' => 34500,
                'final_km' => 35200,
            ],
            [
                'client_id' => $clients[2]->id,
                'car_id' => $cars[2]->id,
                'period_start_date' => '2025-12-01 07:00:00',
                'period_expected_end_date' => '2025-12-05 07:00:00',
                'period_actual_end_date' => '2025-12-04 07:00:00',
                'daily_rate' => 180.00,
                'initial_km' => 87200,
                'final_km' => 87650,
            ],
            [
                'client_id' => $clients[3]->id,
                'car_id' => $cars[3]->id,
                'period_start_date' => '2026-01-10 10:00:00',
                'period_expected_end_date' => '2026-01-14 10:00:00',
                'period_actual_end_date' => '2026-01-14 11:30:00',
                'daily_rate' => 300.00,
                'initial_km' => 5600,
                'final_km' => 6100,
            ],
        ];

        foreach ($completed as $data) {
            Rental::create($data);
        }

        $active = [
            [
                'client_id' => $clients[4]->id,
                'car_id' => $cars[4]->id,
                'period_start_date' => '2026-02-20 08:00:00',
                'period_expected_end_date' => '2026-02-27 08:00:00',
                'daily_rate' => 200.00,
                'initial_km' => 62100,
            ],
            [
                'client_id' => $clients[5]->id,
                'car_id' => $cars[5]->id,
                'period_start_date' => '2026-02-22 09:00:00',
                'period_expected_end_date' => '2026-02-28 09:00:00',
                'daily_rate' => 150.00,
                'initial_km' => 19800,
            ],
        ];

        foreach ($active as $data) {
            Rental::create($data);
            Car::find($data['car_id'])->update(['available' => false]);
        }
    }
}
