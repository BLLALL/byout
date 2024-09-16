<?php

namespace App\Services;

use App\Models\HotelRooms;
use Illuminate\Http\Request;

class HotelRoomService extends UpdateEntityService
{
    public function updateHotelRoom(HotelRooms $hotelRoom, Request $request)
    {
        $fillableAttributes = [
            'title', 'price', 'area',
            'bathrooms_no', 'bedrooms_no',
            'is_reserved','available_from', 'available_until',
        ];

        $this->updateEntity($hotelRoom, $request, $fillableAttributes, 'room_images');
    }
}
