<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Resources\Json\JsonResource;
class WorkingHourResource extends JsonResource
{
    public function toArray($request)
    {

        return [
        'id' => $this->id,
        // 'day' => $this->workingDay?->day?->value ?? 'N/A',
        // 'day_label' => $this->workingDay?->day?->label() ?? 'N/A',
        'start_time' => $this->start_time?->format('H:i') ?? 'N/A',
        'end_time' => $this->end_time?->format('H:i') ?? 'N/A',
    ];
    }
}
