<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Resources\Json\JsonResource;

class BookAppointmentResource extends JsonResource
{
    public function toArray($request)
    {

        $fields = [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'city' => $this->city,
            'appointment_type' => $this->whenLoaded('appointmentType', new AppointmentTypeResource($this->appointmentType)),
            'date' => $this->date?->format('Y-m-d') ?? null,
        ];

        return $fields;
    }
}
