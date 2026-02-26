<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:100',
            'cpf' => ['required', 'string', 'size:14', 'regex:/^\d{3}\.\d{3}\.\d{3}-\d{2}$/', 'unique:clients,cpf'],
            'email' => 'required|email|max:100|unique:clients,email',
            'phone' => 'required|string|max:20',
        ];
    }
}
