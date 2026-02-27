<?php

namespace App\Repositories\Eloquent;

use App\Exceptions\ResourceNotFoundException;
use App\Models\Rental;
use App\Repositories\Contracts\RentalRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class RentalRepository extends BaseRepository implements RentalRepositoryInterface
{
    public function __construct(Rental $rental)
    {
        $this->model = $rental;
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['client', 'car.line'])->paginate($perPage);
    }

    public function find(int $id): Model
    {
        $record = $this->model->with(['client', 'car.line'])->find($id);

        if ($record === null) {
            throw new ResourceNotFoundException;
        }

        return $record;
    }
}
