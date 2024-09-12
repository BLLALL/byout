<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tour;
use App\Models\Owner;
use Illuminate\Database\Eloquent\Builder;
use App\Http\filters\QueryFilter;

class Vehicle extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'has_wifi' => 'boolean',
        'has_air_conditioner' => 'boolean',
        'has_gps' => 'boolean',
        'has_movie_screens' => 'boolean',
        'vehicle_images' => 'array',
    ];

    public function scopeFilter(Builder $builder, QueryFilter $filter)
    {
        return $filter->apply($builder);
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function tour()
    {
        return $this->hasMany(Tour::class);
    }

    public function isAvailable()
    {
        return $this->status === 'available';
    }
}
