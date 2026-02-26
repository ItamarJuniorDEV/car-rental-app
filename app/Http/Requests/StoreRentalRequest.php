<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRentalRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'client_id' => 'required|integer|exists:clients,id',
            'car_id' => 'required|integer|exists:cars,id',
            'period_start_date' => 'required|date',
            'period_expected_end_date' => 'required|date|after:period_start_date',
            'daily_rate' => 'required|numeric|min:1',
            'initial_km' => 'required|integer|min:0',
        ];
    }
}
