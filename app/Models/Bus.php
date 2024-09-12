<?php

namespace App\Models;

use App\Http\filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    protected $casts = [
        'bus_images' => 'array'
    ];
    use HasFactory;

    protected $guarded = [];

    public function scopeFilter(Builder $builder, QueryFilter $filter)
    {
        return $filter->apply($builder);
    }

    public function tour()
    {
        return $this->hasMany(Tour::class);
    }


    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
