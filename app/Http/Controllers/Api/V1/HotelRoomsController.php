<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\filters\HotelRoomsFilter;
use App\Http\Requests\Api\V1\StoreHotelRoomRequest;
use App\Http\Requests\Api\V1\UpdateHotelRoomRequest;
use App\Http\Resources\Api\V1\HotelRoomsResource;
use App\Http\Resources\Api\V1\PendingUpdateResource;
use App\Models\HotelRooms;
use App\Models\User;
use App\Services\CreateHotelRoomService;
use App\Services\CurrencyRateExchangeService;
use App\Services\HotelRoomService;
use App\traits\apiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;


class HotelRoomsController extends Controller
{
    use apiResponses;


    protected CreateHotelRoomService $createHotelRoomService;
    protected HotelRoomService $updateHotelRoomService;
    protected CurrencyRateExchangeService $currencyRateExchangeService;

    public function __construct(CreateHotelRoomService $createHotelRoomService, HotelRoomService $updateHotelRoomService, CurrencyRateExchangeService $currencyRateExchangeService)
    {
        $this->createHotelRoomService = $createHotelRoomService;
        $this->updateHotelRoomService = $updateHotelRoomService;
        $this->currencyRateExchangeService = $currencyRateExchangeService;
    }

    public function index(HotelRoomsFilter $filter): JsonResource
    {
        $rooms = HotelRooms::filter($filter)->get();

        $userCurrency = Auth::user()->preferred_currency;
        $rooms = $this->currencyRateExchangeService->convertEntityPrice($rooms, $userCurrency);
        $rooms = $rooms->filter(function($room) {
            return $this->filterByPrice($room);
        });
        return HotelRoomsResource::collection($rooms);
    }

    public function show($roomId): JsonResource
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

    public function getOwnerRooms($userId, HotelRoomsFilter $hotelRoomsFilter): JsonResponse|AnonymousResourceCollection
    {
        $user = User::findOrFail($userId);
        if (!$user->hasRole('Hotel Owner')) {
            return $this->error([
                'The user you are trying to reach is not hotel owner'
            ], 403);
        }
        $hotels = $user->owner->hotel;
        $rooms = HotelRooms::whereIn('hotel_id', $hotels->pluck('id'))->filter($hotelRoomsFilter)->get();


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
    public function destroy($hotelRoomId): JsonResponse
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
                "message" => 'You are not authorized to delete the room.',
            ], 500);
        }
    }

    private function filterByPrice(HotelRooms $room): bool
    {
        $priceFilter = request('price');

        if (!$priceFilter) return true;

        $prices = explode(',', $priceFilter);

        if (count($prices) > 1)
            return $room->price >= $prices[0] && $room->price <= $prices[1];
        return $room->price <= $prices[0];
    }

}
