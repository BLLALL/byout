<?php

namespace App\Models;

use App\Http\filters\QueryFilter;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
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

    public function scopeFilter(Builder $builder, QueryFilter $filter): Builder
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
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function rentals(): MorphMany
    {
        return $this->morphMany(Rental::class, 'rentable');
    }

    public function pendingUpdates(): MorphMany
    {
        return $this->morphMany(PendingUpdates::class, 'updatable');
    }

    public function getPriceAttribute($value): float
    {
        return Money::ofMinor($value, $this->currency, roundingMode: RoundingMode::UP)->getAmount()->toFloat();
    }

    public function getDiscountPriceAttribute($value)
    {
        return $value ? Money::ofMinor($value, $this->currency,  roundingMode: RoundingMode::UP)->getAmount()->toFloat() : null;
    }

}