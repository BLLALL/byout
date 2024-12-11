<?php

namespace App\Models;

use App\Http\Filters\QueryFilter;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chalet extends Model
{
    use HasFactory, SoftDeletes, Prunable;

    protected $casts = [
        'coordinates' => 'array',
        'chalet_images' => 'array',
        'available_from' => 'date',
        'available_until' => 'date',
    ];

    protected $guarded = ['avg_rating', 'rating_count', 'popularity_score'];

    protected $attributes = [
        'currency' => 'SYP'
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }


    public function users(): MorphToMany
    {
        return $this->morphToMany(User::class, 'favorable');
    }

    public function roomBeds(): MorphMany
    {
        return $this->MorphMany(RoomBed::class, 'bedable');
    }


    public function AccommodationAmenities(): MorphMany
    {
        return $this->MorphMany(AccommodationAmenities::class, 'servable');
    }


    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function rentals(): MorphMany
    {
        return $this->morphMany(Rental::class, 'rentable');
    }

    public function pendingUpdates(): MorphMany
    {
        return $this->morphMany(PendingUpdates::class, 'updatable');
    }

    public function scopeFilter(Builder $builder, QueryFilter $filter): Builder
    {
        return $filter->apply($builder);
    }

    public function getPriceAttribute($value): float
    {
        return Money::ofMinor($value, $this->currency, roundingMode: RoundingMode::UP)->getAmount()->toFloat();
    }


    public function getDiscountPriceAttribute($value): ?float
    {
        return $value ? Money::ofMinor($value, $this->currency, roundingMode: RoundingMode::UP)->getAmount()->toFloat() : null;
    }


    public function updateReviewStatistics(): void
    {
        $avgRating = Review::where('reviewable_type', get_class($this))
            ->where('reviewable_id', $this->id)
            ->avg('rating') ?? 0;

        $ratingCount = Review::where('reviewable_type', get_class($this))
            ->where('reviewable_id', $this->id)
            ->count();

        $this->avg_rating = $avgRating;
        $this->rating_count = $ratingCount;
        $this->popularity_score = $this->getPopularityScore();

        $this->save();
    }

    public function getPopularityScore(): float|int
    {
        if ($this->rating_count > 0) {
            return $this->avg_rating / sqrt($this->rating_count);
        }
        return 0.0;
    }


    public function getAvgRatingAttribute($value): float
    {
        return round($value / 100, 2);
    }

    public function setAvgRatingAttribute($value): void
    {
        $this->attributes['avg_rating'] = $value * 100;
    }


    public function setAvailableUntil($value): void
    {
        $date = Carbon::parse($value);
        $this->attributes['available_until'] = $date->isFuture() ? $date : null;
    }

    public function prunable(): Builder
    {
        return static::where('deleted_at', '<=', now()->subMonths(6));
    }
}

