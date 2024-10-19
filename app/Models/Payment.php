<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payable_type',
        'payable_id',
        'payment_method',
        'payment_status',
        'amount',
        'currency',
        'payment_id',
        'payment_url',
    ];

    public function payable()
    {
        return $this->morphTo();
    }

}

