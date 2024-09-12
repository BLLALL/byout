<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray($request)
    {
        $reviewableData = $this->getReviewableData();

        return array_merge($reviewableData, [
            'id' => $this->id,
            'rating' => $this->rating,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
    }

    protected function getReviewableData()
    {
        switch ($this->reviewable_type) {
            case 'App\Models\Hotel':
                return $this->getHotelData();
            case 'App\Models\Chalet':
                return $this->getChaletData();
            case 'App\Models\Home':
                return $this->getHomeData();
            default:
                return [];
        }
    }

    protected function getHotelData()
    {
        return [
            'name' => $this->reviewable->name,
            'location' => $this->reviewable->location,
            'wifi' => $this->reviewable->wifi,
            'coordinates' => $this->reviewable->coordinates,
            'hotel_images' => $this->reviewable->hotel_images,
            'avg_rating' => $this->reviewable->avg_rating,
            'rating_count' => $this->reviewable->rating_count,
            'popularity_score' => $this->reviewable->popularity_score,
            'hotel_rooms' => $this->reviewable->hotel_rooms,
            'owner_id' => $this->reviewable->owner_id,
        ];
    }

    protected function getChaletData()
    {
        return [
            'title' => $this->reviewable->title,
            'description' => $this->reviewable->description,
            'price' => $this->reviewable->price,
            'area' => $this->reviewable->area,
            'bathrooms_no' => $this->reviewable->bathrooms_no,
            'bedrooms_no' => $this->reviewable->bedrooms_no,
            'chalet_images' => $this->reviewable->chalet_images,
            'location' => $this->reviewable->location,
            'avg_rating' => $this->reviewable->avg_rating,
            'rating_count' => $this->reviewable->rating_count,
            'popularity_score' => $this->reviewable->popularity_score,
            'wifi' => $this->reviewable->wifi,
            'coordinates' => $this->reviewable->coordinates,
            'air_conditioning' => $this->reviewable->air_conditioning,
            'sea_view' => $this->reviewable->sea_view,
            'distance_to_beach' => $this->reviewable->distance_to_beach,
            'available_until' => $this->reviewable->available_until,
            'max_occupancy' => $this->reviewable->max_occupancy,
            'rent_period' => $this->reviewable->rent_period,
            'owner_id' => $this->reviewable->owner_id,
        ];
    }

    protected function getHomeData()
    {
        return [
            'title' => $this->reviewable->title,
            'description' => $this->reviewable->description,
            'price' => $this->reviewable->price,
            'area' => $this->reviewable->area,
            'bathrooms_no' => $this->reviewable->bathrooms_no,
            'bedrooms_no' => $this->reviewable->bedrooms_no,
            'home_images' => $this->reviewable->home_images,
            'location' => $this->reviewable->location,
            'avg_rating' => $this->reviewable->avg_rating,
            'rating_count' => $this->reviewable->rating_count,
            'popularity_score' => $this->reviewable->popularity_score,
            'wifi' => $this->reviewable->wifi,
            'coordinates' => $this->reviewable->coordinates,
            'rent_period' => $this->reviewable->rent_period,
            'owner_id' => $this->reviewable->owner_id,
        ];
    }
}
