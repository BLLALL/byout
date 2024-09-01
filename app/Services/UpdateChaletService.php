<?php

namespace App\Services;

use App\Models\Chalet;
use Illuminate\Http\Request;

class ChaletService extends UpdateEntityService
{
    public function updateChalet(Chalet $chalet, Request $request)
    {
        $fillableAttributes = [
            'title', 'price', 'area',
            'location', 'wifi', 'coordinates',
            'air_conditioning', 'sea_view', 'distance_to_beach',
            'available_until', 'max_occupancy', 'rent_period',
            'bathrooms_no', 'bedrooms_no',
            'description', 'is_reserved'
        ];

        $this->updateEntity($chalet, $request, $fillableAttributes, 'chalet_images');
    }
}
