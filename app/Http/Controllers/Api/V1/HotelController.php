<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\filters\HotelFilter;
use App\Http\Requests\Api\V1\BaseHotelRoomRequest;
use App\Http\Requests\Api\V1\StoreHotelRequest;
use App\Http\Requests\Api\V1\UpdateHotelRequest;
use App\Http\Resources\Api\V1\HotelResource;
use App\Http\Resources\Api\V1\PendingUpdateResource;
use App\Models\Hotel;
use App\Models\HotelRooms;
use App\Models\User;
use App\Services\UpdateHotelService;
use App\traits\apiResponses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\CreateHotelService;
use App\Services\CurrencyRateExchangeService;

class HotelController extends Controller
{
    use apiResponses;

    protected UpdateHotelService $hotelService;

    protected CreateHotelService $createHotelService;

    protected CurrencyRateExchangeService $currencyRateExchangeService;

    public function __construct(UpdateHotelService $hotelService, CreateHotelService $createHotelService, CurrencyRateExchangeService $currencyRateExchangeService)
    {
        $this->hotelService = $hotelService;
        $this->createHotelService = $createHotelService;
        $this->currencyRateExchangeService = $currencyRateExchangeService;
    }

    /**
     * Get Hotels
     *
     * @group Managing Hotels
     * @queryParam sort string Data field to sort by. Separate multiple parameters with commas. Denote descending order with a minus sign.
     * Example: title, -created_at
     * @queryParam price integer Data field to filter hotels by their price u can use comma to filter by range. Example: 2000,100000
     * @queryParam title string Data field to search for hotels by their title. Example:Lorem
     * @queryParam description string Data field to search for hotels by their description. Example:Lorem Ipsum
     *
     */
    public function index(HotelFilter $filter)
    {
        return HotelResource::collection(Hotel::filter($filter)->get());
    }

    /**
     * Create a Hotel
     *
     * @group Managing Hotels
     *
     */
    public function store(StoreHotelRequest $request)
    {
        DB::beginTransaction();
        try {
            if (!Auth::user()->can('Post Hotels') || $request->input('owner_id') != Auth::user()->id) {
                return $this->error(["You're not authorized to store that hotel"], 403);
            }

            $hotel = $this->createHotelService->createEntity($request);

            DB::commit();
            return new HotelResource($hotel->load('hotelRooms'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
        }
    }
    /**
     * show a specific Hotel
     *
     *Display an individual Hotel
     *
     * @group Managing Hotels
     */
    public function show(Hotel $Hotel)
    {
        return new HotelResource($Hotel);
    }

    public function hotelDetails($ownerId)
    {
        $owner = User::find($ownerId);
        if (!$owner->hasRole('Hotel Owner')) {
            return $this->error([
                'The user you are trying to reach is not hotel owner'
            ], 403);
        }
        return $owner->hotel;
    }


    public function update(UpdateHotelRequest $request, Hotel $hotel)
    {
        if (Auth::user()->id === $hotel->owner->user_id) {
            $pendingUpdate = $this->hotelService->updateHotel($hotel, $request);
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

    /**
     * Remove the specified resource from storage.
     * 
     */
    public function destroy(Hotel $hotel)
    {
        if (Auth::user()->hasRole("Hotel Owner") && $hotel->owner->user_id == Auth::user()->id) {
            try {
                $hotel->delete();
                return response()->json([
                    "message" => "Hotel deleted successfully"
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    "message" => 'Failed to delete the hotel. ' . $e->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                "message" => 'You are not authorized to delete the hotel. ',
            ], 500);
        }
    }
}
