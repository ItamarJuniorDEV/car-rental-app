<?php

namespace App\Repositories\Eloquent;

use App\Models\Line;
use App\Repositories\Contracts\LineRepositoryInterface;

class LineRepository extends BaseRepository implements LineRepositoryInterface
{
    public function __construct(Line $line)
    {
        $this->model = $line;
    }

    public function paginate(int $perPage = 15)
    {
        return $this->model->with('brand')->paginate($perPage);
    }

    public function find(int $id): \Illuminate\Database\Eloquent\Model
    {
        $record = $this->model->with('brand')->find($id);

        if ($record === null) {
            throw new \App\Exceptions\ResourceNotFoundException();
        }

        return $record;
    }

    public function findByBrand(int $brandId, int $perPage = 15)
    {
        return $this->model->with('brand')->where('brand_id', $brandId)->paginate($perPage);
    }
}
