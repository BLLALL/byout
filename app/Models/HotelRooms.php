<?php

namespace App\Models;

use App\Http\filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelRooms extends Model
{
    use HasFactory;
    protected $casts = [
        'room_images' => 'array',
    ];
    protected $guarded = [];

    public function scopeFilter(Builder $builder, QueryFilter $filter)
    {
        return $filter->apply($builder);
    }
    public function hotel() {
        return $this->belongsTo(Hotel::class);
    }
}
