<?php

namespace App\Repositories\Eloquent;

use App\Models\Brand;
use App\Repositories\Contracts\BrandRepositoryInterface;

class BrandRepository extends BaseRepository implements BrandRepositoryInterface
{
    public function __construct(Brand $brand)
    {
        $this->model = $brand;
    }

    public function search(string $name)
    {
        return $this->model->where('name', 'like', "%{$name}%")->paginate(15);
    }
}
