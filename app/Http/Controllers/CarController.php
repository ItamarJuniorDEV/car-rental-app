<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCarRequest;
use App\Http\Requests\UpdateCarRequest;
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
        if ($request->has('available')) {
            $cars = $this->repository->findAvailable();
            return response()->json($cars, 200);
        }

        $cars = $this->repository->all();
        return response()->json($cars, 200);
    }

    public function store(StoreCarRequest $request)
    {
        $car = $this->repository->create($request->validated());
        return response()->json($car, 201);
    }

    public function show($id)
    {
        $car = $this->repository->find($id);
        return response()->json($car, 200);
    }

    public function update(UpdateCarRequest $request, $id)
    {
        $car = $this->repository->update($id, $request->validated());
        return response()->json($car, 200);
    }

    public function destroy($id)
    {
        $this->repository->delete($id);
        return response()->json(['msg' => 'O veículo foi removido com sucesso!'], 200);
    }
}
