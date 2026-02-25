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
}
