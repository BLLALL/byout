<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function home() {
        return $this->belongsTo(Home::class);
    }

    public function users() {
        return $this->belongsTo(User::class);
    }
    protected static function booted()
    {
        static::saved(function ($review) {

            $review->home->calcAvgRatingAndCount();

        });

        static::deleted(function ($review) {
           $review->home->calcAvgRatingAndCount();
        });
    }



}
