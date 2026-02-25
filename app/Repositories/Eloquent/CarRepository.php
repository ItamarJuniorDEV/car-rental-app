<?php

namespace App\Repositories\Eloquent;

use App\Models\Car;
use App\Repositories\Contracts\CarRepositoryInterface;

class CarRepository extends BaseRepository implements CarRepositoryInterface
{
    public function __construct(Car $car)
    {
        $this->model = $car;
    }

    public function paginate(int $perPage = 15)
    {
        return $this->model->with('line.brand')->paginate($perPage);
    }

    public function find(int $id): \Illuminate\Database\Eloquent\Model
    {
        $record = $this->model->with('line.brand')->find($id);

        if ($record === null) {
            throw new \App\Exceptions\ResourceNotFoundException();
        }

        return $record;
    }

    public function findAvailable(int $perPage = 15)
    {
        return $this->model->with('line.brand')->where('available', true)->paginate($perPage);
    }

    public function searchByPlate(string $plate)
    {
        return $this->model->with('line.brand')->where('plate', 'like', "%{$plate}%")->paginate(15);
    }
}
