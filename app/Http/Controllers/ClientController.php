<?php

namespace App\Http\Controllers;

use App\Exceptions\ActiveRentalsException;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Repositories\Contracts\ClientRepositoryInterface;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    private ClientRepositoryInterface $repository;

    public function __construct(ClientRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Client::class);

        if ($request->has('name')) {
            return ClientResource::collection($this->repository->search($request->name));
        }

        return ClientResource::collection($this->repository->paginate());
    }

    public function store(StoreClientRequest $request)
    {
        $this->authorize('create', Client::class);

        $client = $this->repository->create($request->validated());
        return (new ClientResource($client))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $client = $this->repository->find($id);
        $this->authorize('view', $client);

        return new ClientResource($client);
    }

    public function update(UpdateClientRequest $request, $id)
    {
        $client = $this->repository->find($id);
        $this->authorize('update', $client);

        return new ClientResource($this->repository->update($id, $request->validated()));
    }

    public function destroy($id)
    {
        $client = $this->repository->find($id);
        $this->authorize('delete', $client);

        if ($client->rentals()->whereNull('period_actual_end_date')->exists()) {
            throw new ActiveRentalsException('Não é possível remover um cliente com locação ativa.');
        }

        $this->repository->delete($id);
        return response()->json(['msg' => 'O cliente foi removido com sucesso!'], 200);
    }
}
