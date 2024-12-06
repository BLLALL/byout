<?php

namespace App\Models;

use App\Http\filters\QueryFilter;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tour extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'seat_status' => 'array'
    ];

    protected $guarded = [];

    public function scopeFilter(Builder $builder, QueryFilter $filter)
    {
        return $filter->apply($builder);
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function tourCompany()
    {
        return $this->belongsTo(TourCompany::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function tourReservations()
    {
        return $this->hasMany(TourReservation::class)->chaperone();
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

    public function getPriceAttribute($value): float
    {
        return Money::ofMinor($value, $this->currency, roundingMode: RoundingMode::UP)->getAmount()->toFloat();
    }

//    public function getDiscountPriceAttribute($value)
//    {
//        return $value ? Money::ofMinor($value, $this->currency, roundingMode: RoundingMode::UP)->getAmount()->toFloat() : null;
//    }

    protected static function calculateTimeDifference($departureTime, $arrivalTime)
    {
        $departure = Carbon::parse($departureTime);
        $arrival = Carbon::parse($arrivalTime);

        $hours = abs($arrival->diffInHours($departure));
        $minutes = abs($arrival->diffInMinutes($departure) % 60);

        return sprintf('%02d:%02d', $hours, $minutes);
    }

}
