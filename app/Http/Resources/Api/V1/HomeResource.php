<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'Home',
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'bathrooms_no' => $this->bathrooms_no,
            'bedrooms_no' => $this->bedrooms_no,
            'price' => $this->price,
            'location' => $this->location,
            'area' => $this->area,
            'avg_rating' => $this->avg_rating,
            'rating_count' => $this->rating_count,
            'home_images' => $this->home_images,
            'wifi' => $this->wifi,
            'coordinates' => $this->coordinates,
            'rent_period' => $this->rent_period,
            'owner_id' => $this->user_id,
            'owner_name' => $this->user->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'popularity_score' => $this->popularity_score,
        ];
    }
}
