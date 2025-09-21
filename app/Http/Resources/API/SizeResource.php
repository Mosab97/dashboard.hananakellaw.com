<?php

namespace App\Http\Resources\API;

use Akaunting\Setting\Facade as Setting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SizeResource extends JsonResource
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
            'name' => $this->name,
            'price' => $this->price,
            'currency' => Setting::get('currency'),
            // 'products' => ProductResource::collection($this->products),
        ];
    }
}
