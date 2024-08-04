<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'home_review',
            'id' => $this->id,
            'attributes' => [
                'rating' => $this->rating,
                'user_id' => $this->user_id,
                'home_id' => $this->home_id,
            ],
        ];
    }
}
