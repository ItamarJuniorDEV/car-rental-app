<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LineResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'image'      => $this->image,
            'door_count' => $this->door_count,
            'seats'      => $this->seats,
            'air_bag'    => $this->air_bag,
            'abs'        => $this->abs,
            'brand'      => new BrandResource($this->whenLoaded('brand')),
        ];
    }
}
