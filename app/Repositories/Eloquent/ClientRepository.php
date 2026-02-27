<?php

namespace App\Repositories\Eloquent;

use App\Exceptions\ResourceNotFoundException;
use App\Models\Client;
use App\Repositories\Contracts\ClientRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ClientRepository extends BaseRepository implements ClientRepositoryInterface
{
    public function __construct(Client $client)
    {
        $this->model = $client;
    }

    public function find(int $id): Client
    {
        $record = Client::find($id);

        if ($record === null) {
            throw new ResourceNotFoundException;
        }

        return $record;
    }

    public function search(string $name): LengthAwarePaginator
    {
        return $this->model->where('name', 'like', "%{$name}%")->paginate(15);
    }

    public function create(array $data): Client
    {
        return Client::create($data);
    }

    public function update(int $id, array $data): Client
    {
        $client = $this->find($id);
        $client->update($data);
        $client->refresh();

        return $client;
    }
}
