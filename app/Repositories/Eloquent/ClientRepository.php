<?php

namespace App\Repositories\Eloquent;

use App\Models\Client;
use App\Repositories\Contracts\ClientRepositoryInterface;

class ClientRepository extends BaseRepository implements ClientRepositoryInterface
{
    public function __construct(Client $client)
    {
        $this->model = $client;
    }

    public function search(string $name)
    {
        return $this->model->where('name', 'like', "%{$name}%")->paginate(15);
    }
}
