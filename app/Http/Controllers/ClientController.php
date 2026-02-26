<?php

namespace App\Http\Controllers;

use App\Exceptions\ActiveRentalsException;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Repositories\Contracts\ClientRepositoryInterface;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ClientController extends Controller
{
    private ClientRepositoryInterface $repository;

    public function __construct(ClientRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[OA\Get(
        path: '/api/clients',
        summary: 'Listar clientes',
        tags: ['Clientes'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'name', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista de clientes'),
            new OA\Response(response: 401, description: 'Não autenticado'),
        ]
    )]
    public function index(Request $request)
    {
        $this->authorize('viewAny', Client::class);

        if ($request->has('name')) {
            return ClientResource::collection($this->repository->search($request->name));
        }

        return ClientResource::collection($this->repository->paginate());
    }

    #[OA\Post(
        path: '/api/clients',
        summary: 'Cadastrar cliente',
        tags: ['Clientes'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'cpf', 'email', 'phone'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 100, example: 'Maria Souza'),
                    new OA\Property(property: 'cpf', type: 'string', example: '123.456.789-00'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'maria@exemplo.com'),
                    new OA\Property(property: 'phone', type: 'string', maxLength: 20, example: '(11) 99999-0000'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Cliente criado'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 422, description: 'Dados inválidos'),
        ]
    )]
    public function store(StoreClientRequest $request)
    {
        $this->authorize('create', Client::class);

        $client = $this->repository->create($request->validated());

        return (new ClientResource($client))->response()->setStatusCode(201);
    }

    #[OA\Get(
        path: '/api/clients/{id}',
        summary: 'Exibir cliente',
        tags: ['Clientes'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Cliente encontrado'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Não encontrado'),
        ]
    )]
    public function show($id)
    {
        $client = $this->repository->find($id);
        $this->authorize('view', $client);

        return new ClientResource($client);
    }

    #[OA\Put(
        path: '/api/clients/{id}',
        summary: 'Atualizar cliente',
        tags: ['Clientes'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 100),
                    new OA\Property(property: 'cpf', type: 'string'),
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'phone', type: 'string', maxLength: 20),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Cliente atualizado'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Não encontrado'),
            new OA\Response(response: 422, description: 'Dados inválidos'),
        ]
    )]
    public function update(UpdateClientRequest $request, $id)
    {
        $client = $this->repository->find($id);
        $this->authorize('update', $client);

        return new ClientResource($this->repository->update($id, $request->validated()));
    }

    #[OA\Delete(
        path: '/api/clients/{id}',
        summary: 'Remover cliente',
        tags: ['Clientes'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Cliente removido'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Não encontrado'),
            new OA\Response(response: 422, description: 'Locação ativa — remoção bloqueada'),
        ]
    )]
    public function destroy($id)
    {
        $client = $this->repository->find($id);
        $this->authorize('delete', $client);

        if ($client->rentals()->whereNull('period_actual_end_date')->exists()) {
            throw new ActiveRentalsException('Não é possível remover um cliente com locação ativa.');
        }

        $this->repository->delete($id);

        return response()->json(['msg' => 'O cliente foi removido com sucesso!'], 200);
    }
}
