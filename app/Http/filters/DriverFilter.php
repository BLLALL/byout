<?php

namespace App\Http\filters;

use Illuminate\Database\Eloquent\Builder;

class DriverFilter extends QueryFilter
{
    public function owner_id($value)
    {
        return $this->builder->whereHas('owner', (function (Builder $query) use ($value) {
            $query->where('user_id', $value);
        }));
    }
}
