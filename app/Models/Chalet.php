<?php
namespace App\Models;

use App\Http\Filters\QueryFilter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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
    public function scopeFilter(Builder $builder, QueryFilter $filter)
    {
        return $filter->apply($builder);
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

        $this->save();
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



    public function setAvailableUntil($value): void
    {
        $date = Carbon::parse($value);
        $this->attributes['available_until'] = $date->isFuture() ? $date : null;
    }

    public function prunable()
    {
        return static::where('deleted_at', '<=', now()->subMonths(6));
    }
}

