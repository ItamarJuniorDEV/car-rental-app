<?php

namespace App\Http\Requests;

use App\Models\Rental;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRentalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'period_actual_end_date' => 'sometimes|date',
            'final_km' => 'sometimes|integer|min:0|required_with:period_actual_end_date',
        ];
    }

    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function (\Illuminate\Validation\Validator $validator) {
            $rental = Rental::query()->find(intval($this->route('rental')));

            if (! ($rental instanceof Rental)) {
                return;
            }

            if ($this->has('final_km') && $this->integer('final_km') < $rental->initial_km) {
                $validator->errors()->add(
                    'final_km',
                    'A quilometragem final não pode ser inferior à inicial.'
                );
            }

            if ($this->has('period_actual_end_date')) {
                $actualEnd = Carbon::parse($this->string('period_actual_end_date')->value());

                if ($actualEnd->lt($rental->period_start_date)) {
                    $validator->errors()->add(
                        'period_actual_end_date',
                        'A data de devolução não pode ser anterior ao início da locação.'
                    );
                }
            }
        });
    }
}
