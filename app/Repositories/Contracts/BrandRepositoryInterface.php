<?php

namespace App\Repositories\Contracts;

use App\Models\Brand;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BrandRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): Brand;

    public function search(string $name): LengthAwarePaginator;

    public function create(array $data): Brand;

    public function update(int $id, array $data): Brand;

    public function delete(int $id): void;
}
