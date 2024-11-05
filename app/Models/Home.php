<?php

namespace App\Models;

use App\Http\filters\QueryFilter;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Illuminate\Database\Eloquent\Builder;

class Home extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'coordinates' => 'array',
        'home_images' => 'array',
    ];

    protected $guarded = ['avg_rating', 'rating_count', 'popularity_score'];


    protected $attributes = [
        'currency' => 'SYP'
    ];
    

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function users()
    {
        return $this->morphToMany(User::class, 'favorable');
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function rentals()
    {
        return $this->morphMany(Rental::class, 'rentable');
    }

    public function pendingUpdates() 
    {
        return $this->morphMany(PendingUpdates::class, 'updatable');
    }

    public function scopeFilter(Builder $builder, QueryFilter $filter)
    {
        return $filter->apply($builder);
    }


    public function getPriceAttribute($value)
    {
        return Money::ofMinor($value, $this->currency, roundingMode: RoundingMode::UP)->getAmount()->toFloat();
    }    


    public function getDiscountPriceAttribute($value)
    {
        return $value ? Money::ofMinor($value, $this->currency,  roundingMode: RoundingMode::UP)->getAmount()->toFloat() : null;
    }

    public function updateReviewStatistics()
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

        $this->saveQuietly();
    }

    public function getPopularityScore()
    {
        if ($this->rating_count > 0) {
            return $this->avg_rating / sqrt($this->rating_count);
        }
        return 0.0;
    }



    public function getAvgRatingAttribute($value)
    {
        return round($value / 100, 2);
    }

    public function setAvgRatingAttribute($value)
    {
        $this->attributes['avg_rating'] = $value * 100;
    }

}
