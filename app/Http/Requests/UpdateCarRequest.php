<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCarRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'line_id'   => 'sometimes|integer|exists:lines,id',
            'plate'     => ['sometimes', 'string', 'max:10', Rule::unique('cars', 'plate')->ignore($this->route('car'))],
            'available' => 'sometimes|boolean',
            'km'        => 'sometimes|integer|min:0',
        ];
    }
}
