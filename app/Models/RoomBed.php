<?php

namespace App\Models;

use Database\Factories\RoomBedFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class RoomBed extends Model
{
    /** @use HasFactory<RoomBedFactory> */
    use HasFactory;

    protected $guarded = [];

    public function bedable(): MorphTo
    {
        return $this->morphTo();
    }
}
