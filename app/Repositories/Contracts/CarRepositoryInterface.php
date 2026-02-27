<?php

namespace App\Repositories\Contracts;

use App\Models\Car;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CarRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): Car;

    public function findAvailable(int $perPage = 15): LengthAwarePaginator;

    public function searchByPlate(string $plate): LengthAwarePaginator;

    public function create(array $data): Car;

    public function update(int $id, array $data): Car;

    public function delete(int $id): void;
}
