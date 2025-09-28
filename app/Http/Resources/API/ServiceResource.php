<?php

namespace App\Http\Resources\API;

use Akaunting\Setting\Facade as Setting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'icon' => $this->icon_path,
            'features' => collect($this->features)->pluck(app()->getLocale())->toArray(),
            'link' => $this->link,
            'active' => $this->active,
            'created_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}
