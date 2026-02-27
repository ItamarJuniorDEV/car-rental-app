<?php

namespace App\Repositories\Eloquent;

use App\Exceptions\ResourceNotFoundException;
use App\Models\Brand;
use App\Repositories\Contracts\BrandRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BrandRepository extends BaseRepository implements BrandRepositoryInterface
{
    public function __construct(Brand $brand)
    {
        $this->model = $brand;
    }

    public function find(int $id): Brand
    {
        $record = Brand::find($id);

        if ($record === null) {
            throw new ResourceNotFoundException;
        }

        return $record;
    }

    public function search(string $name): LengthAwarePaginator
    {
        return $this->model->where('name', 'like', "%{$name}%")->paginate(15);
    }

    public function create(array $data): Brand
    {
        return Brand::create($data);
    }

    public function update(int $id, array $data): Brand
    {
        $brand = $this->find($id);
        $brand->update($data);
        $brand->refresh();

        return $brand;
    }
}
