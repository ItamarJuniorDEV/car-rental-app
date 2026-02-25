<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLineRequest;
use App\Http\Requests\UpdateLineRequest;
use App\Http\Resources\LineResource;
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
        if ($request->has('brand_id')) {
            return LineResource::collection($this->repository->findByBrand((int) $request->brand_id));
        }

        return LineResource::collection($this->repository->paginate());
    }

    public function store(StoreLineRequest $request)
    {
        $line = $this->repository->create($request->validated());
        return (new LineResource($line))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        return new LineResource($this->repository->find($id));
    }

    public function update(UpdateLineRequest $request, $id)
    {
        return new LineResource($this->repository->update($id, $request->validated()));
    }

    public function destroy($id)
    {
        $this->repository->delete($id);
        return response()->json(['msg' => 'A linha foi removida com sucesso!'], 200);
    }
}
