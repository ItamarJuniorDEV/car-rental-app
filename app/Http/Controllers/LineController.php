<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLineRequest;
use App\Http\Requests\UpdateLineRequest;
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
            $lines = $this->repository->findByBrand((int) $request->brand_id);
            return response()->json($lines, 200);
        }

        $lines = $this->repository->all();
        return response()->json($lines, 200);
    }

    public function store(StoreLineRequest $request)
    {
        $line = $this->repository->create($request->validated());
        return response()->json($line, 201);
    }

    public function show($id)
    {
        $line = $this->repository->find($id);
        return response()->json($line, 200);
    }

    public function update(UpdateLineRequest $request, $id)
    {
        $line = $this->repository->update($id, $request->validated());
        return response()->json($line, 200);
    }

    public function destroy($id)
    {
        $this->repository->delete($id);
        return response()->json(['msg' => 'A linha foi removida com sucesso!'], 200);
    }
}
