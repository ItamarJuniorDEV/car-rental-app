<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Http\Resources\BrandResource;
use App\Repositories\Contracts\BrandRepositoryInterface;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    private BrandRepositoryInterface $repository;

    public function __construct(BrandRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        if ($request->has('name')) {
            return BrandResource::collection($this->repository->search($request->name));
        }

        return BrandResource::collection($this->repository->paginate());
    }

    public function store(StoreBrandRequest $request)
    {
        $brand = $this->repository->create($request->validated());
        return (new BrandResource($brand))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        return new BrandResource($this->repository->find($id));
    }

    public function update(UpdateBrandRequest $request, $id)
    {
        return new BrandResource($this->repository->update($id, $request->validated()));
    }

    public function destroy($id)
    {
        $this->repository->delete($id);
        return response()->json(['msg' => 'A marca foi removida com sucesso!'], 200);
    }
}
