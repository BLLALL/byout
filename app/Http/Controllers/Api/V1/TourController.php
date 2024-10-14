<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\filters\TourFilter;
use App\Http\Requests\Api\V1\ScheduleTourRequest;
use App\Http\Requests\Api\V1\StoreTourRequest;
use App\Http\Requests\Api\V1\updateTourRequest;
use App\Http\Resources\Api\V1\TourResource;
use App\Models\Vehicle;
use App\Models\Owner;
use App\Models\Tour;
use App\Services\CreateTourService;
use App\Services\TourService;
use App\traits\apiResponses;
use Illuminate\Support\Facades\Auth;

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
    protected CreateTourService $createTourService;

    public function __construct(TourService $tourService, CreateTourService $createTourService)
    {
        $this->tourService = $tourService;
        $this->createTourService = $createTourService;
    }


    /**
     *
     * @group Managing Tours
     * Schedule a new Tour
     *
     */
    public function schedule(ScheduleTourRequest $request)
    {
        $tourData = $request->only([
            'price',
            'tour_type',
            'source',
            'destination',
            'departure_time',
            'arrival_time',
            'recurrence',
            'vehicle_id',
            'driver_id'
        ]);

        $vehicle = Vehicle::findOrFail($tourData['vehicle_id']);
        $ownerId = Auth::user()->owner->id;
        $userId = $vehicle->owner->user_id;
        $user = $vehicle->owner->user;

        if (Auth::user()->id != $userId || !($user->hasRole('Tour Company Owner'))) {
            return response()->json([
                "message" => "you are not authorized to schedule this tours"
            ]);
        }
        $tourData['owner_id'] = $ownerId;
        $tour = new Tour($tourData);
        if (isset($tourData['recurrence']))
            $this->tourService->createRecurringTour($tour, $tour['recurrence']);
        try {
            $this->createTourService->setCurrency($tour, $request, Auth::user()->owner);
            $this->tourService->scheduleTour($tour);
            $this->createTourService->handleDocuments($tour, $request);
            return response()->json([
                'message' => 'Tour successfully scheduled',
                'tour' => new TourResource($tour)
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                "message" => $exception->getMessage(),
            ]);
        }
    }

    public function getAvailableSeats()
    {
        // return $this
    }
    /**
     * Get Tours
     * Show all Tours`
     * @group Managing Tours
     * @queryParam sort string Data field to sort by. Separate multiple parameters with commas. Denote descending order with a minus sign.
     * Example: departure_time, -created_at
     * @queryParam price integer Data field to filter tours by their price u can use comma to filter by range. Example: 2000,100000
     *
     */
    public function index(TourFilter $filter)
    {
        $tours = Tour::filter($filter)->get();
        $tours = $tours->map(function ($tour) {});

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
     * update a specific tours
     *
     * @group Managing
     *
     */
    public function update(updateTourRequest $request, Tour $tour)
    {

        if (!Auth::user()->id == $tour->owner->user_id) {
            return response()->json([
                "message" => "you are not authorized to update this tour"
            ]);
        }
        $tour->update($request->only(
            [
                'price',
                'tour_type',
                'source',
                'destination',
                'departure_time',
                'arrival_time',
                'recurrence',
                'vehicle_id',
                'driver_id'
            ]
        ));
        return new TourResource($tour);
    }

    public function destroy(Tour $tour)
    {
        if (!Auth::user()->id == $tour->owner->user_id) {
            return response()->json([
                "message" => "you are not authorized to delete this tour"
            ]);
        }
        $tour->delete();
        return response()->json([
            "message" => "tour deleted successfully"
        ]);
    }
}
