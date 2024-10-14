<?php

namespace App\Http\filters;

use Illuminate\Database\Eloquent\Builder;

class HotelFilter extends QueryFilter
{

    public function description($value)
    {
        return $this->builder->where('description', 'LIKE', '%' . $value . '%');

    }

    public function owner_id($value)
    {
        return $this->builder->whereHas('owner', (function (Builder $query) use ($value) {
            $query->where('user_id', $value);
        }));
    }

    public function pending($value) {
        return $this->builder->where('pending',  $value );
    }
}
