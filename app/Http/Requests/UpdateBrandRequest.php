<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBrandRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'  => ['sometimes', 'string', 'max:30', Rule::unique('brands', 'name')->ignore($this->route('brand'))],
            'image' => 'sometimes|string|max:100',
        ];
    }
}
