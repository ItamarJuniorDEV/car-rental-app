<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:100',
            'cpf' => ['sometimes', 'string', 'size:14', 'regex:/^\d{3}\.\d{3}\.\d{3}-\d{2}$/', Rule::unique('clients', 'cpf')->ignore($this->route('client'))],
            'email' => ['sometimes', 'email', 'max:100', Rule::unique('clients', 'email')->ignore($this->route('client'))],
            'phone' => 'sometimes|string|max:20',
        ];
    }
}
