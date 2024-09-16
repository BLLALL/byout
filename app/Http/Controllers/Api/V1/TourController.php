<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\filters\TourFilter;
use App\Http\Requests\Api\V1\ScheduleTourRequest;
use App\Http\Requests\Api\V1\StoreTourRequest;
use App\Http\Resources\Api\V1\TourResource;
use App\Models\Vehicle;
use App\Models\Owner;
use App\Models\Tour;
use App\Services\TourService;
use App\traits\apiResponses;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;

class TourController extends Controller
{
    use apiResponses;

    /**
     * Get Tours
     * Show all Tours`
     * @group Managing Tours
     * @queryParam sort string Data field to sort by. Separate multiple parameters with commas. Denote descending order with a minus sign.
     * Example: departure_time, -created_at
     * @queryParam price integer Data field to filter tours by their price u can use comma to filter by range. Example: 2000,100000
     *
     */

    protected TourService $tourService;

    public function __construct(TourService $tourService)
    {
        $this->tourService = $tourService;
    }


    public function schedule(ScheduleTourRequest $request)
    {
        $tourData = $request->only([
            'price', 'tour_type', 'source', 'destination', 'departure_time',
            'arrival_time', 'recurrence', 'ownership', 'vehicle_id', 'driver_id'
        ]);

        $vehicle = Vehicle::findOrFail($tourData['vehicle_id']);
        $ownerId = $vehicle->owner_id;
        $userId = $vehicle->owner->user_id;
        $user = $vehicle->owner->user;

        if (Auth::user()->id != $userId || !($user->hasRole('Tour Company Owner'))) {
            return response()->json([
                    "message" => "you are not authorized to schedule this tours"
                ]);
        }
        $tourData['owner_id'] = $ownerId;
        $tour = new Tour($tourData);
        if ($tourData['recurrence'])
            $this->tourService->createRecurringTour($tour, $tour['recurrence']);
        try {
            $this->tourService->scheduleTour($tour);
            return response()->json([
                'message' => 'Tour successfully scheduled',
                'tour' => new TourResource($tour)
            ], 200);
        } catch (\Exception $exception) {
           return $exception;
        }
    }
    public function index(TourFilter $filter)
    {
        return TourResource::collection(Tour::filter($filter)->get());
    }

    /**
     * show a specific Tour
     *
     *Display an individual tour
     *
     * @group Managing Tours
     */
    public function show(Tour $tour)
    {
        return new TourResource($tour);
    }

    /**
     * Create a Tour
     *
     * @group Managing Tours
     *
     *
     */
    public function store(StoreTourRequest $request)
    {

        if (Auth::user()->can('Post Tours')) {
            return new TourResource(Tour::create($request->mappedAttributes()));
        } else {
            return $this->error([
                "you aren't authorized to store this tour."
            ], 403);
        }
    }

    public function delete(Tour $tour)
    {
        if(Auth::hasRole('Tour Company Owner') && $tour->owner->user_id == Auth::user()->id) {

        }
    }


}
