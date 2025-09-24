<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentTypeResource extends JsonResource
{
    public function toArray($request)
    {

        $fields = [
            'id' => $this->id,
            'name' => $this->name,
            'active' => $this->active,
            'order' => $this->order,
        ];

        return $fields;
    }
}
