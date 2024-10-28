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
    public function rentable()
    {
        return $this->morphTo();
    }

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
}
