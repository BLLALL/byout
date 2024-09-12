<?php

namespace App\Services;

use App\Models\Chalet;

class CreateChaletService extends CreateEntityService
{
    public function getModel()
    {
        return new Chalet();
    }

    protected function getFillableAttributes()
    {
        return [
            'title', 'description', 'price',
            'area', 'bathrooms_no', 'bedrooms_no', 'location',
            'wifi', 'coordinates', 'rent_period', 'air_conditioning',
            'sea_view', 'distance_to_beach', 'available_until',
            'max_occupancy', 'available_from', 'available_until'
        ];
    }

    protected function getImageColumn()
    {
        return 'chalet_images';
    }

    protected function getImagePath()
    {
        return 'chalet_images';
    }
}
