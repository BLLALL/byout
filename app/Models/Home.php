<?php

namespace App\Models;

use App\Http\filters\QueryFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Builder;

class Home extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }


    public function HomeImages()
    {
        return $this->hasMany(HomeImage::class);
    }

    public function scopeFilter(Builder $builder, QueryFilter $filter) {
        return $filter->apply($builder);
    }

    public function calcAvgRatingAndCount()
    {
        $this->avg_rating = $this->reviews()->avg('rating') ?? 0;
        $this->rating_count = $this->reviews()->count();
        $this->save();
    }

    public function getAvgRatingAttribute($value) {
        return round($value / 100, 2);
    }

    public function setAvgRatingAttribute($value)
    {
        $this->attributes['avg_rating'] = $value * 100;
    }
}
