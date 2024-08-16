<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HotelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'Hotel',
            'id' => $this->id,
            'name' => $this->name,
            'location' => $this->location,
            'wifi' => $this->wifi,
            'coordinates' => $this->coordinates,
            'hotel_images' => $this->hotel_images,
            'avg_rating' => $this->avg_rating,
            'rating_count' => $this->rating_count,
            'popularity_score' => $this->popularity_score,
            'hotel_rooms' => $this->hotel_rooms,
            'user_id' => $this->user_id,
        ];
    }
}
