<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\filters\TourFilter;
use App\Http\Requests\Api\V1\ScheduleTourRequest;
use App\Http\Requests\Api\V1\updateTourRequest;
use App\Http\Resources\Api\V1\TourResource;
use App\Models\Vehicle;
use App\Models\Tour;
use App\Services\CreateTourService;
use App\Services\TourService;
use App\traits\apiResponses;
use Illuminate\Support\Facades\Auth;
use App\Services\CurrencyRateExchangeService;
use Carbon\Carbon;

class TourController extends Controller
{
    use apiResponses;

    /**
     * Get Tours
     * Show all Tours
     * @group Managing Tours
     * @queryParam sort string Data field to sort by. Separate multiple parameters with commas. Denote descending order with a minus sign.
     * Example: departure_time, -created_at
     * @queryParam price integer Data field to filter tours by their price u can use comma to filter by range. Example: 2000,100000
     *
     */

    protected TourService $tourService;
    protected CreateTourService $createTourService;

    protected CurrencyRateExchangeService $currencyRateExchangeService;


    public function __construct(TourService $tourService, CreateTourService $createTourService, CurrencyRateExchangeService $currencyRateExchangeService)
    {
        $this->tourService = $tourService;
        $this->createTourService = $createTourService;
        $this->currencyRateExchangeService = $currencyRateExchangeService;
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

        $userTimeZone = Auth::user()->timezone;

        $tourData['departure_time'] = Carbon::parse($request->departure_time, $userTimeZone)->utc();
        $tourData['arrival_time'] = Carbon::parse($request->arrival_time, $userTimeZone)->utc();

        $vehicle = Vehicle::findOrFail($tourData['vehicle_id']);
        $ownerId = Auth::user()->owner->id;
        $userId = $vehicle->owner->user_id;
        $user = $vehicle->owner->user;

        if (Auth::user()->id != $userId || !($user->hasRole('Tour Company Owner'))) {
            return response()->json([
                "message" => "you are not authorized to schedule this tours"
            ], 400);
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

        $userTimezone = Auth::user()->timezone;

        $tours = Tour::filter($filter)->get();

        $userCurrency = Auth::user()->preferred_currency;

        $currencyRateExchangeService = $this->currencyRateExchangeService;

        $tours = $tours->map(function ($tour) use ($userCurrency, $currencyRateExchangeService, $userTimezone) {
            if ($tour->currency != $userCurrency) {
                $money = $currencyRateExchangeService->convertPrice($tour->currency, $userCurrency, $tour->price);
                $tour->price = $money->getAmount()->toFloat();
                $tour->currency = $money->getCurrency();
            }
            // $tour->departure_time = $tour->departure_time->setTimezone($userTimezone);
            // $tour->arrival_time = $tour->arrival_time->setTimezone($userTimezone);
            return $tour;   
        });

        return TourResource::collection($tours);
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
        $userTimezone = Auth::user()->timezone;
    
        $tour->departure_time = Carbon::parse($tour->departure_time)->timezone($userTimezone);
        $tour->arrival_time = Carbon::parse($tour->arrival_time)->timezone($userTimezone);
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
