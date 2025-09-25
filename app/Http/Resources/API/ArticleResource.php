<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray($request)
    {

        $fields = [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'active' => $this->active,
            'published_at' => $this->published_at->format('Y-m-d'),
            'article_type' => $this->whenLoaded('article_type', function () {
                return [
                    'id' => $this->article_type->id,
                    'name' => $this->article_type->name,
                ];
            }),
            'article_contents' => $this->whenLoaded('article_contents', ArticleContentResource::collection($this->article_contents)),
        ];

        return $fields;
    }
}
