<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Resources\Json\JsonResource;

class SliderResource extends JsonResource
{
    public function toArray($request)
    {

        $fields = [
            'id' => $this->id,
            'restaurant' => $this->whenLoaded('restaurant', function () {
                return new RestaurantResource($this->restaurant);
            }),
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image_path,

            'link' => $this->link,
        ];

        return $fields;
    }
}
