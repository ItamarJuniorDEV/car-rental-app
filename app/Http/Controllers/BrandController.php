<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    private Brand $brand;

    public function __construct(Brand $brand)
    {
        $this->brand = $brand;
    }

    public function index()
    {
        // $brands = Brand::all();
        $brands = $this->brand->all();
        return response()->json($brands, 200);
    }

    public function store(Request $request)
    {
        // $brands = Brand::create($request->all());
        $brand = $this->brand->create($request->all());
        return response()->json($brand, 201);
    }

    public function show($id)
    {
        // $brand = Brand::find($id);
        $brand = $this->brand->find($id);
        if ($brand === null) {
            return response()->json(['erro' => 'Recurso pesquisado não existe!'], 404);
        }

        return response()->json($brand, 200);
    }

    public function update(Request $request, $id)
    {
        $brand = $this->brand->find($id);

        if ($brand === null) {
            return response()->json(['erro' => 'Impossível realizar a atualização. O recurso solicitado não existe!'], 404);
        }

        $brand->update($request->all());
        return response()->json($brand, 200);
    }

    public function destroy($id)
    {
        $brand = $this->brand->find($id);
        if ($brand === null) {
            return response()->json(['erro' => 'Impossível realizar a exclusão. O recurso solicitado não existe'], 404);
        }
        $brand->delete();
        return response()->json(["msg" => "A marca foi removida com sucesso!"], 200);
    }
}
