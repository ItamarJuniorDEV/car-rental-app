<?php

namespace App\Repositories\Contracts;

interface CarRepositoryInterface
{
    public function all();
    public function paginate(int $perPage = 15);
    public function find(int $id);
    public function findAvailable(int $perPage = 15);
    public function searchByPlate(string $plate);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id): void;
}
