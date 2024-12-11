<?php

namespace App\Models;

use Database\Factories\AccommodationAmenitiesFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AccommodationAmenities extends Model
{
    /** @use HasFactory<AccommodationAmenitiesFactory> */
    use HasFactory;

    protected $guarded = [];
    public function servable(): MorphTo
    {
        return $this->morphTo();
    }
}
