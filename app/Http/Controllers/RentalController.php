<?php

namespace App\Http\Controllers;

use App\Exceptions\CarNotAvailableException;
use App\Http\Requests\StoreRentalRequest;
use App\Http\Requests\UpdateRentalRequest;
use App\Http\Resources\RentalResource;
use App\Models\Rental;
use App\Repositories\Contracts\CarRepositoryInterface;
use App\Repositories\Contracts\RentalRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
        $this->authorize('viewAny', Rental::class);

        return RentalResource::collection($this->repository->paginate());
    }

    public function store(StoreRentalRequest $request)
    {
        $this->authorize('create', Rental::class);

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
        $rental = $this->repository->find($id);
        $this->authorize('view', $rental);

        return new RentalResource($rental);
    }

    public function update(UpdateRentalRequest $request, $id)
    {
        $rental = $this->repository->find($id);
        $this->authorize('update', $rental);

        if ($request->has('final_km') && $request->final_km < $rental->initial_km) {
            throw ValidationException::withMessages([
                'final_km' => ['A quilometragem final não pode ser inferior à inicial.'],
            ]);
        }

        if ($request->has('period_actual_end_date')) {
            $actualEnd = Carbon::parse($request->period_actual_end_date);
            if ($actualEnd->lt($rental->period_start_date)) {
                throw ValidationException::withMessages([
                    'period_actual_end_date' => ['A data de devolução não pode ser anterior à data de início da locação.'],
                ]);
            }
        }

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
        $this->authorize('delete', $rental);

        $this->carRepository->update($rental->car_id, ['available' => true]);
        $this->repository->delete($id);

        return response()->json(['msg' => 'A locação foi removida com sucesso!'], 200);
    }
}
