<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLineRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'brand_id' => 'required|integer|exists:brands,id',
            'name' => 'required|string|max:30',
            'image' => 'required|string|max:100',
            'door_count' => 'required|integer|min:1',
            'seats' => 'required|integer|min:1',
            'air_bag' => 'required|boolean',
            'abs' => 'required|boolean',
        ];
    }
}
