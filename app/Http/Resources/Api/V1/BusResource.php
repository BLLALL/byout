<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'Bus',
            'id' => $this->id,
            'registration_number' => $this->registration_number,
            'model' => $this->model,
            'bus_images' => $this->bus_images,
            'seats_number' => $this->seats_number,
            'has_wifi' => $this->has_wifi,
            'has_bathroom' => $this->has_bathroom,
            'has_movie_screens' => $this->has_movie_screens,
            'has_entrance_camera' => $this->has_entrance_camera,
            'has_air_conditioner' => $this->has_air_conditioner,
            'has_passenger_camera' => $this->has_passenger_camera,
            'owner_id' => $this->owner->user_id,
            "ownership" => $this->ownership,
        ];
    }
}
