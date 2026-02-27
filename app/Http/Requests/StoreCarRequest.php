<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'line_id' => 'required|integer|exists:lines,id',
            'plate' => 'required|string|max:10|unique:cars,plate',
            'available' => 'required|boolean',
            'km' => 'required|integer|min:0',
        ];
    }
}
