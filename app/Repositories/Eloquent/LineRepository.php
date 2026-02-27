<?php

namespace App\Repositories\Eloquent;

use App\Exceptions\ResourceNotFoundException;
use App\Models\Line;
use App\Repositories\Contracts\LineRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LineRepository extends BaseRepository implements LineRepositoryInterface
{
    public function __construct(Line $line)
    {
        $this->model = $line;
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with('brand')->paginate($perPage);
    }

    public function find(int $id): Line
    {
        $record = Line::with('brand')->find($id);

        if ($record === null) {
            throw new ResourceNotFoundException;
        }

        return $record;
    }

    public function findByBrand(int $brandId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with('brand')->where('brand_id', $brandId)->paginate($perPage);
    }

    public function create(array $data): Line
    {
        return Line::create($data);
    }

    public function update(int $id, array $data): Line
    {
        $line = $this->find($id);
        $line->update($data);
        $line->refresh();

        return $line;
    }
}
