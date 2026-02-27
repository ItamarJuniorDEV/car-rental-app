<?php

namespace App\Repositories\Contracts;

use App\Models\Rental;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface RentalRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): Rental;

    public function create(array $data): Rental;

    public function update(int $id, array $data): Rental;

    public function delete(int $id): void;
}
