<?php

namespace App\Http\Resources\Api\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TourResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'type' => 'Tour',
            'id' => $this->id,
            'price' => $this->price,
            'tour_type' => $this->tour_type,
            'source' => $this->source,
            'destination' => $this->destination,
            'departure_time' => $this->departure_time,
            'arrival_time' => $this->arrival_time,
            'time_difference' => $this->time_difference,
            'recurrence' => $this->recurrence,
            'status' => $this->status,
            'tour_company_id' => $this->tour_company_id,
            //start bus details
            'has_wifi' => $this->bus->has_wifi,
            'has_bathroom' => $this->bus->has_bathroom,
            'has_movie_screens' => $this->bus->has_movie_screens,
            'has_entrance_camera' => $this->bus->has_entrance_camera,
            'has_air_conditioner' => $this->bus->has_air_conditioner,
            'has_passenger_camera' => $this->bus->has_passenger_camera,
            'bus_images' => $this->bus->bus_images,
            'seats_number' => $this->bus->seats_number,
            //end bus details
            'driver_name' => $this->driver->name,
            'is_driver_smoker' => $this->driver->is_smoker,
            'owner_id' => $this->owner->user_id,
            'driver_id' => $this->driver_id,
            'tour_company_name' => $this->transportation_company,
            'documents' => $this->documents->map(function ($document) {
                return [
                    'id' => $document->id,
                    'type' => $document->document_type,
                    'file_path' => $document->file_path,
                    'uploaded_at' => $document->uploaded_at,
                ];
            }),
        ];
    }
}
