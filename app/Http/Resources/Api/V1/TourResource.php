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
            'attributes' => [
                'price' => $this->price,
                'source' => $this->source,
                'destination' => $this->destination,
                'departure_time' => $this->departure_time,
                'arrival_time' => $this->arrival_time,
                'time_difference' => $this->time_difference,
                'tour_company_id' => $this->tour_company_id,
                'seat_status' => json_decode($this->seat_status),
            ],
        ];
    }
}
