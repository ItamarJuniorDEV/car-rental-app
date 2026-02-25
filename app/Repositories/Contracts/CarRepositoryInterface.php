<?php

namespace App\Repositories\Contracts;

interface CarRepositoryInterface
{
    public function all();
    public function find(int $id);
    public function findAvailable(): array;
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id): void;
}
