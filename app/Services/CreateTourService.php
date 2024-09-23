<?php

namespace App\Services;

use App\Models\Tour;

class CreateTourService extends CreateEntityService
{
    public function __construct()
    {
        
    }

    public function getModel()
    {
        return new Tour();
    }

    public function getFillableAttributes()
    {
        return [
            'price',
            'currency',
            'tour_type',
            'source',
            'destination',
            'departure_time',
            'arrival_time',
            'recurrence',
            'vehicle_id',
            'drivers_id',
        ];
    }

    
}
