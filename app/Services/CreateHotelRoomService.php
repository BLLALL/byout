<?php

namespace App\Services;

use App\Models\HotelRooms;

class CreateHotelRoomService extends CreateEntityService
{
    public function getModel(): HotelRooms
    {
        return new HotelRooms();
    }

    protected function getFillableAttributes(): array
    {
        return [
            'price',
            'area',
            'bathrooms_no',
            'room_images',
            'capacity',
            'room_type',
            'hotel_id',
            'bedrooms_no',
            'available_from',
            'available_until',
        ];
    }

    protected function getImageColumn(): string
    {
        return 'room_images';
    }

    protected function getImagePath(): string
    {
        return 'hotel_rooms';
    }

}
