<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Log;

class Review extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
    protected static function booted()
    {
        static::saved(function ($review) {
            Log::info('Event saved triggered for Review ID:'. $review->id);
            $review->reviewable->updateReviewStatistics();
        });

        static::deleted(function ($review) {
            Log::info('Event deleted triggered for Review ID:'. $review->id);
            $review->reviewable->updateReviewStatistics();
        });
    }



}
