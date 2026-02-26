<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLineRequest;
use App\Http\Requests\UpdateLineRequest;
use App\Http\Resources\LineResource;
use App\Models\Line;
use App\Repositories\Contracts\LineRepositoryInterface;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class LineController extends Controller
{
    private LineRepositoryInterface $repository;

    public function __construct(LineRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[OA\Get(
        path: '/api/lines',
        summary: 'Listar linhas',
        tags: ['Linhas'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'brand_id', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista de linhas'),
            new OA\Response(response: 401, description: 'Não autenticado'),
        ]
    )]
    public function index(Request $request)
    {
        $this->authorize('viewAny', Line::class);

        if ($request->has('brand_id')) {
            return LineResource::collection($this->repository->findByBrand((int) $request->brand_id));
        }

        return LineResource::collection($this->repository->paginate());
    }

    #[OA\Post(
        path: '/api/lines',
        summary: 'Cadastrar linha',
        tags: ['Linhas'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['brand_id', 'name', 'image', 'door_count', 'seats', 'air_bag', 'abs'],
                properties: [
                    new OA\Property(property: 'brand_id',   type: 'integer', example: 1),
                    new OA\Property(property: 'name',       type: 'string',  maxLength: 30,  example: 'Corolla'),
                    new OA\Property(property: 'image',      type: 'string',  maxLength: 100, example: 'corolla.png'),
                    new OA\Property(property: 'door_count', type: 'integer', minimum: 1,     example: 4),
                    new OA\Property(property: 'seats',      type: 'integer', minimum: 1,     example: 5),
                    new OA\Property(property: 'air_bag',    type: 'boolean', example: true),
                    new OA\Property(property: 'abs',        type: 'boolean', example: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Linha criada'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 422, description: 'Dados inválidos'),
        ]
    )]
    public function store(StoreLineRequest $request)
    {
        $this->authorize('create', Line::class);

        $line = $this->repository->create($request->validated());
        return (new LineResource($line))->response()->setStatusCode(201);
    }

    #[OA\Get(
        path: '/api/lines/{id}',
        summary: 'Exibir linha',
        tags: ['Linhas'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Linha encontrada'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Não encontrada'),
        ]
    )]
    public function show($id)
    {
        $line = $this->repository->find($id);
        $this->authorize('view', $line);

        return new LineResource($line);
    }

    #[OA\Put(
        path: '/api/lines/{id}',
        summary: 'Atualizar linha',
        tags: ['Linhas'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'brand_id',   type: 'integer'),
                    new OA\Property(property: 'name',       type: 'string',  maxLength: 30),
                    new OA\Property(property: 'image',      type: 'string',  maxLength: 100),
                    new OA\Property(property: 'door_count', type: 'integer', minimum: 1),
                    new OA\Property(property: 'seats',      type: 'integer', minimum: 1),
                    new OA\Property(property: 'air_bag',    type: 'boolean'),
                    new OA\Property(property: 'abs',        type: 'boolean'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Linha atualizada'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Não encontrada'),
            new OA\Response(response: 422, description: 'Dados inválidos'),
        ]
    )]
    public function update(UpdateLineRequest $request, $id)
    {
        $line = $this->repository->find($id);
        $this->authorize('update', $line);

        return new LineResource($this->repository->update($id, $request->validated()));
    }

    #[OA\Delete(
        path: '/api/lines/{id}',
        summary: 'Remover linha',
        tags: ['Linhas'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Linha removida'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Não encontrada'),
        ]
    )]
    public function destroy($id)
    {
        $line = $this->repository->find($id);
        $this->authorize('delete', $line);

        $this->repository->delete($id);
        return response()->json(['msg' => 'A linha foi removida com sucesso!'], 200);
    }
}
