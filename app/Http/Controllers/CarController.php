<?php

namespace App\Http\Controllers;

use App\Exceptions\ActiveRentalsException;
use App\Http\Requests\StoreCarRequest;
use App\Http\Requests\UpdateCarRequest;
use App\Http\Resources\CarResource;
use App\Models\Car;
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
        $this->authorize('viewAny', Car::class);

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
        $this->authorize('create', Car::class);

        $car = $this->repository->create($request->validated());
        return (new CarResource($car))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $car = $this->repository->find($id);
        $this->authorize('view', $car);

        return new CarResource($car);
    }

    public function update(UpdateCarRequest $request, $id)
    {
        $car = $this->repository->find($id);
        $this->authorize('update', $car);

        return new CarResource($this->repository->update($id, $request->validated()));
    }

    public function destroy($id)
    {
        $car = $this->repository->find($id);
        $this->authorize('delete', $car);

        if ($car->rentals()->whereNull('period_actual_end_date')->exists()) {
            throw new ActiveRentalsException('Não é possível remover um veículo com locação ativa.');
        }

        $this->repository->delete($id);
        return response()->json(['msg' => 'O veículo foi removido com sucesso!'], 200);
    }
}
