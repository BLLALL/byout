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
            'owner_phone_number' => $this->owner->user?->phone_number,
            'rooms' => $this->when($this->hotelRooms, function () {
                return HotelRoomsResource::collection($this->hotelRooms);
            }),
        
            'pending' => $this->pending,
            'property_ownership' => $this->when(
                $this->documents,
                fn() => optional($this->documents->firstWhere('document_type', 'property_ownership'))->file_path
            ),
            'agreement_contract' => $this->when(
                $this->documents,
                fn() => optional($this->documents->firstWhere('document_type', 'agreement_contract'))->file_path
            ),
            'signatory_authorization' => $this->when(
                $this->documents,
                fn() => optional($this->documents->firstWhere('document_type', 'signatory_authorization'))->file_path
            ),
            'hotel_license' => $this->when(
                $this->documents,
                fn() => optional($this->documents->firstWhere('document_type', 'hotel_license'))->file_path
            ),
        ];
    }
}
