<?php

namespace App\Http\Resources\Api\V1;

use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChaletResource extends JsonResource
{
/**
 * Transform the resource into an array.
 *
 * @return array<string, mixed>
 */
public function toArray(Request $request): array
{
    $money = Money::ofMinor($this->price, $this->currency, roundingMode: RoundingMode::HALF_UP);
    return [
        'type' => 'Chalet',
        'id' => $this->id,
        'title' => $this->title,
        'description' => $this->description,
        'bathrooms_no' => $this->bathrooms_no,
        'bedrooms_no' => $this->bedrooms_no,
        'price' => $money->getAmount()->toFloat(),
        'currency' => $money->getCurrency()->getCurrencyCode(),
        'location' => $this->location,
        'area' => $this->area,
        'avg_rating' => $this->avg_rating,
        'rating_count' => $this->rating_count,
        'chalet_images' => $this->chalet_images,
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
        'air_conditioning' => $this->air_conditioning,
        'sea_view' => $this->sea_view,
        'distance_to_beach' => $this->distance_to_beach,
        'documents' => $this->documents->map(function ($document) {
            return [
                'id' => $document->id,
                'type' => $document->document_type,
                'file_path' => $document->file_path,
                'uploaded_at' => $document->uploaded_at,
            ];
        }),
        'pending' => $this->pending,
        'available_from' => $this->available_from,
        'available_until' => $this->available_until,
        'is_available' => $this->is_available,
    ];
}
}
