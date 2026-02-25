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

    public function findAvailable(): array
    {
        $cars = $this->model->all();
        $available = [];

        foreach ($cars as $car) {
            if ($car->available === true) {
                $available[] = $car;
            }
        }

        return $available;
    }
}
