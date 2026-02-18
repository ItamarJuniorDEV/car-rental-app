<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        $brand = Brand::create($request->all());
        return $brand;
    }

    public function show(Braneeed $brand)
    {
        //
    }

    public function update(Request $request, Brand $brand)
    {
        //
    }

    public function destroy(Brand $brand)
    {
        //
    }
}
