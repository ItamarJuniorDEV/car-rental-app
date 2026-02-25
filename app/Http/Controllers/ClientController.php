<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Resources\ClientResource;
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
        if ($request->has('name')) {
            return ClientResource::collection($this->repository->search($request->name));
        }

        return ClientResource::collection($this->repository->paginate());
    }

    public function store(StoreClientRequest $request)
    {
        $client = $this->repository->create($request->validated());
        return (new ClientResource($client))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        return new ClientResource($this->repository->find($id));
    }

    public function update(UpdateClientRequest $request, $id)
    {
        return new ClientResource($this->repository->update($id, $request->validated()));
    }

    public function destroy($id)
    {
        $this->repository->delete($id);
        return response()->json(['msg' => 'O cliente foi removido com sucesso!'], 200);
    }
}
