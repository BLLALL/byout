<?php

namespace App\Services;

use App\Models\HotelRooms;
use Illuminate\Http\Request;
use App\traits\HandlesDiscount;

class HotelRoomService extends UpdateEntityService
{
    use HandlesDiscount;
    protected PendingUpdateService $pendingUpdateService;
    public function __construct(PendingUpdateService $pendingUpdateService)
    {
        $this->pendingUpdateService = $pendingUpdateService;
    }

    public function updateHotelRoom(HotelRooms $hotelRoom, Request $request)
    {
        $fillableAttributes = [
            'title', 'price', 'discount_price', 'area',
            'bathrooms_no', 'bedrooms_no',
            'is_reserved','available_from', 'available_until',
        ];

        $data = $request->only($fillableAttributes);
        $this->handlePriceUpdate($hotelRoom, $data);
        $this->setCurrency($data, $hotelRoom->owner);

        return $this->pendingUpdateService->createPendingUpdate($hotelRoom, $data, 'room_images');
    }
}
