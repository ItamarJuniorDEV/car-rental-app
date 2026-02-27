<?php

namespace App\Repositories\Contracts;

use App\Models\Line;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface LineRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): Line;

    public function findByBrand(int $brandId, int $perPage = 15): LengthAwarePaginator;

    public function create(array $data): Line;

    public function update(int $id, array $data): Line;

    public function delete(int $id): void;
}
