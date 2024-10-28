<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\Api\V1\OwnerResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PendingUpdateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $updatable = class_basename($this->updatable_type);
        if($updatable == 'HotelRooms') $updatable = 'Hotel Room';
        return [
            'id' => $this->id,
            'updatable_type' => $this->updatable_type,
            'updatable_id' => $this->updatable_id,
            'changes' => $this->changes,
            'owner' => new OwnerResource($this->owner),
            'created_at' => $this->created_at,
            $updatable => $this->when($this->updatable, function () {
                // Dynamically return the appropriate resource based on type
                $namespace = "App\\Http\\Resources\\Api\\V1\\";
                $resourceClass = $namespace . class_basename($this->updatable_type) . "Resource";

                return new $resourceClass($this->updatable);
            }),
        ];
    }
}
