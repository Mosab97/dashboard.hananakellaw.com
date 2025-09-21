<?php

namespace App\Http\Resources\API;

use Akaunting\Setting\Facade as Setting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->resource->getTranslations('name'),
            'description' => $this->resource->getTranslations('description'),
            'image' => $this->image_path,
            'price' => $this->price,
            'currency' => Setting::get('currency'),
            'category' => new CategoryResource($this->category),
            'sizes' => SizeResource::collection($this->sizes),
        ];
    }
}
