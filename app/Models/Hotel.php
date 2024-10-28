<?php
namespace App\Models;

use App\Http\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Hotel extends Model
{
    use HasFactory;

    protected $casts = [
        'coordinates' => 'array',
        'hotel_images' => 'array',
    ];

    protected $guarded = ['avg_rating', 'rating_count', 'popularity_score'];

    public function scopeFilter(Builder $builder, QueryFilter $filter)
    {
        return $filter->apply($builder);
    }

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
        return $this->morphToMany(User::class, 'favorable', 'favourites');
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }


    public function hotelRooms(): HasMany
    {
        return $this->hasMany(HotelRooms::class);
    }

    public function pendingUpdates() 
    {
        return $this->morphMany(PendingUpdates::class, 'updatable');
    }
    

    public function getAvgRatingAttribute($value)
    {
        return round($value / 100, 2);
    }

    public function setAvgRatingAttribute($value)
    {
        $this->attributes['avg_rating'] = $value * 100;
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

}
