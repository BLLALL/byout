<?php

namespace App\Http\Resources\Api\V1;

use Brick\Math\RoundingMode;
use Brick\Money\Money;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Route;

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
            'discount_price' => $this->discount_price,
            'currency' => $this->currency,
            'location' => $this->location,
            'area' => $this->area,
            'avg_rating' => $this->avg_rating,
            'rating_count' => $this->rating_count,
            'home_images' => $this->home_images,
            'living_room_no' => $this->living_room_no,
            'kitchen_no' => $this->kitchen_no,
            'total_rooms_no' => $this->living_room_no + $this->bedrooms_no,
            'wifi' => $this->wifi,
            'coordinates' => $this->coordinates,
            'rent_period' => $this->rent_period,
            'owner_id' => $this->owner->user_id,
            'owner_name' => $this->owner->user->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'popularity_score' => $this->popularity_score,
            'pending' => $this->pending,
            'available_from' => ($this->available_from),
            'available_until' => $this->available_until,
            'is_available' => $this->is_available,
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
        ];
    }
}
