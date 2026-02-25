<?php

namespace App\Http\Controllers;

use App\Exceptions\CarNotAvailableException;
use App\Http\Requests\StoreRentalRequest;
use App\Http\Requests\UpdateRentalRequest;
use App\Http\Resources\RentalResource;
use App\Repositories\Contracts\CarRepositoryInterface;
use App\Repositories\Contracts\RentalRepositoryInterface;
use Illuminate\Support\Facades\DB;

class RentalController extends Controller
{
    private RentalRepositoryInterface $repository;
    private CarRepositoryInterface $carRepository;

    public function __construct(
        RentalRepositoryInterface $repository,
        CarRepositoryInterface $carRepository
    ) {
        $this->repository    = $repository;
        $this->carRepository = $carRepository;
    }

    public function index()
    {
        return RentalResource::collection($this->repository->paginate());
    }

    public function store(StoreRentalRequest $request)
    {
        $car = $this->carRepository->find($request->car_id);

        if (!$car->available) {
            throw new CarNotAvailableException();
        }

        $rental = DB::transaction(function () use ($request, $car) {
            $rental = $this->repository->create($request->validated());
            $this->carRepository->update($car->id, ['available' => false]);
            return $rental;
        });

        return (new RentalResource($rental))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        return new RentalResource($this->repository->find($id));
    }

    public function update(UpdateRentalRequest $request, $id)
    {
        $rental = $this->repository->update($id, $request->validated());

        if ($request->has('period_actual_end_date')) {
            $this->carRepository->update($rental->car_id, [
                'available' => true,
                'km'        => $request->final_km,
            ]);
        }

        return new RentalResource($rental->fresh());
    }

    public function destroy($id)
    {
        $rental = $this->repository->find($id);
        $this->carRepository->update($rental->car_id, ['available' => true]);
        $this->repository->delete($id);

        return response()->json(['msg' => 'A locação foi removida com sucesso!'], 200);
    }
}
