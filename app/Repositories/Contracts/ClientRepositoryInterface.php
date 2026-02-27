<?php

namespace App\Repositories\Contracts;

use App\Models\Client;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ClientRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): Client;

    public function search(string $name): LengthAwarePaginator;

    public function create(array $data): Client;

    public function update(int $id, array $data): Client;

    public function delete(int $id): void;
}
