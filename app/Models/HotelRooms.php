<?php

namespace App\Models;

use App\Http\filters\QueryFilter;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class HotelRooms extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $casts = [
        'room_images' => 'array',
        'available_from' => 'date',
        'available_until' => 'date',
    ];

    protected $attributes = [
        'currency' => 'SYP'
    ];
    
    
    protected $guarded = [];

    public function scopeFilter(Builder $builder, QueryFilter $filter)
    {
        return $filter->apply($builder);
    }
    
    public function scopeAvailableBetween($query, $fromDate, $toDate)
    {
        return $query->where('available_until', '>=',  $toDate)
        ->where('available_from', '<=', $fromDate)
        ->whereDoesntHave('rentals', function($q) use($fromDate, $toDate) {
            $q->where('check_out', '>', $fromDate)
            ->where('check_in', '<', $toDate);
        });
    }
    public function hotel() {
        return $this->belongsTo(Hotel::class);
    }
    
    public function rentals()
    {
        return $this->morphMany(Rental::class, 'rentable');
    }

    public function pendingUpdates() 
    {
        return $this->morphMany(PendingUpdates::class, 'updatable');
    }

    public function getPriceAttribute($value)
    {
        return Money::ofMinor($value, $this->currency, roundingMode: RoundingMode::UP)->getAmount()->toFloat();
    }    


    public function getDiscountPriceAttribute($value)
    {
        return $value ? Money::ofMinor($value, $this->currency,  roundingMode: RoundingMode::UP)->getAmount()->toFloat() : null;
    }

}