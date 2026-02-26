<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CarResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'plate' => $this->plate,
            'available' => $this->available,
            'km' => $this->km,
            'line' => new LineResource($this->whenLoaded('line')),
        ];
    }
}
