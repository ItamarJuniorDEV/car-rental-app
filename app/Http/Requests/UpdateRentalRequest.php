<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRentalRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'period_actual_end_date' => 'sometimes|date',
            'final_km'               => 'sometimes|integer|min:0|required_with:period_actual_end_date',
        ];
    }
}
