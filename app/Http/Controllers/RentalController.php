<?php

namespace App\Http\Controllers;

use App\Exceptions\CarNotAvailableException;
use App\Http\Requests\StoreRentalRequest;
use App\Http\Requests\UpdateRentalRequest;
use App\Http\Resources\RentalResource;
use App\Models\Car;
use App\Models\Rental;
use App\Repositories\Contracts\CarRepositoryInterface;
use App\Repositories\Contracts\RentalRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

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

    #[OA\Get(
        path: '/api/rentals',
        summary: 'Listar locações',
        tags: ['Locações'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Lista de locações'),
            new OA\Response(response: 401, description: 'Não autenticado'),
        ]
    )]
    public function index()
    {
        $this->authorize('viewAny', Rental::class);

        return RentalResource::collection($this->repository->paginate());
    }

    #[OA\Post(
        path: '/api/rentals',
        summary: 'Registrar locação',
        tags: ['Locações'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['client_id', 'car_id', 'period_start_date', 'period_expected_end_date', 'daily_rate', 'initial_km'],
                properties: [
                    new OA\Property(property: 'client_id',                 type: 'integer', example: 1),
                    new OA\Property(property: 'car_id',                    type: 'integer', example: 1),
                    new OA\Property(property: 'period_start_date',         type: 'string',  format: 'date', example: '2026-03-01'),
                    new OA\Property(property: 'period_expected_end_date',  type: 'string',  format: 'date', example: '2026-03-07'),
                    new OA\Property(property: 'daily_rate',                type: 'number',  example: 150.00),
                    new OA\Property(property: 'initial_km',                type: 'integer', example: 15000),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Locação criada'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 422, description: 'Veículo indisponível ou dados inválidos'),
        ]
    )]
    public function store(StoreRentalRequest $request)
    {
        $this->authorize('create', Rental::class);

        $rental = DB::transaction(function () use ($request) {
            $car = Car::lockForUpdate()->findOrFail($request->car_id);

            if (!$car->available) {
                throw new CarNotAvailableException();
            }

            $rental = $this->repository->create($request->validated());
            $this->carRepository->update($car->id, ['available' => false]);
            return $rental;
        });

        return (new RentalResource($rental))->response()->setStatusCode(201);
    }

    #[OA\Get(
        path: '/api/rentals/{id}',
        summary: 'Exibir locação',
        tags: ['Locações'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Locação encontrada'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Não encontrada'),
        ]
    )]
    public function show($id)
    {
        $rental = $this->repository->find($id);
        $this->authorize('view', $rental);

        return new RentalResource($rental);
    }

    #[OA\Put(
        path: '/api/rentals/{id}',
        summary: 'Registrar devolução',
        tags: ['Locações'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'period_actual_end_date', type: 'string',  format: 'date', example: '2026-03-08'),
                    new OA\Property(property: 'final_km',              type: 'integer', example: 15700),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Devolução registrada, multa calculada se houver atraso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Não encontrada'),
            new OA\Response(response: 422, description: 'Dados inválidos'),
        ]
    )]
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

    #[OA\Delete(
        path: '/api/rentals/{id}',
        summary: 'Cancelar locação',
        tags: ['Locações'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Locação cancelada, veículo liberado'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Não encontrada'),
        ]
    )]
    public function destroy($id)
    {
        $rental = $this->repository->find($id);
        $this->authorize('delete', $rental);

        $this->carRepository->update($rental->car_id, ['available' => true]);
        $this->repository->delete($id);

        return response()->json(['msg' => 'A locação foi removida com sucesso!'], 200);
    }
}
