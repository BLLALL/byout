<?php

namespace App\Services;

use App\Models\Home;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UpdateHomeService extends UpdateEntityService
{
    public function updateHome(Home $home, Request $request)
    {
        $fillableAttributes = [
            'title', 'description', 'price', 'area',
            'bathrooms_no', 'bedrooms_no', 'location',
            'wifi', 'coordinates', 'rent_period'
        ];

        $this->updateEntity($home, $request, $fillableAttributes, 'home_images');
    }


}
