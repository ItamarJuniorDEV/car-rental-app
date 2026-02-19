<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRentalRequest;
use App\Http\Requests\UpdateRentalRequest;
use App\Models\Rental;

class RentalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(StoreRentalRequest $request)
    {
        //
    }

    public function show(Rental $rental)
    {
        //
    }

    public function edit(Rental $rental)
    {
        //
    }

    public function update(UpdateRentalRequest $request, Rental $rental)
    {
        //
    }

    public function destroy(Rental $rental)
    {
        //
    }
}
