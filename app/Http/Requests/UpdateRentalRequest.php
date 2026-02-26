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

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $rental = Rental::find($this->route('rental'));

            if (! $rental) {
                return;
            }

            if ($this->has('final_km') && (int) $this->final_km < $rental->initial_km) {
                $validator->errors()->add(
                    'final_km',
                    'A quilometragem final não pode ser inferior à inicial.'
                );
            }

            if ($this->has('period_actual_end_date')) {
                $actualEnd = Carbon::parse($this->period_actual_end_date);

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
