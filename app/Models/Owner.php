<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Rental;
class Owner extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function home()
    {
        return $this->hasMany(Home::class);
    }

    public function bus()
    {
        return $this->hasMany(Bus::class);
    }
    public function hotel()
    {
        return $this->hasMany(Hotel::class);
    }

    public function tour() {
        return $this->hasMany(Tour::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function driver() {
        return $this->hasMany(Driver::class);
    }

    public function rental() {
        return $this->hasMany(Rental::class);
    }
}
