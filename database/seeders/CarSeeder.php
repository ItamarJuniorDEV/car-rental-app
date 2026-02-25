<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Car;
use App\Models\Line;
use Illuminate\Database\Seeder;

class CarSeeder extends Seeder
{
    public function run()
    {
        $catalog = [
            'Toyota' => [
                ['name' => 'Corolla', 'image' => 'corolla.png', 'door_count' => 4, 'seats' => 5, 'air_bag' => true, 'abs' => true],
                ['name' => 'Hilux',   'image' => 'hilux.png',   'door_count' => 4, 'seats' => 5, 'air_bag' => true, 'abs' => true],
            ],
            'Honda' => [
                ['name' => 'Civic', 'image' => 'civic.png', 'door_count' => 4, 'seats' => 5, 'air_bag' => true,  'abs' => true],
                ['name' => 'HR-V',  'image' => 'hrv.png',   'door_count' => 4, 'seats' => 5, 'air_bag' => true,  'abs' => true],
            ],
            'Volkswagen' => [
                ['name' => 'Gol',     'image' => 'gol.png',     'door_count' => 4, 'seats' => 5, 'air_bag' => false, 'abs' => false],
                ['name' => 'T-Cross', 'image' => 'tcross.png',  'door_count' => 4, 'seats' => 5, 'air_bag' => true,  'abs' => true],
            ],
            'Fiat' => [
                ['name' => 'Uno',   'image' => 'uno.png',   'door_count' => 4, 'seats' => 5, 'air_bag' => false, 'abs' => false],
                ['name' => 'Pulse', 'image' => 'pulse.png', 'door_count' => 4, 'seats' => 5, 'air_bag' => true,  'abs' => true],
            ],
        ];

        $plates = [
            'ABC-1D23', 'DEF-2E45', 'GHI-3F67', 'JKL-4G89',
            'MNO-5H01', 'PQR-6I23', 'STU-7J45', 'VWX-8K67',
        ];

        $kms = [12000, 34500, 87200, 5600, 62100, 19800, 45300, 73000];

        $plateIndex = 0;

        foreach ($catalog as $brandName => $lines) {
            $brand = Brand::create(['name' => $brandName, 'image' => strtolower($brandName) . '.png']);

            foreach ($lines as $lineData) {
                $line = Line::create(array_merge(['brand_id' => $brand->id], $lineData));

                Car::create([
                    'line_id'   => $line->id,
                    'plate'     => $plates[$plateIndex],
                    'available' => true,
                    'km'        => $kms[$plateIndex],
                ]);

                $plateIndex++;
            }
        }
    }
}
