<?php

namespace App\Services;

use App\Models\HotelRooms;
use Illuminate\Http\Request;

class HotelRoomService extends UpdateEntityService
{
    protected PendingUpdateService $pendingUpdateService;
    public function __construct(PendingUpdateService $pendingUpdateService)
    {
        $this->pendingUpdateService = $pendingUpdateService;
    }

    public function updateHotelRoom(HotelRooms $hotelRoom, Request $request)
    {
        $fillableAttributes = [
            'title', 'price', 'area',
            'bathrooms_no', 'bedrooms_no',
            'is_reserved','available_from', 'available_until',
        ];

        return $this->pendingUpdateService->createPendingUpdate($hotelRoom, $request, $fillableAttributes, 'room_images');
    }
}
