<?php

namespace App\Repositories\Eloquent;

use App\Models\Rental;
use App\Repositories\Contracts\RentalRepositoryInterface;

class RentalRepository extends BaseRepository implements RentalRepositoryInterface
{
    public function __construct(Rental $rental)
    {
        $this->model = $rental;
    }

    public function paginate(int $perPage = 15)
    {
        return $this->model->with(['client', 'car.line'])->paginate($perPage);
    }

    public function find(int $id): \Illuminate\Database\Eloquent\Model
    {
        $record = $this->model->with(['client', 'car.line'])->find($id);

        if ($record === null) {
            throw new \App\Exceptions\ResourceNotFoundException();
        }

        return $record;
    }
}
