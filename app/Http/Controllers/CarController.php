<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCarRequest;
use App\Http\Requests\UpdateCarRequest;
use App\Http\Resources\CarResource;
use App\Repositories\Contracts\CarRepositoryInterface;
use Illuminate\Http\Request;

class CarController extends Controller
{
    private CarRepositoryInterface $repository;

    public function __construct(CarRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        if ($request->has('plate')) {
            return CarResource::collection($this->repository->searchByPlate($request->plate));
        }

        if ($request->has('available')) {
            return CarResource::collection($this->repository->findAvailable());
        }

        return CarResource::collection($this->repository->paginate());
    }

    public function store(StoreCarRequest $request)
    {
        $car = $this->repository->create($request->validated());
        return (new CarResource($car))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        return new CarResource($this->repository->find($id));
    }

    public function update(UpdateCarRequest $request, $id)
    {
        return new CarResource($this->repository->update($id, $request->validated()));
    }

    public function destroy($id)
    {
        $this->repository->delete($id);
        return response()->json(['msg' => 'O veículo foi removido com sucesso!'], 200);
    }
}
