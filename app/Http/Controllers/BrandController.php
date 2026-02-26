<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
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
        $this->authorize('viewAny', Brand::class);

        if ($request->has('name')) {
            return BrandResource::collection($this->repository->search($request->name));
        }

        return BrandResource::collection($this->repository->paginate());
    }

    public function store(StoreBrandRequest $request)
    {
        $this->authorize('create', Brand::class);

        $brand = $this->repository->create($request->validated());
        return (new BrandResource($brand))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $brand = $this->repository->find($id);
        $this->authorize('view', $brand);

        return new BrandResource($brand);
    }

    public function update(UpdateBrandRequest $request, $id)
    {
        $brand = $this->repository->find($id);
        $this->authorize('update', $brand);

        return new BrandResource($this->repository->update($id, $request->validated()));
    }

    public function destroy($id)
    {
        $brand = $this->repository->find($id);
        $this->authorize('delete', $brand);

        $this->repository->delete($id);
        return response()->json(['msg' => 'A marca foi removida com sucesso!'], 200);
    }
}
