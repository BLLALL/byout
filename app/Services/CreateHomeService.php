<?php

namespace App\Services;


use App\Models\Home;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CreateHomeService extends CreateEntityService
{

    public function getModel()
    {
        return new Home();
    }

    protected function getFillableAttributes()
    {
        return [
            'title', 'description', 'price', 'living_room_no', 'kitchen_no',
            'area', 'bathrooms_no', 'bedrooms_no', 'location',
            'wifi', 'coordinates', 'rent_period', 'available_from', 'available_until'
        ];
    }

    protected function getImageColumn()
    {
        return 'home_images';
    }

    protected function getImagePath()
    {
        return 'home_images';
    }

}
