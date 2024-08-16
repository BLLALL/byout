<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\filters\HotelFilter;
use App\Http\Requests\Api\V1\BaseHotelRoomRequest;
use App\Http\Requests\Api\V1\storeHotelRequest;
use App\Http\Resources\Api\V1\HotelResource;
use App\Models\Hotel;
use App\Models\HotelRooms;
use App\Models\User;
use App\traits\apiResponses;
use Illuminate\Support\Facades\DB;

class HotelController extends Controller
{
    use apiResponses;

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
     *
     */
    public function store(storeHotelRequest $request)
    {
        DB::beginTransaction();
        try {
            $hotel = Hotel::create($request->mappedAttributes());
            if ($request->has('rooms')) {
                collect($request->input('rooms'))->map(function () use ($hotel) {
                    return $hotel->hotelRooms()->create((new BaseHotelRoomRequest)->mappedAttributes());
                });
            }
            DB::commit();
            return new HotelResource($hotel->load('hotelRooms'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([
                'failed to create hotel or rooms'
            ], 500);
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
        return new HotelResource($owner->hotel);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hotel $Hotel)
    {
        //
    }
}
