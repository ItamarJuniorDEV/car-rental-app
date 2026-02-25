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

    public function findByBrand(int $brandId): array
    {
        $lines = $this->model->all();
        $result = [];

        foreach ($lines as $line) {
            if ((int) $line->brand_id === $brandId) {
                $result[] = $line;
            }
        }

        return $result;
    }
}
