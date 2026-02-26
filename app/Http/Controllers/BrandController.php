<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use App\Repositories\Contracts\BrandRepositoryInterface;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class BrandController extends Controller
{
    private BrandRepositoryInterface $repository;

    public function __construct(BrandRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[OA\Get(
        path: '/api/brands',
        summary: 'Listar marcas',
        tags: ['Marcas'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'name', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista de marcas'),
            new OA\Response(response: 401, description: 'Não autenticado'),
        ]
    )]
    public function index(Request $request)
    {
        $this->authorize('viewAny', Brand::class);

        if ($request->has('name')) {
            return BrandResource::collection($this->repository->search($request->name));
        }

        return BrandResource::collection($this->repository->paginate());
    }

    #[OA\Post(
        path: '/api/brands',
        summary: 'Cadastrar marca',
        tags: ['Marcas'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'image'],
                properties: [
                    new OA\Property(property: 'name',  type: 'string', maxLength: 30,  example: 'Toyota'),
                    new OA\Property(property: 'image', type: 'string', maxLength: 100, example: 'toyota.png'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Marca criada'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 422, description: 'Dados inválidos'),
        ]
    )]
    public function store(StoreBrandRequest $request)
    {
        $this->authorize('create', Brand::class);

        $brand = $this->repository->create($request->validated());
        return (new BrandResource($brand))->response()->setStatusCode(201);
    }

    #[OA\Get(
        path: '/api/brands/{id}',
        summary: 'Exibir marca',
        tags: ['Marcas'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Marca encontrada'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Não encontrada'),
        ]
    )]
    public function show($id)
    {
        $brand = $this->repository->find($id);
        $this->authorize('view', $brand);

        return new BrandResource($brand);
    }

    #[OA\Put(
        path: '/api/brands/{id}',
        summary: 'Atualizar marca',
        tags: ['Marcas'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name',  type: 'string', maxLength: 30),
                    new OA\Property(property: 'image', type: 'string', maxLength: 100),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Marca atualizada'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Não encontrada'),
            new OA\Response(response: 422, description: 'Dados inválidos'),
        ]
    )]
    public function update(UpdateBrandRequest $request, $id)
    {
        $brand = $this->repository->find($id);
        $this->authorize('update', $brand);

        return new BrandResource($this->repository->update($id, $request->validated()));
    }

    #[OA\Delete(
        path: '/api/brands/{id}',
        summary: 'Remover marca',
        tags: ['Marcas'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Marca removida'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Não encontrada'),
        ]
    )]
    public function destroy($id)
    {
        $brand = $this->repository->find($id);
        $this->authorize('delete', $brand);

        $this->repository->delete($id);
        return response()->json(['msg' => 'A marca foi removida com sucesso!'], 200);
    }
}
