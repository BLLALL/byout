<?php

namespace App\Services;

use App\Models\HotelRooms;

class CreateHotelRoomService extends CreateEntityService
{
    public function getModel()
    {
        return new HotelRooms();
    }

    protected function getFillableAttributes()
    {
        return [
            'title',
            'price',
            'area',
            'bathrooms_no',
            'bedrooms_no',
            'room_images',
            'is_reserved',
            'hotel_id',
            'available_from',
            'available_until',
        ];
    }

    protected function getImageColumn()
    {
        return 'room_images';
    }

    protected function getImagePath()
    {
        return 'hotel_rooms';
    }

    // protected function handleImages($entity, $request)
    // {
    //     $roomImages = $request->input('room_images');
    //     if (!empty($roomImages)) {
    //         $images = [];
    //         foreach ($roomImages as $image) {
    //             $imagePath = $image->store($this->imagePath, 'public');
    //             $images[] = 'https://fayroz97.com/real-estate/' . $imagePath;
    //         }
    //         $entity->room_images = $images;
    //     }
    //     return $entity;
    // }
}
