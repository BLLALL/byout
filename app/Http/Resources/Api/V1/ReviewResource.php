<?php

namespace App\Http\Resources;

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
            'id' => $this->id,
            'rating' => $this->rating,
            'user' => new UserResource($this->whenLoaded('user')),
            'reviewable_type' => $this->reviewable_type,
            'reviewable_id' => $this->reviewable_id,
            'reviewable' => $this->when($this->reviewable, function () {
                return $this->reviewable_type::createResource($this->reviewable);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
