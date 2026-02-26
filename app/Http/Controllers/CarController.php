<?php

namespace App\Http\Controllers;

use App\Exceptions\ActiveRentalsException;
use App\Http\Requests\StoreCarRequest;
use App\Http\Requests\UpdateCarRequest;
use App\Http\Resources\CarResource;
use App\Models\Car;
use App\Repositories\Contracts\CarRepositoryInterface;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CarController extends Controller
{
    private CarRepositoryInterface $repository;

    public function __construct(CarRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[OA\Get(
        path: '/api/cars',
        summary: 'Listar veículos',
        tags: ['Veículos'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'plate', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'available', in: 'query', required: false, schema: new OA\Schema(type: 'boolean')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista de veículos'),
            new OA\Response(response: 401, description: 'Não autenticado'),
        ]
    )]
    public function index(Request $request)
    {
        $this->authorize('viewAny', Car::class);

        if ($request->has('plate')) {
            return CarResource::collection($this->repository->searchByPlate($request->plate));
        }

        if ($request->has('available') && $request->boolean('available')) {
            return CarResource::collection($this->repository->findAvailable());
        }

        return CarResource::collection($this->repository->paginate());
    }

    #[OA\Post(
        path: '/api/cars',
        summary: 'Cadastrar veículo',
        tags: ['Veículos'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['line_id', 'plate', 'available', 'km'],
                properties: [
                    new OA\Property(property: 'line_id', type: 'integer', example: 1),
                    new OA\Property(property: 'plate', type: 'string', maxLength: 10, example: 'ABC-1D23'),
                    new OA\Property(property: 'available', type: 'boolean', example: true),
                    new OA\Property(property: 'km', type: 'integer', minimum: 0, example: 15000),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Veículo criado'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 422, description: 'Dados inválidos'),
        ]
    )]
    public function store(StoreCarRequest $request)
    {
        $this->authorize('create', Car::class);

        $car = $this->repository->create($request->validated());

        return (new CarResource($car))->response()->setStatusCode(201);
    }

    #[OA\Get(
        path: '/api/cars/{id}',
        summary: 'Exibir veículo',
        tags: ['Veículos'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Veículo encontrado'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Não encontrado'),
        ]
    )]
    public function show($id)
    {
        $car = $this->repository->find($id);
        $this->authorize('view', $car);

        return new CarResource($car);
    }

    #[OA\Put(
        path: '/api/cars/{id}',
        summary: 'Atualizar veículo',
        tags: ['Veículos'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'line_id', type: 'integer'),
                    new OA\Property(property: 'plate', type: 'string', maxLength: 10),
                    new OA\Property(property: 'available', type: 'boolean'),
                    new OA\Property(property: 'km', type: 'integer', minimum: 0),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Veículo atualizado'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Não encontrado'),
            new OA\Response(response: 422, description: 'Dados inválidos'),
        ]
    )]
    public function update(UpdateCarRequest $request, $id)
    {
        $car = $this->repository->find($id);
        $this->authorize('update', $car);

        return new CarResource($this->repository->update($id, $request->validated()));
    }

    #[OA\Delete(
        path: '/api/cars/{id}',
        summary: 'Remover veículo',
        tags: ['Veículos'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Veículo removido'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Não encontrado'),
            new OA\Response(response: 422, description: 'Locação ativa — remoção bloqueada'),
        ]
    )]
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
