<?php

namespace App\Repositories\Eloquent;

use App\Exceptions\ResourceNotFoundException;
use App\Models\Car;
use App\Repositories\Contracts\CarRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CarRepository extends BaseRepository implements CarRepositoryInterface
{
    public function __construct(Car $car)
    {
        $this->model = $car;
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with('line.brand')->paginate($perPage);
    }

    public function find(int $id): Car
    {
        $record = Car::with('line.brand')->find($id);

        if ($record === null) {
            throw new ResourceNotFoundException;
        }

        return $record;
    }

    public function findAvailable(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with('line.brand')->where('available', true)->paginate($perPage);
    }

    public function searchByPlate(string $plate): LengthAwarePaginator
    {
        return $this->model->with('line.brand')->where('plate', 'like', "%{$plate}%")->paginate(15);
    }

    public function create(array $data): Car
    {
        return Car::create($data);
    }

    public function update(int $id, array $data): Car
    {
        $car = $this->find($id);
        $car->update($data);
        $car->refresh();

        return $car;
    }
}
