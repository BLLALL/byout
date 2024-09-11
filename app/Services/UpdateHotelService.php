<?php

namespace App\Services;

use App\Models\Hotel;
use Illuminate\Http\Request;

class HotelService extends UpdateEntityService
{
    public function updateHotel(Hotel $hotel, Request $request)
    {
        $fillableAttributes =[
            'name', 'wifi', 'location',
            'wifi', 'coordinates'
        ];

        $this->updateEntity($hotel, $request, $fillableAttributes, 'hotel_images');
    }

}