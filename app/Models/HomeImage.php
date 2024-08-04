<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomeImage extends Model
{
    use HasFactory;

    protected $guarded = [];
    public function Home() {
        return $this->belongsTo(Home::class);
    }
}
