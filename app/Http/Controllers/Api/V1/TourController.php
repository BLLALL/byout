<?php

namespace App\Http\Controllers;

use App\Http\Resources\Api\V1\TourResource;
use App\Models\Tour;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function index() {
        return TourResource::collection(Tour::get());
    }

    public function show(Tour $tour)
    {
        return new TourResource($tour);
    }
}
