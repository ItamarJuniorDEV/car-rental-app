<?php

namespace App\Repositories\Contracts;

interface LineRepositoryInterface
{
    public function all();
    public function paginate(int $perPage = 15);
    public function find(int $id);
    public function findByBrand(int $brandId, int $perPage = 15);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id): void;
}
