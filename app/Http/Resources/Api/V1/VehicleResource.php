<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'model' => $this->model,
            'registration_number' => $this->registration_number,
            'vehicle_images' => $this->vehicle_images,
            'seats_number' => $this->seats_number,
            'status' => $this->status,
            'has_wifi' => $this->has_wifi,
            'has_air_conditioner' => $this->has_air_conditioner,
            'has_gps' => $this->has_gps,
            'has_movie_screens' => $this->has_movie_screens,
            'owner_id' => $this->owner->user_id,
            'has_entrance_camera' => $this->when($this->type === 'bus', $this->has_entrance_camera),
            'has_passenger_camera' => $this->when($this->type === 'bus', $this->has_passenger_camera),
            'has_bathroom' => $this->when($this->type === 'bus', $this->has_bathroom),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
