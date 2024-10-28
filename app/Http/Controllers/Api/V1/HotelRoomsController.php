<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\filters\HotelRoomsFilter;
use App\Http\Requests\Api\V1\StoreHotelRoomRequest;
use App\Http\Requests\Api\V1\UpdateHotelRoomRequest;
use App\Http\Resources\Api\V1\HotelRoomsResource;
use App\Http\Resources\Api\V1\PendingUpdateResource;
use App\Models\Hotel;
use App\Models\HotelRooms;
use App\Models\Owner;
use App\Models\User;
use App\Services\CreateHotelRoomService;
use App\traits\apiResponses;
use Illuminate\Support\Facades\Auth;
use App\Services\HotelRoomService;
class HotelRoomsController extends Controller
{
    use apiResponses;


    protected CreateHotelRoomService $createHotelRoomService;

    protected HotelRoomService $updateHotelRoomService;
    public function __construct(CreateHotelRoomService $createHotelRoomService, HotelRoomService $updateHotelRoomService)
    {
        $this->createHotelRoomService = $createHotelRoomService;
        $this->updateHotelRoomService = $updateHotelRoomService;
    }

    public function index(HotelRoomsFilter $filter)
    {
        return HotelRoomsResource::collection(HotelRooms::filter($filter)->get());
    }

    public function show($roomId)
    {
        $room = HotelRooms::findOrFail($roomId);


        return new HotelRoomsResource($room);
    }

    public function store(StoreHotelRoomRequest $request)
    {
        if (Auth::user()->can('Post Rooms')) {
            $room = $this->createHotelRoomService->createEntity($request);
            return new HotelRoomsResource($room);

        } else return $this->error(["You're not authorized to store rooms to this hotel"], 403);
    }

    public function update(UpdateHotelRoomRequest $request, $roomId)
    {
        $room = HotelRooms::findOrFail($roomId);
        if (Auth::user()->id === $room->hotel->owner->user_id) {
            $pendingUpdate = $this->updateHotelRoomService->updateHotelRoom($room, $request);
            
            return response()->json([
                'message' => 'Update request submitted for approval',
                'pending_update' => new PendingUpdateResource($pendingUpdate)
            ]);
        } else {
            return response()->json([
                "You are not authorized to update this resource."
            ]);
        }
    }

    public function getOwnerRooms($userId)
    {
        $user = User::findOrFail($userId);
        if (!$user->hasRole('Hotel Owner')) {
            return $this->error([
                'The user you are trying to reach is not hotel owner'
            ], 403);
        }
        $hotels = $user->owner->hotel;
        $rooms = $hotels?->flatMap->hotelRooms;

        if (!$hotels || !$rooms) {
            return response()->json([
                "This owner has no rooms"
            ]);
        }

        return HotelRoomsResource::collection($rooms);
    }

    public function getOwner($roomId)
    {
        $room = HotelRooms::find($roomId);
        return $room->hotel->user;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($hotelRoomId)
    {
        $hotelRoom = HotelRooms::findOrFail($hotelRoomId);

        if (Auth::user()->hasRole("Hotel Owner") && $hotelRoom->hotel->owner->user_id == Auth::user()->id) {
            try {
                $hotelRoom->delete();
                return response()->json([
                    "message" => "Room deleted successfully"
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    "message" => 'Failed to delete the room. ' . $e->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                "message" => 'You are not authorized to delete the room. ',
            ], 500);
        }
    }
}
