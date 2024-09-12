<?php

namespace App\Http\filters;

use Illuminate\Database\Eloquent\Builder;

class BusFilter extends QueryFilter
{
    public function seats_number($value)
    {
        return $this->builder->where('seat_number', $value);
    }

    public function owner_id($value)
    {
        return $this->builder->whereHas('owner', (function (Builder $query) use ($value) {
            $query->where('user_id', $value);
        }));
    }

}
