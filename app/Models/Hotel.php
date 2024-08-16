<?php

namespace App\Models;

use App\Http\filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hotel extends Model
{
    use HasFactory;

    protected $casts = [
        'coordinates' => 'array',
        'hotel_images' => 'array',
    ];
    protected $guarded = [];


    public function scopeFilter(Builder $builder, QueryFilter $filter)
    {
        return $filter->apply($builder);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function hotelRooms(): HasMany
    {
        return $this->hasMany(HotelRooms::class);
    }

    public function setRoomsCount()
    {
        $this->hotel_rooms = $this->hotelRooms->count();
    }



}
