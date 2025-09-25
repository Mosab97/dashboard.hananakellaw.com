<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerRateResource extends JsonResource
{
    public function toArray($request)
    {

        $fields = [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'rate' => $this->rate,
            'active' => $this->active,
            'order' => $this->order,
        ];

        return $fields;
    }
}
