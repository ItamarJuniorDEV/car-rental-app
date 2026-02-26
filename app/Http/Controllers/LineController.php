<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLineRequest;
use App\Http\Requests\UpdateLineRequest;
use App\Http\Resources\LineResource;
use App\Models\Line;
use App\Repositories\Contracts\LineRepositoryInterface;
use Illuminate\Http\Request;

class LineController extends Controller
{
    private LineRepositoryInterface $repository;

    public function __construct(LineRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Line::class);

        if ($request->has('brand_id')) {
            return LineResource::collection($this->repository->findByBrand((int) $request->brand_id));
        }

        return LineResource::collection($this->repository->paginate());
    }

    public function store(StoreLineRequest $request)
    {
        $this->authorize('create', Line::class);

        $line = $this->repository->create($request->validated());
        return (new LineResource($line))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $line = $this->repository->find($id);
        $this->authorize('view', $line);

        return new LineResource($line);
    }

    public function update(UpdateLineRequest $request, $id)
    {
        $line = $this->repository->find($id);
        $this->authorize('update', $line);

        return new LineResource($this->repository->update($id, $request->validated()));
    }

    public function destroy($id)
    {
        $line = $this->repository->find($id);
        $this->authorize('delete', $line);

        $this->repository->delete($id);
        return response()->json(['msg' => 'A linha foi removida com sucesso!'], 200);
    }
}
