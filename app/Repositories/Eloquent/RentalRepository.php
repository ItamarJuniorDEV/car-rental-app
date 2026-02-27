<?php

namespace App\Repositories\Eloquent;

use App\Exceptions\ResourceNotFoundException;
use App\Models\Rental;
use App\Repositories\Contracts\RentalRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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

    public function find(int $id): Rental
    {
        $record = Rental::with(['client', 'car.line'])->find($id);

        if ($record === null) {
            throw new ResourceNotFoundException;
        }

        return $record;
    }

    public function create(array $data): Rental
    {
        return Rental::create($data);
    }

    public function update(int $id, array $data): Rental
    {
        $rental = $this->find($id);
        $rental->update($data);
        $rental->refresh();

        return $rental;
    }
}
