<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserReviewCollectionResource extends UserReviewResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'user_reviews',
            'user_id' => $this->collection->first()->user_id,
            'reviews' => $this->collection->map(function ($review) {
                return [
                    'attributes' => [
                        'rating' => $review->rating,
                        'home_id' => $review->home_id
                    ]
                ];
            })
        ];
    }
}
