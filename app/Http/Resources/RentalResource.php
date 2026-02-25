<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RentalResource extends JsonResource
{
    public function toArray($request)
    {
        $total = null;

        if ($this->period_actual_end_date !== null) {
            $days  = (int) $this->period_start_date->diffInDays($this->period_actual_end_date);
            $total = $days * $this->daily_rate;
        }

        return [
            'id'                       => $this->id,
            'period_start_date'        => $this->period_start_date?->format('Y-m-d H:i:s'),
            'period_expected_end_date' => $this->period_expected_end_date?->format('Y-m-d H:i:s'),
            'period_actual_end_date'   => $this->period_actual_end_date?->format('Y-m-d H:i:s'),
            'daily_rate'               => $this->daily_rate,
            'initial_km'               => $this->initial_km,
            'final_km'                 => $this->final_km,
            'total'                    => $total,
            'client'                   => new ClientResource($this->whenLoaded('client')),
            'car'                      => new CarResource($this->whenLoaded('car')),
        ];
    }
}
