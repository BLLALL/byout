<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreTourRequest;
use App\Http\Resources\Api\V1\TourResource;
use App\Models\Tour;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery\Exception;

class TourController extends Controller
{
    /**
     * Get Tours
     *
     * @group Managing Tours
     * @queryParam sort string Data field to sort by. Separate multiple parameters with commas. Denote descending order with a minus sign.
     * Example: departure_time, -created_at
     * @queryParam price integer Data field to filter tours by their price u can use comma to filter by range. Example: 2000,100000
     *
     */
    public function index()
    {
        return TourResource::collection(Tour::get());
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

    
    public function store(StoreTourRequest $request)
    {


        return new TourResource(Tour::create($request->mappedAttributes()));
    }
}
