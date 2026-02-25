<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run()
    {
        $clients = [
            ['name' => 'Maria Oliveira',    'cpf' => '123.456.789-09', 'email' => 'maria.oliveira@gmail.com',    'phone' => '(51) 99123-4567'],
            ['name' => 'Carlos Mendes',     'cpf' => '234.567.890-10', 'email' => 'carlos.mendes@hotmail.com',   'phone' => '(11) 98765-3210'],
            ['name' => 'Ana Paula Souza',   'cpf' => '345.678.901-21', 'email' => 'anapaula.souza@gmail.com',    'phone' => '(21) 97654-8901'],
            ['name' => 'Roberto Lima',      'cpf' => '456.789.012-32', 'email' => 'roberto.lima@outlook.com',    'phone' => '(41) 96543-7890'],
            ['name' => 'Fernanda Castro',   'cpf' => '567.890.123-43', 'email' => 'fernanda.castro@gmail.com',   'phone' => '(31) 95432-6789'],
            ['name' => 'Lucas Pereira',     'cpf' => '678.901.234-54', 'email' => 'lucas.pereira@yahoo.com.br',  'phone' => '(85) 94321-5678'],
            ['name' => 'Juliana Rocha',     'cpf' => '789.012.345-65', 'email' => 'juliana.rocha@gmail.com',     'phone' => '(71) 93210-4567'],
            ['name' => 'Paulo Ferreira',    'cpf' => '890.123.456-76', 'email' => 'paulo.ferreira@hotmail.com',  'phone' => '(62) 92109-3456'],
            ['name' => 'Camila Barbosa',    'cpf' => '901.234.567-87', 'email' => 'camila.barbosa@gmail.com',    'phone' => '(47) 91098-2345'],
            ['name' => 'Rafael Goncalves',  'cpf' => '012.345.678-98', 'email' => 'rafael.goncalves@gmail.com',  'phone' => '(48) 90987-1234'],
        ];

        foreach ($clients as $data) {
            Client::create($data);
        }
    }
}
