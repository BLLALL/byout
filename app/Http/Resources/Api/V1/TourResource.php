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
                'source' => $this->source,
                'destination' => $this->destination,
                'departure_time' => $this->departure_time,
                'arrival_time' => $this->arrival_time,
                'tour_company_id' => $this->tour_company_id,
                'seat_position' => $this->seat_position,
                'traveller_gender' => $this->traveller_gender,

        ];
    }
}
