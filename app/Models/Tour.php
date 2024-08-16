<?php

namespace App\Models;

use App\Http\filters\QueryFilter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    use HasFactory;

    protected $casts = [
        'seat_status' => 'array'
    ];

    protected $guarded = [];

    public function scopeFilter(Builder $builder, QueryFilter $filter)
    {
        return $filter->apply($builder);
    }

    public function tourCompany()
    {
        return $this->belongsTo(TourCompany::class);
    }

    public function tourReservations() {
        return $this->hasMany(TourReservation::class);
    }


    protected static function booted()
    {
        static::creating(function ($tour) {
            $tour->time_difference = self::calculateTimeDifference($tour->departure_time, $tour->arrival_time);
        });
        static::updating(function ($tour) {
            $tour->time_difference = self::calculateTimeDifference($tour->departure_time, $tour->arrival_time);
        });
    }

    // Marking the method as static
    protected static function calculateTimeDifference($departureTime, $arrivalTime)
    {
        $departure = Carbon::parse($departureTime);
        $arrival = Carbon::parse($arrivalTime);

        $hours = abs($arrival->diffInHours($departure));
        $minutes = abs($arrival->diffInMinutes($departure) % 60);

        return sprintf('%02d:%02d', $hours, $minutes);
    }

}
