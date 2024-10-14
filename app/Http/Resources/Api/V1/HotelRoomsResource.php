<?php

namespace App\Http\Resources\Api\V1;

use Brick\Math\RoundingMode;
use Brick\Money\Money;
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
        $money = Money::ofMinor($this->price, $this->currency, roundingMode: RoundingMode::HALF_UP);
    
        return [
            'id' => $this->id,
            'title' => $this->title,
            'price' => $money->getAmount()->toFloat(),
            'currency' => $money->getCurrency()->getCurrencyCode(),
            'area' => $this->area,
            'bathrooms_no' => $this->bathrooms_no,
            'bedrooms_no' => $this->bedrooms_no,
            'room_images' => $this->room_images,
            'is_reserved' => $this->is_reserved,
            'hotel_id' => $this->hotel_id,
            'available_from' => $this->available_from,
            'available_until' => $this->available_until,
            'is_available' => $this->is_available,
        ];
    }
}
