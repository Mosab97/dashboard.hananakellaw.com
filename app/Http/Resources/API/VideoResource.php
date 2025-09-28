<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    public function toArray($request)
    {

        $fields = [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'path' => $this->path,
            'thumbnail' => $this->thumbnail_path,
            'active' => $this->active,
            'created_at' => $this->created_at->format('Y-m-d'),
        ];

        return $fields;
    }
}
