<?php

namespace App\Models;

use App\Http\filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class HotelRooms extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $casts = [
        'room_images' => 'array',
        'available_from' => 'date',
        'available_until' => 'date',
    ];
    
    protected $guarded = [];

    public function scopeFilter(Builder $builder, QueryFilter $filter)
    {
        return $filter->apply($builder);
    }
    public function hotel() {
        return $this->belongsTo(Hotel::class);
    }
    
    public function rentals()
    {
        return $this->morphMany(Rental::class, 'rentable');
    }

    public function pendingUpdates() 
    {
        return $this->morphMany(PendingUpdates::class, 'updatable');
    }

    

}
