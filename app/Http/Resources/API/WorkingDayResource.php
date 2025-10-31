<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\API\WorkingHourResource;
class WorkingDayResource extends JsonResource
{
    public function toArray($request)
    {

        return [
        'id' => $this->id,
        'day' => $this->day?->value ?? 'N/A',
        'day_label' => $this->day?->label() ?? 'N/A',
        'day_of_week' => $this->day?->dayOfWeek() ?? 0,
        'working_day_hours' => WorkingHourResource::collection($this->workingDayHours),
    ];
    }
}
