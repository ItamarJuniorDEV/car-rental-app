<?php

namespace App\Repositories\Eloquent;

use App\Exceptions\ResourceNotFoundException;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    protected Model $model;

    public function all()
    {
        return $this->model->all();
    }

    public function paginate(int $perPage = 15)
    {
        return $this->model->paginate($perPage);
    }

    public function find(int $id): Model
    {
        $record = $this->model->find($id);

        if ($record === null) {
            throw new ResourceNotFoundException();
        }

        return $record;
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Model
    {
        $record = $this->find($id);
        $record->update($data);

        return $record->fresh();
    }

    public function delete(int $id): void
    {
        $record = $this->find($id);
        $record->delete();
    }
}
