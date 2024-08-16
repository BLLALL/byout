<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\filters\HotelRoomsFilter;
use App\Http\Resources\Api\V1\HotelResource;
use App\Http\Resources\Api\V1\HotelRoomsResource;
use App\Models\Hotel;
use App\Models\HotelRooms;
use App\Models\User;
use App\traits\apiResponses;
use Illuminate\Http\Request;

class HotelRoomsController extends Controller
{
    use apiResponses;
    public function index(HotelRoomsFilter $filter)
    {
        return HotelRoomsResource::collection(HotelRooms::filter($filter)->get());
    }

    public function show($roomId)
    {
        $room = HotelRooms::find($roomId);

        return new HotelRoomsResource($room);
    }

    public function getOwnerRooms($ownerId)
    {
        $owner = User::find($ownerId);
        if (!$owner->hasRole('Hotel Owner')) {
            return $this->error([
                'The user you are trying to reach is not hotel owner'
            ], 403);
        }
        $hotel = $owner->hotel[0];
        return HotelRoomsResource::collection($hotel->hotelRooms);

    }

    public function getOwner($roomId)
    {
        $room = HotelRooms::find($roomId);
        return $room->hotel->user;
    }


}
