<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Hotel;
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
            'wifi' => (integer)$this->wifi,
            'coordinates' => $this->coordinates,
            'hotel_images' => $this->hotel_images,
            'avg_rating' => $this->avg_rating,
            'rating_count' => $this->rating_count,
            'popularity_score' => $this->popularity_score,
            'owner_id' => $this->owner->user_id,
           'rooms' => $this->hotelRooms ? HotelRoomsResource::collection($this->hotelRooms) : null,
            'documents' => $this->documents->map(function ($document) {
                return [
                    'id' => $document->id,
                    'type' => $document->document_type,
                    'file_path' => $document->file_path,
                    'uploaded_at' => $document->uploaded_at,
                ];
            }),
            'pending' => $this->pending,
        ];
    }
}
