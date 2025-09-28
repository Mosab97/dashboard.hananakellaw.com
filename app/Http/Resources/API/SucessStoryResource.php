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
            'description' => $this->description,
            'url' => $this->url,
            'thumbnail' => $this->thumbnail_path,
            'active' => $this->active,
            'created_at' => $this->created_at->format('Y-m-d'),
            ];

        return $fields;
    }
}
