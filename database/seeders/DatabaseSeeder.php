<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name'     => 'Admin',
            'email'    => 'admin@locadora.com',
            'password' => bcrypt('senha123'),
            'role'     => 'admin',
        ]);

        $this->call([
            CarSeeder::class,
            ClientSeeder::class,
            RentalSeeder::class,
        ]);
    }
}
