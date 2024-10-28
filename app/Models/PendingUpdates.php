<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingUpdates extends Model
{
    /** @use HasFactory<\Database\Factories\PendingUpdatesFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'changes' => 'array'
    ];

    public function updatable()
    {
        return $this->morphTo();
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function replace_existing(array $newChanges)
    {
        $this->changes = $newChanges;
        $this->save();
    }
    

}
