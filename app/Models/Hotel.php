<?php

namespace App\Models;

use App\Http\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Hotel extends Model
{
    use HasFactory;

    protected $casts = [
        'coordinates' => 'array',
        'hotel_images' => 'array',
    ];

    protected $guarded = ['avg_rating', 'rating_count', 'popularity_score'];

    public function scopeFilter(Builder $builder, QueryFilter $filter): Builder
    {
        return $filter->apply($builder);
    }

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
        return $this->morphToMany(User::class, 'favorable', 'favourites');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }


    public function hotelRooms(): HasMany
    {
        return $this->hasMany(HotelRooms::class);
    }

    public function pendingUpdates(): MorphMany
    {
        return $this->morphMany(PendingUpdates::class, 'updatable');
    }

    public function prunable(): Builder
    {
        return static::where('created_at', '<=', now()->subMonth(6));
    }

    public function getAvgRatingAttribute($value)
    {
        return round($value / 100, 2);
    }

    public function setAvgRatingAttribute($value): void
    {
        $this->attributes['avg_rating'] = $value * 100;
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

        $this->saveQuietly();
    }

    public function getPopularityScore(): float|int
    {
        if ($this->rating_count > 0) {
            return $this->avg_rating / sqrt($this->rating_count);
        }
        return 0.0;
    }

}
