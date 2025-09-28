<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleContentResource extends JsonResource
{
    public function toArray($request)
    {

        $fields = [
            'id' => $this->id,
            'title' => $this->title,
            'features' => collect($this->features)->pluck(app()->getLocale())->toArray(),
            'created_at' => $this->created_at->format('Y-m-d'),
            'active' => $this->active,
        ];

        return $fields;
    }
}
