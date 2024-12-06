<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Owner;

class Rental extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
    ];

    const PAYMENT_PENDING = 'pending';
    const PAYMENT_ACCEPTED = 'accepted';
    const PAYMENT_FAILED = 'failed';
    const PAYMENT_CANCELLED = 'cancelled';


    public function payment()
    {
        return $this->morphOne(Payment::class, 'payable');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function hotelRooms()
    {
        return $this->belongsToMany(HotelRooms::class, 'rental_hotel_rooms', 'rental_id', 'hotel_room_id');
    }

    public function rentable()
    {
        if ($this->rentable_type == 'App\Models\HotelRooms') {
            return $this->hotelRooms();
        }
        return $this->morphTo();
    }
}
