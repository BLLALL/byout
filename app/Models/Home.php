<?php

namespace App\Models;

use App\Http\filters\QueryFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Builder;

class Home extends Model
{
    use HasFactory;

    protected $casts = [
        'coordinates' => 'array',
        'home_images' => 'array',
    ];

    protected $guarded = [];

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function users()
    {
        return $this->belongsToMany(User::class, 'home_favourites',
            'home_id', 'user_id');
    }

    public function scopeFilter(Builder $builder, QueryFilter $filter)
    {
        return $filter->apply($builder);
    }

    public function getPopularityScore()
    {
        if ($this->rating_count > 0) {
            return $this->avg_rating / sqrt($this->rating_count);
        }
        return 0;
    }

    public function calcAvgRatingAndCount()
    {
        $this->avg_rating = $this->reviews()->avg('rating') ?? 0;
        $this->rating_count = $this->reviews()->count();
        $this->popularity_score = $this->getPopularityScore();
        $this->save();
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
