<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Resources\Json\JsonResource;

class SucessStoryResource extends JsonResource
{
    public function toArray($request)
    {

        $fields = [
            'id' => $this->id,
            'owner_name' => $this->owner_name,
            'rate' => $this->rate,
            'description' => $this->description,
            ];

        return $fields;
    }
}
