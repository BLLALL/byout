<?php

namespace App\Services;

use App\Models\Hotel;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class UpdateVehicleService extends UpdateEntityService
{
    public function updateVehicle(Vehicle $vehicle, Request $request)
    {
        $fillableAttributes =[
            'type', 'model', 'registration_number',
            'vehicle_images', 'seats_number', 'status',
            'has_wifi', 'has_air_conditioner', 'has_gps',
            'has_movie_screens',
        ];

        $this->updateEntity($vehicle, $request, $fillableAttributes, 'vehicle_images');
    }

}
