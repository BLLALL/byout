<?php

namespace App\Services;

use App\Models\Hotel;
use Illuminate\Http\Request;

class UpdateHotelService extends UpdateEntityService
{
    protected PendingUpdateService $pendingUpdateService;

    public function __construct(PendingUpdateService $pendingUpdateService)
    {
        $this->pendingUpdateService = $pendingUpdateService;
    }

    public function updateHotel(Hotel $hotel, Request $request)
    {
        $fillableAttributes = [
            'name', 'wifi', 'location',
            'wifi', 'coordinates', 'pending',
        ];

        return $this->updateEntity($hotel, $request, $fillableAttributes, 'hotel_images');
    }

}
