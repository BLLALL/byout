<?php

namespace App\Http\Resources\Api\V1;

use Brick\Math\RoundingMode;
use Brick\Money\Money;
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
            'currency' => $this->currency,
            'tour_type' => $this->tour_type,
            'source' => $this->source,
            'destination' => $this->destination,
            'departure_time' => $this->departure_time,
            'arrival_time' => $this->arrival_time,
            'time_difference' => $this->time_difference,
            'recurrence' => $this->recurrence,
            'status' => $this->status,
            'tour_company_id' => $this->tour_company_id,
            'vehicle' => $this->when($this->vehicle, function () {
                return new VehicleResource($this->vehicle);
            }),
            'driver_name' => $this->driver?->user->name,
            'vehicle_id' => $this->vehicle_id,
            'is_driver_smoker' => $this->driver?->is_smoker,
            'driver_id' => $this->driver_id,
            'driver_image' => $this->driver?->user->profile_image, 
            'driver_phone_number' => $this->driver?->user->phone_number,
            'owner_id' => $this->owner->user_id,
            'transportation_company' => $this->transportation_company,
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
