<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HotelRoomsResource extends JsonResource
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
            'price' => $this->price,
            'area' => $this->area,
            'bathrooms_no' => $this->bathrooms_no,
            'bedrooms_no' => $this->bedrooms_no,
            'room_images' => $this->room_images,
            'is_reserved' => $this->is_reserved,
            'hotel_id' => $this->hotel_id,
            'hotel_details'=> $this->hotel,
        ];
    }
}
