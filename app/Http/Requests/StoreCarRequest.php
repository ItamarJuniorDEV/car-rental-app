<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'line_id'   => 'required|integer|exists:lines,id',
            'plate'     => 'required|string|max:10|unique:cars,plate',
            'available' => 'required|boolean',
            'km'        => 'required|integer|min:0',
        ];
    }
}
