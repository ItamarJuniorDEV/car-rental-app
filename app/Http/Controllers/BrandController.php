<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Repositories\Contracts\BrandRepositoryInterface;

class BrandController extends Controller
{
    private BrandRepositoryInterface $repository;

    public function __construct(BrandRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        $brands = $this->repository->all();
        return response()->json($brands, 200);
    }

    public function store(StoreBrandRequest $request)
    {
        $brand = $this->repository->create($request->validated());
        return response()->json($brand, 201);
    }

    public function show($id)
    {
        $brand = $this->repository->find($id);
        return response()->json($brand, 200);
    }

    public function update(UpdateBrandRequest $request, $id)
    {
        $brand = $this->repository->update($id, $request->validated());
        return response()->json($brand, 200);
    }

    public function destroy($id)
    {
        $this->repository->delete($id);
        return response()->json(['msg' => 'A marca foi removida com sucesso!'], 200);
    }
}
