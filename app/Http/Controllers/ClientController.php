<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Repositories\Contracts\ClientRepositoryInterface;

class ClientController extends Controller
{
    private ClientRepositoryInterface $repository;

    public function __construct(ClientRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        $clients = $this->repository->all();
        return response()->json($clients, 200);
    }

    public function store(StoreClientRequest $request)
    {
        $client = $this->repository->create($request->validated());
        return response()->json($client, 201);
    }

    public function show($id)
    {
        $client = $this->repository->find($id);
        return response()->json($client, 200);
    }

    public function update(UpdateClientRequest $request, $id)
    {
        $client = $this->repository->update($id, $request->validated());
        return response()->json($client, 200);
    }

    public function destroy($id)
    {
        $this->repository->delete($id);
        return response()->json(['msg' => 'O cliente foi removido com sucesso!'], 200);
    }
}
