<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name'      => 'Admin',
            'email'     => 'admin@locadora.com',
            'password'  => bcrypt('senha123'),
            'api_token' => Str::random(80),
        ]);

        $this->call([
            CarSeeder::class,
            ClientSeeder::class,
            RentalSeeder::class,
        ]);
    }
}
